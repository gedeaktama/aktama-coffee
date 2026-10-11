<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Customer;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\Purchase;
use App\Models\Supplier;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        // User statistics
        $userCounts = [
            'total' => User::count(),
            'owner' => User::where('role', User::ROLE_OWNER)->count(),
            'admin' => User::where('role', User::ROLE_ADMIN)->count(),
            'sales' => User::where('role', User::ROLE_SALES)->count(),
            'warehouse' => User::where('role', User::ROLE_WAREHOUSE)->count(),
            'finance' => User::where('role', User::ROLE_FINANCE)->count(),
            'customer' => User::where('role', User::ROLE_CUSTOMER)->count(),
        ];

        // Master data statistics
        $masterCounts = [
            'categories' => Category::count(),
            'products' => Product::count(),
            'variants' => ProductVariant::count(),
            'total_stock' => ProductVariant::sum('current_stock'),
            'low_stock' => ProductVariant::whereColumn('current_stock', '<=', 'minimum_stock')->count(),
            'suppliers' => Supplier::count(),
            'b2b_customers' => Customer::whereIn('customer_type', ['kafe', 'toko', 'reseller'])->count(),
            'retail_customers' => Customer::where('customer_type', 'retail')->count(),
            'pending_purchases' => Purchase::where('status', Purchase::STATUS_ORDERED)->count(),
            'total_purchases' => Purchase::count(),
        ];

        return view('dashboard.index', compact('user', 'userCounts', 'masterCounts'));
    }
}
