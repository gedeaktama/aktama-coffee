<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\ProductVariant;
use App\Models\StockMovement;
use App\Services\InventoryService;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class OrderController extends Controller
{
    protected InventoryService $inventoryService;

    public function __construct(InventoryService $inventoryService)
    {
        $this->inventoryService = $inventoryService;
    }

    public function index(Request $request)
    {
        $query = Order::with(['customer', 'salesUser', 'user', 'items.variant.product'])->latest();

        if ($request->filled('order_type')) {
            $query->where('order_type', $request->order_type);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('payment_status')) {
            $query->where('payment_status', $request->payment_status);
        }

        if ($request->filled('payment_type')) {
            $query->where('payment_type', $request->payment_type);
        }

        if ($request->filled('customer_id')) {
            $query->where('customer_id', $request->customer_id);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('order_number', 'like', "%{$search}%")
                  ->orWhere('recipient_name', 'like', "%{$search}%")
                  ->orWhereHas('customer', function ($cq) use ($search) {
                      $cq->where('business_name', 'like', "%{$search}%")
                         ->orWhere('pic_name', 'like', "%{$search}%");
                  });
            });
        }

        // Filter for Sales role: can see all or specifically assigned
        $user = Auth::user();
        if ($user->isSales() && !$request->has('all')) {
            $query->where(function ($sq) use ($user) {
                $sq->where('sales_user_id', $user->id)
                   ->orWhere('user_id', $user->id);
            });
        }

        $orders = $query->paginate(15)->withQueryString();
        $customers = Customer::where('is_active', true)->get();

        $metrics = [
            'total_orders' => Order::count(),
            'pending_count' => Order::where('status', Order::STATUS_PENDING)->count(),
            'confirmed_count' => Order::where('status', Order::STATUS_CONFIRMED)->count(),
            'total_sales' => Order::where('status', '!=', Order::STATUS_CANCELLED)->sum('total_amount'),
        ];

        return view('orders.index', compact('orders', 'customers', 'metrics'));
    }

    public function create()
    {
        $user = Auth::user();
        $customersQuery = Customer::whereIn('customer_type', [Customer::TYPE_KAFE, Customer::TYPE_TOKO, Customer::TYPE_RESELLER])
            ->where('is_active', true);

        if ($user->isSales()) {
            $customers = (clone $customersQuery)->where(function ($q) use ($user) {
                $q->where('sales_user_id', $user->id)->orWhereNull('sales_user_id');
            })->get();
        } else {
            $customers = $customersQuery->get();
        }

        $variants = ProductVariant::with('product')
            ->where('is_active', true)
            ->get();

        // Generate SO number: SO-YYYYMM-0001
        $prefix = 'SO-' . date('Ym') . '-';
        $latest = Order::where('order_number', 'like', "{$prefix}%")->latest('id')->first();
        $nextNumber = 1;
        if ($latest) {
            $nextNumber = (int)substr($latest->order_number, -4) + 1;
        }
        $autoOrderNumber = $prefix . str_pad($nextNumber, 4, '0', STR_PAD_LEFT);

        $variantsJson = $variants->map(function ($v) {
            return [
                'id' => $v->id,
                'sku' => $v->sku,
                'name' => $v->product->name . ' - ' . $v->variant_name . ' (' . $v->package_weight_grams . 'g)',
                'price' => (float)$v->selling_price,
                'price_toko' => (float)($v->price_toko ?? $v->selling_price),
                'price_kafe' => (float)($v->price_kafe ?? $v->price_toko ?? $v->selling_price),
                'tier_pricing' => $v->tier_pricing,
                'current_stock' => $v->current_stock,
            ];
        });

        return view('orders.create', compact('customers', 'variants', 'variantsJson', 'autoOrderNumber'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'order_number' => ['required', 'string', 'max:50', 'unique:orders,order_number'],
            'customer_id' => ['required', 'exists:customers,id'],
            'order_date' => ['required', 'date'],
            'payment_type' => ['required', 'in:cash,transfer,credit'],
            'discount' => ['nullable', 'numeric', 'min:0'],
            'shipping_cost' => ['nullable', 'numeric', 'min:0'],
            'shipping_address' => ['nullable', 'string'],
            'recipient_name' => ['nullable', 'string', 'max:100'],
            'recipient_phone' => ['nullable', 'string', 'max:30'],
            'notes' => ['nullable', 'string'],
            'items' => ['required', 'array', 'min:1'],
            'items.*.product_variant_id' => ['required', 'exists:product_variants,id'],
            'items.*.quantity' => ['required', 'integer', 'min:1'],
        ], [
            'order_number.unique' => 'Nomor Pesanan ini sudah pernah digunakan.',
            'items.required' => 'Minimal harus memilih satu item varian produk.',
        ]);

        $customer = Customer::findOrFail($validated['customer_id']);
        $discount = (float)($validated['discount'] ?? 0);
        $shippingCost = (float)($validated['shipping_cost'] ?? 0);

        // Calculate subtotal & check credit terms
        $subtotal = 0;
        $itemsData = [];

        foreach ($validated['items'] as $item) {
            $variant = ProductVariant::findOrFail($item['product_variant_id']);
            $qty = (int)$item['quantity'];
            $unitPrice = (float)$variant->getPriceFor($customer->customer_type, $qty);
            $lineSubtotal = $qty * $unitPrice;
            $subtotal += $lineSubtotal;

            $itemsData[] = [
                'variant' => $variant,
                'quantity' => $qty,
                'unit_price' => $unitPrice,
                'cost_price' => (float)$variant->cost_price,
                'subtotal' => $lineSubtotal,
            ];
        }

        $totalAmount = max(0, $subtotal - $discount + $shippingCost);

        // Credit Limit & Payment Terms validation
        $dueDate = null;
        if ($validated['payment_type'] === Order::PAYMENT_CREDIT) {
            if ($customer->payment_terms_days <= 0) {
                return back()->withInput()->with('error', "Pelanggan {$customer->business_name} tidak memiliki fasilitas termin tempo kredit (TOP: 0 hari). Silakan pilih pembayaran Tunai atau Transfer.");
            }

            $currentReceivables = $customer->getCurrentReceivables();
            $projectedTotal = $currentReceivables + $totalAmount;

            if ($projectedTotal > (float)$customer->credit_limit) {
                $user = Auth::user();
                if (!$user->isOwner() && !$user->isAdmin()) {
                    return back()->withInput()->with('error', sprintf(
                        "Plafon kredit tidak mencukupi! Plafon: Rp %s, Piutang Berjalan: Rp %s, Pesanan Ini: Rp %s. Total Rp %s melebihi batas. Diperlukan persetujuan Admin/Owner.",
                        number_format($customer->credit_limit, 0, ',', '.'),
                        number_format($currentReceivables, 0, ',', '.'),
                        number_format($totalAmount, 0, ',', '.'),
                        number_format($projectedTotal, 0, ',', '.')
                    ));
                }
            }

            $dueDate = Carbon::parse($validated['order_date'])->addDays($customer->payment_terms_days)->toDateString();
        }

        $order = DB::transaction(function () use ($validated, $customer, $subtotal, $discount, $shippingCost, $totalAmount, $dueDate, $itemsData) {
            $createdOrder = Order::create([
                'order_number' => strtoupper(trim($validated['order_number'])),
                'order_type' => Order::TYPE_B2B,
                'customer_id' => $customer->id,
                'user_id' => Auth::id(),
                'sales_user_id' => $customer->sales_user_id ?? Auth::id(),
                'order_date' => $validated['order_date'],
                'payment_type' => $validated['payment_type'],
                'due_date' => $dueDate,
                'status' => Order::STATUS_PENDING,
                'payment_status' => Order::PAYMENT_STATUS_UNPAID,
                'subtotal' => $subtotal,
                'discount' => $discount,
                'shipping_cost' => $shippingCost,
                'total_amount' => $totalAmount,
                'paid_amount' => 0,
                'shipping_address' => $validated['shipping_address'] ?? $customer->shipping_address ?? $customer->address,
                'recipient_name' => $validated['recipient_name'] ?? $customer->pic_name,
                'recipient_phone' => $validated['recipient_phone'] ?? $customer->phone,
                'stock_reduced' => false,
                'notes' => $validated['notes'] ?? null,
            ]);

            foreach ($itemsData as $row) {
                $createdOrder->items()->create([
                    'product_variant_id' => $row['variant']->id,
                    'quantity' => $row['quantity'],
                    'unit_price' => $row['unit_price'],
                    'cost_price' => $row['cost_price'],
                    'discount_amount' => 0,
                    'subtotal' => $row['subtotal'],
                ]);
            }

            return $createdOrder;
        });

        return redirect()->route('orders.show', $order)->with('success', "Pesanan Penjualan Grosir #{$order->order_number} berhasil diterbitkan.");
    }

    public function show(Order $order)
    {
        $order->load(['customer', 'salesUser', 'user', 'confirmedBy', 'items.variant.product']);
        return view('orders.show', compact('order'));
    }

    /**
     * Konfirmasi pesanan penjualan & potong stok secara otomatis
     */
    public function confirm(Order $order)
    {
        if (!$order->canBeConfirmed()) {
            return back()->with('error', "Pesanan #{$order->order_number} tidak dapat dikonfirmasi (Status: {$order->status_label}).");
        }

        try {
            DB::transaction(function () use ($order) {
                // Reduce inventory stock for each order item with audit trail
                foreach ($order->items as $item) {
                    $this->inventoryService->reduceStock(
                        variant: $item->product_variant_id,
                        quantity: $item->quantity,
                        referenceType: StockMovement::REF_SALES_ORDER,
                        referenceId: $order->id,
                        notes: "Penjualan pesanan #{$order->order_number} kepada " . ($order->customer->business_name ?? $order->recipient_name),
                        userId: Auth::id()
                    );
                }

                $order->update([
                    'status' => Order::STATUS_CONFIRMED,
                    'stock_reduced' => true,
                    'confirmed_at' => now(),
                    'confirmed_by_user_id' => Auth::id(),
                ]);
            });

            return redirect()->route('orders.show', $order)->with('success', "Pesanan #{$order->order_number} berhasil dikonfirmasi. Stok fisik di gudang telah dipotong secara otomatis.");
        } catch (Exception $e) {
            return back()->with('error', 'Gagal mengonfirmasi pesanan: ' . $e->getMessage());
        }
    }

    /**
     * Batalkan pesanan & kembalikan stok jika sebelumnya sudah dipotong
     */
    public function cancel(Order $order)
    {
        if (!$order->canBeCancelled()) {
            return back()->with('error', "Pesanan #{$order->order_number} tidak dapat dibatalkan.");
        }

        try {
            DB::transaction(function () use ($order) {
                if ($order->stock_reduced) {
                    foreach ($order->items as $item) {
                        $this->inventoryService->addStock(
                            variant: $item->product_variant_id,
                            quantity: $item->quantity,
                            referenceType: 'sales_order_cancellation',
                            referenceId: $order->id,
                            notes: "Pengembalian stok akibat pembatalan pesanan #{$order->order_number}",
                            userId: Auth::id()
                        );
                    }
                }

                $order->update([
                    'status' => Order::STATUS_CANCELLED,
                    'stock_reduced' => false,
                ]);
            });

            return redirect()->route('orders.show', $order)->with('success', "Pesanan #{$order->order_number} berhasil dibatalkan dan stok telah dikembalikan ke persediaan gudang.");
        } catch (Exception $e) {
            return back()->with('error', 'Gagal membatalkan pesanan: ' . $e->getMessage());
        }
    }
}
