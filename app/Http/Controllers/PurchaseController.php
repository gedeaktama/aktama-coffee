<?php

namespace App\Http\Controllers;

use App\Models\ProductVariant;
use App\Models\Purchase;
use App\Models\PurchaseItem;
use App\Models\StockMovement;
use App\Models\Supplier;
use App\Services\InventoryService;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class PurchaseController extends Controller
{
    protected InventoryService $inventoryService;

    public function __construct(InventoryService $inventoryService)
    {
        $this->inventoryService = $inventoryService;
    }

    public function index(Request $request)
    {
        $query = Purchase::with(['supplier', 'user', 'receivedBy'])->latest();

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('supplier_id')) {
            $query->where('supplier_id', $request->supplier_id);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('purchase_number', 'like', "%{$search}%")
                  ->orWhereHas('supplier', function ($sq) use ($search) {
                      $sq->where('name', 'like', "%{$search}%");
                  });
            });
        }

        $purchases = $query->paginate(10)->withQueryString();
        $suppliers = Supplier::where('is_active', true)->get();

        return view('inventory.purchases.index', compact('purchases', 'suppliers'));
    }

    public function create()
    {
        $suppliers = Supplier::where('is_active', true)->get();
        $variants = ProductVariant::with('product')->where('is_active', true)->get();

        // Generate next PO number: PO-YYYYMM-0001
        $prefix = 'PO-' . date('Ym') . '-';
        $latest = Purchase::where('purchase_number', 'like', "{$prefix}%")->latest('id')->first();
        $nextNumber = 1;
        if ($latest) {
            $nextNumber = (int)substr($latest->purchase_number, -4) + 1;
        }
        $autoPoNumber = $prefix . str_pad($nextNumber, 4, '0', STR_PAD_LEFT);

        $variantsJson = $variants->map(function ($v) {
            return [
                'id' => $v->id,
                'sku' => $v->sku,
                'name' => $v->product->name . ' - ' . $v->variant_name . ' (' . $v->package_weight_grams . 'g)',
                'cost_price' => (float)$v->cost_price,
                'current_stock' => $v->current_stock,
            ];
        });

        return view('inventory.purchases.create', compact('suppliers', 'variants', 'variantsJson', 'autoPoNumber'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'purchase_number' => ['required', 'string', 'max:50', 'unique:purchases,purchase_number'],
            'supplier_id' => ['required', 'exists:suppliers,id'],
            'purchase_date' => ['required', 'date'],
            'discount' => ['nullable', 'numeric', 'min:0'],
            'shipping_cost' => ['nullable', 'numeric', 'min:0'],
            'notes' => ['nullable', 'string'],
            'items' => ['required', 'array', 'min:1'],
            'items.*.product_variant_id' => ['required', 'exists:product_variants,id'],
            'items.*.quantity' => ['required', 'integer', 'min:1'],
            'items.*.cost_price' => ['required', 'numeric', 'min:0'],
        ], [
            'purchase_number.unique' => 'Nomor PO ini sudah pernah digunakan.',
            'items.required' => 'Minimal harus memilih satu item produk untuk dipesan.',
        ]);

        $discount = (float)($validated['discount'] ?? 0);
        $shippingCost = (float)($validated['shipping_cost'] ?? 0);

        DB::transaction(function () use ($validated, $discount, $shippingCost) {
            $subtotal = 0;
            $itemsData = [];

            foreach ($validated['items'] as $item) {
                $qty = (int)$item['quantity'];
                $price = (float)$item['cost_price'];
                $lineSubtotal = $qty * $price;
                $subtotal += $lineSubtotal;

                $itemsData[] = [
                    'product_variant_id' => $item['product_variant_id'],
                    'quantity' => $qty,
                    'cost_price' => $price,
                    'subtotal' => $lineSubtotal,
                ];
            }

            $totalAmount = max(0, $subtotal - $discount + $shippingCost);

            $purchase = Purchase::create([
                'purchase_number' => strtoupper(trim($validated['purchase_number'])),
                'supplier_id' => $validated['supplier_id'],
                'user_id' => Auth::id(),
                'purchase_date' => $validated['purchase_date'],
                'status' => Purchase::STATUS_ORDERED, // Langsung status 'ordered'
                'subtotal' => $subtotal,
                'discount' => $discount,
                'shipping_cost' => $shippingCost,
                'total_amount' => $totalAmount,
                'notes' => $validated['notes'] ?? null,
            ]);

            foreach ($itemsData as $row) {
                $purchase->items()->create($row);
            }
        });

        return redirect()->route('purchases.index')->with('success', 'Pesanan Pembelian (PO) berhasil diterbitkan ke supplier.');
    }

    public function show(Purchase $purchase)
    {
        $purchase->load(['supplier', 'user', 'receivedBy', 'items.variant.product']);
        return view('inventory.purchases.show', compact('purchase'));
    }

    /**
     * Confirm physical receipt of goods from supplier.
     * Strictly increases inventory stock only upon this confirmation.
     */
    public function receive(Request $request, Purchase $purchase)
    {
        if ($purchase->status !== Purchase::STATUS_ORDERED) {
            return back()->with('error', 'Pesanan ini tidak dapat dikonfirmasi penerimaannya (Status: ' . $purchase->status_label . ').');
        }

        try {
            DB::transaction(function () use ($purchase) {
                // Increase stock for each purchase item with audit trail
                foreach ($purchase->items as $item) {
                    $this->inventoryService->addStock(
                        variant: $item->product_variant_id,
                        quantity: $item->quantity,
                        referenceType: StockMovement::REF_PURCHASE,
                        referenceId: $purchase->id,
                        notes: "Penerimaan PO #{$purchase->purchase_number} dari Supplier {$purchase->supplier->name}",
                        userId: Auth::id()
                    );
                }

                $purchase->update([
                    'status' => Purchase::STATUS_RECEIVED,
                    'received_at' => now(),
                    'received_by_user_id' => Auth::id(),
                ]);
            });

            return redirect()->route('purchases.show', $purchase)->with('success', "Barang fisik untuk PO #{$purchase->purchase_number} berhasil dikonfirmasi diterima. Saldo stok telah bertambah secara otomatis.");
        } catch (Exception $e) {
            return back()->with('error', 'Gagal memproses penerimaan barang: ' . $e->getMessage());
        }
    }

    public function cancel(Purchase $purchase)
    {
        if ($purchase->status === Purchase::STATUS_RECEIVED) {
            return back()->with('error', 'Pesanan yang sudah diterima fisik tidak dapat dibatalkan.');
        }

        $purchase->update(['status' => Purchase::STATUS_CANCELLED]);

        return redirect()->route('purchases.show', $purchase)->with('success', "Pesanan PO #{$purchase->purchase_number} berhasil dibatalkan.");
    }
}
