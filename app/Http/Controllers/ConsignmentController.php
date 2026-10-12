<?php

namespace App\Http\Controllers;

use App\Models\Consignment;
use App\Models\ConsignmentAudit;
use App\Models\ConsignmentAuditItem;
use App\Models\ConsignmentItem;
use App\Models\Customer;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\StockMovement;
use App\Models\User;
use App\Services\InventoryService;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ConsignmentController extends Controller
{
    protected InventoryService $inventoryService;

    public function __construct(InventoryService $inventoryService)
    {
        $this->inventoryService = $inventoryService;
    }

    /**
     * Daftar Toko & Posisi Stok Titipan Konsinyasi
     */
    public function index(Request $request)
    {
        $query = Consignment::with(['customer', 'salesUser', 'items.variant.product'])->latest('delivery_date');

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('customer_id')) {
            $query->where('customer_id', $request->customer_id);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('consignment_number', 'like', "%{$search}%")
                  ->orWhereHas('customer', function ($cq) use ($search) {
                      $cq->where('business_name', 'like', "%{$search}%")
                         ->orWhere('pic_name', 'like', "%{$search}%");
                  });
            });
        }

        $consignments = $query->paginate(12)->withQueryString();

        // Statistik Ringkasan Konsinyasi
        $totalActiveStores = Consignment::where('status', Consignment::STATUS_ACTIVE)->distinct('customer_id')->count('customer_id');
        $totalItemsInStores = ConsignmentItem::whereHas('consignment', function ($q) {
            $q->where('status', Consignment::STATUS_ACTIVE);
        })->sum('current_quantity');
        
        $totalStockValue = ConsignmentItem::whereHas('consignment', function ($q) {
            $q->where('status', Consignment::STATUS_ACTIVE);
        })->select(DB::raw('SUM(current_quantity * selling_price) as val'))->value('val') ?? 0;

        $totalRevenueFromSold = ConsignmentAudit::where('payment_status', ConsignmentAudit::PAYMENT_STATUS_PAID)->sum('total_sold_amount');

        $customers = Customer::where('is_active', true)->orderBy('business_name')->get();

        return view('consignments.index', compact(
            'consignments',
            'totalActiveStores',
            'totalItemsInStores',
            'totalStockValue',
            'totalRevenueFromSold',
            'customers'
        ));
    }

    /**
     * Form Kirim Titip Barang Baru ke Toko Mitra
     */
    public function create()
    {
        $customers = Customer::where('is_active', true)->orderBy('business_name')->get();
        $salesUsers = User::whereIn('role', [User::ROLE_SALES, User::ROLE_ADMIN, User::ROLE_OWNER])->get();
        $products = Product::with(['variants' => function ($vq) {
            $vq->where('is_active', true);
        }])->where('is_active', true)->get();

        return view('consignments.create', compact('customers', 'salesUsers', 'products'));
    }

    /**
     * Simpan Pengiriman Titip Barang Baru
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'customer_id' => ['required', 'exists:customers,id'],
            'sales_user_id' => ['nullable', 'exists:users,id'],
            'delivery_date' => ['required', 'date'],
            'notes' => ['nullable', 'string', 'max:500'],
            'items' => ['required', 'array', 'min:1'],
            'items.*.variant_id' => ['required', 'exists:product_variants,id'],
            'items.*.quantity' => ['required', 'integer', 'min:1'],
            'items.*.price' => ['required', 'numeric', 'min:0'],
        ]);

        try {
            $consignment = DB::transaction(function () use ($validated) {
                // 1. Cek stok gudang mencukupi
                foreach ($validated['items'] as $itemData) {
                    $variant = ProductVariant::findOrFail($itemData['variant_id']);
                    if ($variant->current_stock < $itemData['quantity']) {
                        throw new Exception("Stok gudang tidak cukup untuk SKU {$variant->sku} ({$variant->variant_name}). Tersedia: {$variant->current_stock} pack.");
                    }
                }

                $customer = Customer::findOrFail($validated['customer_id']);

                // 2. Buat header penitipan
                $consignment = Consignment::create([
                    'consignment_number' => Consignment::generateNumber(),
                    'customer_id' => $customer->id,
                    'sales_user_id' => $validated['sales_user_id'] ?? $customer->sales_user_id ?? Auth::id(),
                    'created_by_user_id' => Auth::id(),
                    'delivery_date' => $validated['delivery_date'],
                    'status' => Consignment::STATUS_ACTIVE,
                    'total_items_count' => count($validated['items']),
                    'notes' => $validated['notes'],
                ]);

                // 3. Simpan item dan potong stok gudang
                foreach ($validated['items'] as $itemData) {
                    $variant = ProductVariant::findOrFail($itemData['variant_id']);
                    $qty = (int) $itemData['quantity'];
                    $price = (float) $itemData['price'];

                    $consignment->items()->create([
                        'product_variant_id' => $variant->id,
                        'initial_quantity' => $qty,
                        'current_quantity' => $qty,
                        'sold_quantity' => 0,
                        'returned_quantity' => 0,
                        'selling_price' => $price,
                    ]);

                    // Potong stok gudang utama
                    $this->inventoryService->reduceStock(
                        $variant,
                        $qty,
                        'consignment_out',
                        $consignment->id,
                        "Titip konsinyasi ke {$customer->business_name} (#{$consignment->consignment_number})"
                    );
                }

                return $consignment;
            });

            return redirect()->route('consignments.show', $consignment)
                ->with('success', "Penitipan konsinyasi {$consignment->consignment_number} berhasil dibuat dan stok fisik gudang telah dialokasikan.");
        } catch (Exception $e) {
            return back()->withInput()->with('error', $e->getMessage());
        }
    }

    /**
     * Detail Stok Titipan Toko & Riwayat Survei
     */
    public function show(Consignment $consignment)
    {
        $consignment->load([
            'customer',
            'salesUser',
            'createdByUser',
            'items.variant.product',
            'audits.user',
            'audits.items.variant.product'
        ]);

        return view('consignments.show', compact('consignment'));
    }

    /**
     * Cetak Surat Titip Barang Resmi (Surat Jalan Konsinyasi)
     */
    public function print(Consignment $consignment)
    {
        $consignment->load(['customer', 'salesUser', 'items.variant.product']);
        return view('consignments.print', compact('consignment'));
    }

    /**
     * Formulir Survei Mingguan & Penagihan Produk Laku
     */
    public function createAudit(Consignment $consignment)
    {
        if ($consignment->status !== Consignment::STATUS_ACTIVE) {
            return redirect()->route('consignments.show', $consignment)
                ->with('error', 'Penitipan ini sudah tidak aktif atau selesai.');
        }

        $consignment->load(['customer', 'salesUser', 'items.variant.product']);
        return view('consignments.audit_create', compact('consignment'));
    }

    /**
     * Simpan Hasil Survei Mingguan, Hitung Laku, dan Proses Pembayaran
     */
    public function storeAudit(Request $request, Consignment $consignment)
    {
        $validated = $request->validate([
            'audit_date' => ['required', 'date'],
            'payment_method' => ['required', 'in:cash,transfer'],
            'payment_status' => ['required', 'in:paid,unpaid'],
            'notes' => ['nullable', 'string', 'max:500'],
            'items' => ['required', 'array'],
            'items.*.item_id' => ['required', 'exists:consignment_items,id'],
            'items.*.physical_remaining' => ['required', 'integer', 'min:0'],
            'items.*.quantity_returned' => ['nullable', 'integer', 'min:0'],
            'items.*.restock_quantity' => ['nullable', 'integer', 'min:0'],
        ]);

        try {
            $audit = DB::transaction(function () use ($validated, $consignment) {
                $totalSoldQty = 0;
                $totalSoldAmount = 0.0;
                $totalRestockQty = 0;
                $auditItemsData = [];
                $orderItemsToCreate = [];

                foreach ($validated['items'] as $inputItem) {
                    $cItem = ConsignmentItem::with('variant')->findOrFail($inputItem['item_id']);
                    $stockBefore = $cItem->current_quantity;
                    $physicalRemaining = (int) $inputItem['physical_remaining'];
                    $returnedQty = (int) ($inputItem['quantity_returned'] ?? 0);
                    $restockQty = (int) ($inputItem['restock_quantity'] ?? 0);

                    if ($physicalRemaining + $returnedQty > $stockBefore) {
                        throw new Exception("Sisa fisik ({$physicalRemaining}) + retur ({$returnedQty}) untuk SKU {$cItem->variant->sku} melebihi stok tercatat ({$stockBefore}). Periksa kembali hitungan fisik.");
                    }

                    // Hitung produk laku
                    $soldQty = $stockBefore - $physicalRemaining - $returnedQty;
                    $price = (float) $cItem->selling_price;
                    $subtotal = $soldQty * $price;

                    // Cek jika ada restock baru, apakah stok gudang cukup
                    if ($restockQty > 0) {
                        if ($cItem->variant->current_stock < $restockQty) {
                            throw new Exception("Stok gudang tidak cukup untuk restock SKU {$cItem->variant->sku}. Tersedia di gudang: {$cItem->variant->current_stock} pack.");
                        }
                    }

                    $stockAfter = $physicalRemaining + $restockQty;

                    $totalSoldQty += $soldQty;
                    $totalSoldAmount += $subtotal;
                    $totalRestockQty += $restockQty;

                    $auditItemsData[] = [
                        'consignment_item_id' => $cItem->id,
                        'product_variant_id' => $cItem->product_variant_id,
                        'stock_before' => $stockBefore,
                        'physical_remaining' => $physicalRemaining,
                        'quantity_sold' => $soldQty,
                        'quantity_returned' => $returnedQty,
                        'restock_quantity' => $restockQty,
                        'stock_after' => $stockAfter,
                        'price_per_unit' => $price,
                        'subtotal' => $subtotal,
                        'cItemModel' => $cItem,
                    ];

                    if ($soldQty > 0) {
                        $orderItemsToCreate[] = [
                            'product_variant_id' => $cItem->product_variant_id,
                            'quantity' => $soldQty,
                            'cost_price' => $cItem->variant->cost_price,
                            'selling_price' => $price,
                            'subtotal' => $subtotal,
                        ];
                    }
                }

                // Buat Sales Order resmi otomatis untuk penjualan laku konsinyasi
                $orderId = null;
                if ($totalSoldAmount > 0) {
                    $orderNumber = 'SO-KNS-' . date('Ym') . '-' . str_pad(Order::count() + 1, 4, '0', STR_PAD_LEFT);
                    $order = Order::create([
                        'order_number' => $orderNumber,
                        'order_type' => Order::TYPE_CONSIGNMENT,
                        'customer_id' => $consignment->customer_id,
                        'user_id' => Auth::id(),
                        'sales_user_id' => $consignment->sales_user_id ?? Auth::id(),
                        'order_date' => $validated['audit_date'],
                        'payment_type' => $validated['payment_method'],
                        'status' => Order::STATUS_DELIVERED,
                        'payment_status' => $validated['payment_status'] === 'paid' ? Order::PAYMENT_STATUS_PAID : Order::PAYMENT_STATUS_UNPAID,
                        'subtotal' => $totalSoldAmount,
                        'discount' => 0,
                        'shipping_cost' => 0,
                        'total_amount' => $totalSoldAmount,
                        'paid_amount' => $validated['payment_status'] === 'paid' ? $totalSoldAmount : 0,
                        'stock_reduced' => true, // Stok sudah berkurang saat awal konsinyasi
                        'confirmed_at' => now(),
                        'confirmed_by_user_id' => Auth::id(),
                        'notes' => "Penjualan laku konsinyasi Toko {$consignment->customer->business_name} (#{$consignment->consignment_number})",
                    ]);

                    foreach ($orderItemsToCreate as $oi) {
                        $order->items()->create($oi);
                    }

                    $orderId = $order->id;
                }

                // Buat Header Audit
                $audit = ConsignmentAudit::create([
                    'audit_number' => ConsignmentAudit::generateNumber(),
                    'consignment_id' => $consignment->id,
                    'customer_id' => $consignment->customer_id,
                    'user_id' => Auth::id(),
                    'audit_date' => $validated['audit_date'],
                    'total_sold_qty' => $totalSoldQty,
                    'total_sold_amount' => $totalSoldAmount,
                    'total_restock_qty' => $totalRestockQty,
                    'payment_status' => $validated['payment_status'],
                    'payment_method' => $validated['payment_method'],
                    'order_id' => $orderId,
                    'notes' => $validated['notes'],
                ]);

                // Simpan rincian audit dan update stok konsinyasi toko
                foreach ($auditItemsData as $d) {
                    $audit->items()->create([
                        'consignment_item_id' => $d['consignment_item_id'],
                        'product_variant_id' => $d['product_variant_id'],
                        'stock_before' => $d['stock_before'],
                        'physical_remaining' => $d['physical_remaining'],
                        'quantity_sold' => $d['quantity_sold'],
                        'quantity_returned' => $d['quantity_returned'],
                        'restock_quantity' => $d['restock_quantity'],
                        'stock_after' => $d['stock_after'],
                        'price_per_unit' => $d['price_per_unit'],
                        'subtotal' => $d['subtotal'],
                    ]);

                    // Update konsinyasi item toko
                    $cItem = $d['cItemModel'];
                    $cItem->update([
                        'current_quantity' => $d['stock_after'],
                        'sold_quantity' => $cItem->sold_quantity + $d['quantity_sold'],
                        'returned_quantity' => $cItem->returned_quantity + $d['quantity_returned'],
                    ]);

                    // Jika ada restock baru: potong stok dari gudang utama
                    if ($d['restock_quantity'] > 0) {
                        $this->inventoryService->reduceStock(
                            $cItem->variant,
                            $d['restock_quantity'],
                            'consignment_restock',
                            $audit->id,
                            "Restock konsinyasi Toko {$consignment->customer->business_name} (#{$audit->audit_number})"
                        );
                    }

                    // Jika ada barang retur: masukkan kembali ke stok gudang
                    if ($d['quantity_returned'] > 0) {
                        $this->inventoryService->addStock(
                            $cItem->variant,
                            $d['quantity_returned'],
                            'consignment_return',
                            $audit->id,
                            "Retur barang konsinyasi dari Toko {$consignment->customer->business_name} (#{$audit->audit_number})"
                        );
                    }
                }

                return $audit;
            });

            return redirect()->route('consignments.audits.show', $audit)
                ->with('success', "Survei mingguan {$audit->audit_number} berhasil dicatat. Terjual {$audit->total_sold_qty} pack senilai {$audit->formatted_total_sold_amount}.");
        } catch (Exception $e) {
            return back()->withInput()->with('error', $e->getMessage());
        }
    }

    /**
     * Riwayat Seluruh Survei & Penagihan Konsinyasi
     */
    public function auditsIndex(Request $request)
    {
        $query = ConsignmentAudit::with(['consignment', 'customer', 'user'])->latest('audit_date');

        if ($request->filled('customer_id')) {
            $query->where('customer_id', $request->customer_id);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('audit_number', 'like', "%{$search}%")
                  ->orWhereHas('customer', function ($cq) use ($search) {
                      $cq->where('business_name', 'like', "%{$search}%");
                  });
            });
        }

        $audits = $query->paginate(15)->withQueryString();
        $customers = Customer::where('is_active', true)->orderBy('business_name')->get();

        return view('consignments.audits_index', compact('audits', 'customers'));
    }

    /**
     * Detail Nota Hasil Survei & Kuitansi Penyetoran
     */
    public function showAudit(ConsignmentAudit $audit)
    {
        $audit->load(['consignment', 'customer', 'user', 'order', 'items.variant.product']);
        return view('consignments.audit_show', compact('audit'));
    }

    /**
     * Cetak Nota Hasil Survei & Bukti Setor
     */
    public function printAudit(ConsignmentAudit $audit)
    {
        $audit->load(['consignment', 'customer', 'user', 'order', 'items.variant.product']);
        return view('consignments.audit_print', compact('audit'));
    }
}
