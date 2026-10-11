<?php

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ProductController;
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
