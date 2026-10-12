<?php

namespace App\Http\Controllers;

use App\Models\AppSetting;
use App\Models\Category;
use App\Models\Customer;
use App\Models\Order;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\StockMovement;
use App\Services\InventoryService;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class StoreController extends Controller
{
    protected InventoryService $inventoryService;

    public function __construct(InventoryService $inventoryService)
    {
        $this->inventoryService = $inventoryService;
    }

    /**
     * Katalog Kopi Retail Online
     */
    public function catalog(Request $request)
    {
        $query = Product::with(['category', 'variants' => function ($vq) {
            $vq->where('is_active', true);
        }])->where('is_active', true);

        if ($request->filled('category_id')) {
            $query->where('category_id', $request->category_id);
        }

        if ($request->filled('coffee_type')) {
            $query->where('coffee_type', $request->coffee_type);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('brand', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }

        $products = $query->paginate(9)->withQueryString();
        $categories = Category::where('is_active', true)->get();

        return view('store.catalog', compact('products', 'categories'));
    }

    /**
     * Halaman Detail Produk & Foto Kemasan Lengkap
     */
    public function productDetail(string $slug)
    {
        $product = Product::with(['category', 'variants' => function ($vq) {
            $vq->where('is_active', true);
        }])->where('slug', $slug)->where('is_active', true)->firstOrFail();

        $relatedProducts = Product::where('id', '!=', $product->id)
            ->where('is_active', true)
            ->take(3)
            ->get();

        return view('store.product_detail', compact('product', 'relatedProducts'));
    }

    /**
     * Tampilan Keranjang Belanja
     */
    public function cart()
    {
        $cart = session()->get('cart', []);
        $total = 0;

        foreach ($cart as $item) {
            $total += $item['price'] * $item['quantity'];
        }

        return view('store.cart', compact('cart', 'total'));
    }

    /**
     * Tambah varian kopi ke keranjang
     */
    public function addToCart(Request $request)
    {
        if (AppSetting::isDevMode()) {
            return back()->with('error', 'Mode Pengembang sedang aktif. Seluruh fitur pembelian dan keranjang belanja dinonaktifkan sementara.');
        }

        $validated = $request->validate([
            'product_variant_id' => ['required', 'exists:product_variants,id'],
            'quantity' => ['required', 'integer', 'min:1'],
        ]);

        $variant = ProductVariant::with('product')->findOrFail($validated['product_variant_id']);
        $quantity = (int)$validated['quantity'];

        if ($variant->current_stock < $quantity) {
            return back()->with('error', "Stok tidak mencukupi untuk SKU {$variant->sku}. Tersedia: {$variant->current_stock} pack.");
        }

        $cart = session()->get('cart', []);

        if (isset($cart[$variant->id])) {
            $newQty = $cart[$variant->id]['quantity'] + $quantity;
            if ($variant->current_stock < $newQty) {
                return back()->with('error', "Total kuantitas di keranjang ({$newQty}) melebihi stok tersedia ({$variant->current_stock} pack).");
            }
            $cart[$variant->id]['quantity'] = $newQty;
        } else {
            $cart[$variant->id] = [
                'variant_id' => $variant->id,
                'sku' => $variant->sku,
                'name' => $variant->product->name . ' - ' . $variant->variant_name,
                'weight' => $variant->package_weight_grams,
                'grind_form' => $variant->grind_form,
                'price' => (float)$variant->selling_price,
                'quantity' => $quantity,
                'image' => $variant->product->image_url,
            ];
        }

        session()->put('cart', $cart);

        return redirect()->route('store.cart')->with('success', "{$variant->product->name} ({$variant->variant_name}) berhasil ditambahkan ke keranjang belanja.");
    }

    /**
     * Perbarui jumlah item di keranjang
     */
    public function updateCart(Request $request)
    {
        $validated = $request->validate([
            'variant_id' => ['required', 'exists:product_variants,id'],
            'quantity' => ['required', 'integer', 'min:1'],
        ]);

        $cart = session()->get('cart', []);
        $variantId = $validated['variant_id'];

        if (isset($cart[$variantId])) {
            $variant = ProductVariant::findOrFail($variantId);
            $qty = (int)$validated['quantity'];

            if ($variant->current_stock < $qty) {
                return back()->with('error', "Stok tersedia hanya {$variant->current_stock} pack.");
            }

            $cart[$variantId]['quantity'] = $qty;
            session()->put('cart', $cart);
        }

        return redirect()->route('store.cart')->with('success', 'Keranjang belanja berhasil diperbarui.');
    }

    /**
     * Hapus item dari keranjang
     */
    public function removeFromCart($variantId)
    {
        $cart = session()->get('cart', []);

        if (isset($cart[$variantId])) {
            unset($cart[$variantId]);
            session()->put('cart', $cart);
        }

        return redirect()->route('store.cart')->with('success', 'Item berhasil dihapus dari keranjang.');
    }

    /**
     * Halaman Checkout Retail
     */
    public function checkout()
    {
        if (AppSetting::isDevMode()) {
            return redirect()->route('store.catalog')->with('error', 'Mode Pengembang sedang aktif. Halaman checkout dinonaktifkan sementara.');
        }

        $cart = session()->get('cart', []);

        if (empty($cart)) {
            return redirect()->route('store.catalog')->with('error', 'Keranjang belanja Anda masih kosong.');
        }

        $subtotal = 0;
        foreach ($cart as $item) {
            $subtotal += $item['price'] * $item['quantity'];
        }

        $user = Auth::user();
        $customer = null;
        if ($user) {
            $customer = Customer::where('user_id', $user->id)->first();
        }

        return view('store.checkout', compact('cart', 'subtotal', 'user', 'customer'));
    }

    /**
     * Proses Pesanan Checkout Retail
     */
    public function processCheckout(Request $request)
    {
        if (AppSetting::isDevMode()) {
            return redirect()->route('store.catalog')->with('error', 'Mode Pengembang sedang aktif. Transaksi pesanan dinonaktifkan sementara.');
        }

        $cart = session()->get('cart', []);

        if (empty($cart)) {
            return redirect()->route('store.catalog')->with('error', 'Keranjang belanja Anda kosong.');
        }

        $validated = $request->validate([
            'recipient_name' => ['required', 'string', 'max:100'],
            'recipient_phone' => ['required', 'string', 'max:30'],
            'shipping_address' => ['required', 'string'],
            'payment_type' => ['required', 'in:cash,transfer'],
            'notes' => ['nullable', 'string'],
        ], [
            'recipient_name.required' => 'Nama lengkap penerima pesanan wajib diisi.',
            'recipient_phone.required' => 'Nomor kontak / WhatsApp penerima wajib diisi.',
            'shipping_address.required' => 'Alamat lengkap pengiriman pesanan wajib diisi.',
        ]);

        // Verifikasi ketersediaan stok aktual
        foreach ($cart as $item) {
            $variant = ProductVariant::find($item['variant_id']);
            if (!$variant || $variant->current_stock < $item['quantity']) {
                $avail = $variant ? $variant->current_stock : 0;
                return redirect()->route('store.cart')->with('error', "Maaf, stok untuk {$item['name']} tidak mencukupi (Tersedia: {$avail} pack). Silakan sesuaikan jumlah pesanan Anda.");
            }
        }

        // Generate nomor order retail: ORD-YYYYMM-XXXX
        $prefix = 'ORD-' . date('Ym') . '-';
        $latest = Order::where('order_number', 'like', "{$prefix}%")->latest('id')->first();
        $nextNumber = 1;
        if ($latest) {
            $nextNumber = (int)substr($latest->order_number, -4) + 1;
        }
        $orderNumber = $prefix . str_pad($nextNumber, 4, '0', STR_PAD_LEFT);

        $user = Auth::user();

        // Cari atau buat customer retail
        $customer = null;
        if ($user) {
            $customer = Customer::where('user_id', $user->id)->first();
        }

        if (!$customer) {
            $custCode = 'CUST-RET-' . date('Ym') . rand(100, 999);
            $customer = Customer::create([
                'code' => $custCode,
                'customer_type' => Customer::TYPE_RETAIL,
                'business_name' => $validated['recipient_name'],
                'pic_name' => $validated['recipient_name'],
                'phone' => $validated['recipient_phone'],
                'email' => $user?->email,
                'address' => $validated['shipping_address'],
                'shipping_address' => $validated['shipping_address'],
                'user_id' => $user?->id,
                'credit_limit' => 0,
                'payment_terms_days' => 0,
            ]);
        }

        try {
            $order = DB::transaction(function () use ($validated, $cart, $orderNumber, $customer, $user) {
                $subtotal = 0;
                $itemsData = [];

                foreach ($cart as $item) {
                    $variant = ProductVariant::findOrFail($item['variant_id']);
                    $lineTotal = $item['quantity'] * $item['price'];
                    $subtotal += $lineTotal;

                    $itemsData[] = [
                        'variant' => $variant,
                        'quantity' => $item['quantity'],
                        'unit_price' => $item['price'],
                        'cost_price' => (float)$variant->cost_price,
                        'subtotal' => $lineTotal,
                    ];
                }

                $shippingCost = 15000; // Standar ongkir flat pengiriman lokal
                $totalAmount = $subtotal + $shippingCost;

                $order = Order::create([
                    'order_number' => $orderNumber,
                    'order_type' => Order::TYPE_RETAIL,
                    'customer_id' => $customer->id,
                    'user_id' => $user?->id,
                    'sales_user_id' => null,
                    'order_date' => now()->toDateString(),
                    'payment_type' => $validated['payment_type'],
                    'due_date' => null,
                    'status' => Order::STATUS_CONFIRMED, // Langsung confirmed untuk retail checkout
                    'payment_status' => Order::PAYMENT_STATUS_UNPAID,
                    'subtotal' => $subtotal,
                    'discount' => 0,
                    'shipping_cost' => $shippingCost,
                    'total_amount' => $totalAmount,
                    'paid_amount' => 0,
                    'shipping_address' => $validated['shipping_address'],
                    'recipient_name' => $validated['recipient_name'],
                    'recipient_phone' => $validated['recipient_phone'],
                    'stock_reduced' => true,
                    'confirmed_at' => now(),
                    'confirmed_by_user_id' => $user?->id,
                    'notes' => $validated['notes'] ?? null,
                ]);

                foreach ($itemsData as $row) {
                    $order->items()->create([
                        'product_variant_id' => $row['variant']->id,
                        'quantity' => $row['quantity'],
                        'unit_price' => $row['unit_price'],
                        'cost_price' => $row['cost_price'],
                        'discount_amount' => 0,
                        'subtotal' => $row['subtotal'],
                    ]);

                    // Potong stok persediaan otomatis untuk pesanan ritel
                    $this->inventoryService->reduceStock(
                        variant: $row['variant']->id,
                        quantity: $row['quantity'],
                        referenceType: StockMovement::REF_SALES_ORDER,
                        referenceId: $order->id,
                        notes: "Penjualan Retail Online #{$order->order_number} ({$order->recipient_name})",
                        userId: $user?->id
                    );
                }

                return $order;
            });

            // Kosongkan keranjang belanja
            session()->forget('cart');

            return view('store.success', compact('order'));
        } catch (Exception $e) {
            return back()->with('error', 'Gagal memproses pesanan: ' . $e->getMessage());
        }
    }

    /**
     * Riwayat pesanan untuk akun role customer
     */
    public function myOrders()
    {
        $user = Auth::user();
        $orders = Order::with('items.variant.product')
            ->where(function ($q) use ($user) {
                $q->where('user_id', $user->id)
                  ->orWhereHas('customer', function ($cq) use ($user) {
                      $cq->where('user_id', $user->id);
                  });
            })
            ->latest()
            ->paginate(10);

        return view('store.my_orders', compact('orders'));
    }
}
