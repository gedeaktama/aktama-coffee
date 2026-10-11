<?php

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\FinanceController;
use App\Http\Controllers\InventoryController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\PurchaseController;
use App\Http\Controllers\StoreController;
use App\Http\Controllers\SupplierController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes - Kopi Distro
|--------------------------------------------------------------------------
*/

// Public storefront route
Route::get('/', function () {
    return view('home');
})->name('home');

// Retail Storefront & Keranjang Belanja Online (B2C)
Route::get('/store', [StoreController::class, 'catalog'])->name('store.catalog');
Route::get('/cart', [StoreController::class, 'cart'])->name('store.cart');
Route::post('/cart/add', [StoreController::class, 'addToCart'])->name('store.cart.add');
Route::post('/cart/update', [StoreController::class, 'updateCart'])->name('store.cart.update');
Route::post('/cart/remove/{variant}', [StoreController::class, 'removeFromCart'])->name('store.cart.remove');
Route::get('/checkout', [StoreController::class, 'checkout'])->name('store.checkout');
Route::post('/checkout', [StoreController::class, 'processCheckout'])->name('store.checkout.process');

// Guest routes (Login & Registration)
Route::middleware('guest')->group(function () {
    Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [LoginController::class, 'login']);

    Route::get('/register', [RegisterController::class, 'showRegistrationForm'])->name('register');
    Route::post('/register', [RegisterController::class, 'register']);
});

// Authenticated routes
Route::middleware('auth')->group(function () {
    Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

    // Internal Dashboard for Staff (Owner, Admin, Sales, Warehouse, Finance)
    Route::middleware('role:owner,admin,sales,warehouse,finance')->group(function () {
        Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    });

    // MASTER DATA ROUTES
    Route::prefix('master')->group(function () {

        // 1. Categories (Owner & Admin)
        Route::middleware('role:owner,admin')->group(function () {
            Route::get('/categories', [CategoryController::class, 'index'])->name('categories.index');
            Route::post('/categories', [CategoryController::class, 'store'])->name('categories.store');
            Route::put('/categories/{category}', [CategoryController::class, 'update'])->name('categories.update');
            Route::patch('/categories/{category}/toggle', [CategoryController::class, 'toggleStatus'])->name('categories.toggle');
        });

        // 2. Products & Variants
        Route::middleware('role:owner,admin')->group(function () {
            Route::get('/products/create', [ProductController::class, 'create'])->name('products.create');
            Route::post('/products', [ProductController::class, 'store'])->name('products.store');
            Route::get('/products/{product}/edit', [ProductController::class, 'edit'])->name('products.edit');
            Route::put('/products/{product}', [ProductController::class, 'update'])->name('products.update');
            Route::patch('/products/{product}/toggle', [ProductController::class, 'toggleStatus'])->name('products.toggle');
            Route::post('/products/{product}/variants', [ProductController::class, 'storeVariant'])->name('products.variants.store');
            Route::put('/variants/{variant}', [ProductController::class, 'updateVariant'])->name('variants.update');
        });

        Route::middleware('role:owner,admin,sales,warehouse,finance')->group(function () {
            Route::get('/products', [ProductController::class, 'index'])->name('products.index');
            Route::get('/products/{product}', [ProductController::class, 'show'])->name('products.show');
        });

        // 3. Suppliers
        Route::middleware('role:owner,admin,warehouse')->group(function () {
            Route::get('/suppliers', [SupplierController::class, 'index'])->name('suppliers.index');
        });

        Route::middleware('role:owner,admin')->group(function () {
            Route::post('/suppliers', [SupplierController::class, 'store'])->name('suppliers.store');
            Route::put('/suppliers/{supplier}', [SupplierController::class, 'update'])->name('suppliers.update');
            Route::patch('/suppliers/{supplier}/toggle', [SupplierController::class, 'toggleStatus'])->name('suppliers.toggle');
        });

        // 4. Customers (B2B Kafe, Toko, Reseller & Retail)
        Route::middleware('role:owner,admin,sales')->group(function () {
            Route::get('/customers/create', [CustomerController::class, 'create'])->name('customers.create');
            Route::post('/customers', [CustomerController::class, 'store'])->name('customers.store');
            Route::get('/customers/{customer}/edit', [CustomerController::class, 'edit'])->name('customers.edit');
            Route::put('/customers/{customer}', [CustomerController::class, 'update'])->name('customers.update');
            Route::patch('/customers/{customer}/toggle', [CustomerController::class, 'toggleStatus'])->name('customers.toggle');
        });

        Route::middleware('role:owner,admin,sales,finance')->group(function () {
            Route::get('/customers', [CustomerController::class, 'index'])->name('customers.index');
            Route::get('/customers/{customer}', [CustomerController::class, 'show'])->name('customers.show');
        });
    });

    // PERSEDIAAN & PEMBELIAN SUPPLIER (TAHAP 3)
    Route::prefix('inventory')->group(function () {
        // Monitoring Stok & Kartu Mutasi (Semua Staf Internal)
        Route::middleware('role:owner,admin,warehouse,sales,finance')->group(function () {
            Route::get('/stocks', [InventoryController::class, 'index'])->name('inventory.index');
            Route::get('/movements', [InventoryController::class, 'movements'])->name('inventory.movements');
        });

        // Penyesuaian Fisik Stok Opname (Owner, Admin, Warehouse)
        Route::middleware('role:owner,admin,warehouse')->group(function () {
            Route::post('/adjust', [InventoryController::class, 'adjust'])->name('inventory.adjust');
        });

        // Pembelian ke Supplier / PO (Owner, Admin, Warehouse)
        Route::middleware('role:owner,admin,warehouse')->group(function () {
            Route::get('/purchases', [PurchaseController::class, 'index'])->name('purchases.index');
            Route::get('/purchases/create', [PurchaseController::class, 'create'])->name('purchases.create');
            Route::post('/purchases', [PurchaseController::class, 'store'])->name('purchases.store');
            Route::post('/purchases/{purchase}/receive', [PurchaseController::class, 'receive'])->name('purchases.receive');
        });

        // Batalkan PO (Owner & Admin)
        Route::middleware('role:owner,admin')->group(function () {
            Route::post('/purchases/{purchase}/cancel', [PurchaseController::class, 'cancel'])->name('purchases.cancel');
        });

        // Detail PO (Owner, Admin, Warehouse, Finance)
        Route::middleware('role:owner,admin,warehouse,finance')->group(function () {
            Route::get('/purchases/{purchase}', [PurchaseController::class, 'show'])->name('purchases.show');
        });
    });

    // Riwayat Pesanan Pelanggan Retail (Customer)
    Route::get('/my-orders', [StoreController::class, 'myOrders'])->name('store.my_orders');

    // PENJUALAN & PESANAN (TAHAP 4)
    Route::prefix('sales')->group(function () {
        // Input Pesanan Grosir B2B Baru (Owner, Admin, Sales)
        Route::middleware('role:owner,admin,sales')->group(function () {
            Route::get('/orders/create', [OrderController::class, 'create'])->name('orders.create');
            Route::post('/orders', [OrderController::class, 'store'])->name('orders.store');
        });

        // Daftar Pesanan & Detail (Owner, Admin, Sales, Warehouse, Finance)
        Route::middleware('role:owner,admin,sales,warehouse,finance')->group(function () {
            Route::get('/orders', [OrderController::class, 'index'])->name('orders.index');
            Route::get('/orders/{order}', [OrderController::class, 'show'])->name('orders.show');
        });

        // Konfirmasi Pesanan & Potong Stok Otomatis (Owner, Admin, Sales, Warehouse)
        Route::middleware('role:owner,admin,sales,warehouse')->group(function () {
            Route::post('/orders/{order}/confirm', [OrderController::class, 'confirm'])->name('orders.confirm');
        });

        // Batalkan Pesanan & Kembalikan Stok (Owner, Admin, Sales)
        Route::middleware('role:owner,admin,sales')->group(function () {
            Route::post('/orders/{order}/cancel', [OrderController::class, 'cancel'])->name('orders.cancel');
        });
    });

    // KEUANGAN, FAKTUR & PEMBAYARAN PIUTANG (TAHAP 5)
    Route::prefix('finance')->group(function () {
        // Monitoring Faktur, Piutang & Kuitansi (Owner, Admin, Sales, Finance)
        Route::middleware('role:owner,admin,sales,finance')->group(function () {
            Route::get('/invoices', [FinanceController::class, 'invoices'])->name('finance.invoices.index');
            Route::get('/invoices/{invoice}', [FinanceController::class, 'showInvoice'])->name('finance.invoices.show');
            Route::get('/receivables', [FinanceController::class, 'receivables'])->name('finance.receivables.index');
            Route::get('/receivables/{customer}/ledger', [FinanceController::class, 'customerLedger'])->name('finance.receivables.ledger');
            Route::get('/payments/{payment}/receipt', [FinanceController::class, 'printReceipt'])->name('finance.payments.receipt');
        });

        // Terbitkan Faktur & Rekam Pembayaran (Owner, Admin, Finance)
        Route::middleware('role:owner,admin,finance')->group(function () {
            Route::post('/orders/{order}/generate-invoice', [FinanceController::class, 'generateInvoice'])->name('finance.invoices.generate');
            Route::post('/invoices/{invoice}/payments', [FinanceController::class, 'recordPayment'])->name('finance.payments.record');
        });
    });

    // Specific Role Test Routes (for verifying policy & middleware enforcement)
    Route::middleware('role:owner')->group(function () {
        Route::get('/dashboard/owner-zone', function () {
            return response()->json(['status' => 'success', 'message' => 'Akses Khusus Owner Disetujui']);
        })->name('dashboard.owner');
    });

    Route::middleware('role:finance')->group(function () {
        Route::get('/dashboard/finance-zone', function () {
            return response()->json(['status' => 'success', 'message' => 'Akses Khusus Finance Disetujui']);
        })->name('dashboard.finance');
    });
});
