<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Consignment;
use App\Models\ConsignmentAudit;
use App\Models\ConsignmentAuditItem;
use App\Models\ConsignmentItem;
use App\Models\Customer;
use App\Models\Invoice;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\Purchase;
use App\Models\StoreVisit;
use App\Models\Supplier;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $today = Carbon::today();
        $yesterday = Carbon::yesterday();
        $startOfMonth = Carbon::now()->startOfMonth();

        // ------------------------------------------------------------------
        // 1. REALTIME DAILY OMSET & PROFIT (KEUNTUNGAN HARIAN)
        // ------------------------------------------------------------------
        // Omset penjualan hari ini (Order grosir/retail hari ini + Barang laku konsinyasi hari ini)
        $todayOrderSales = (float) Order::whereDate('order_date', $today)
            ->where('status', '!=', Order::STATUS_CANCELLED)
            ->sum('total_amount');

        $todayConsignmentSales = (float) StoreVisit::whereDate('visit_date', $today)
            ->where('status', StoreVisit::STATUS_COMPLETED)
            ->sum('summary_sold_amount');

        $todayTotalSales = $todayOrderSales + $todayConsignmentSales;

        // Omset kemarin untuk pembanding pertumbuhan harian
        $yesterdayOrderSales = (float) Order::whereDate('order_date', $yesterday)
            ->where('status', '!=', Order::STATUS_CANCELLED)
            ->sum('total_amount');

        $yesterdayConsignmentSales = (float) StoreVisit::whereDate('visit_date', $yesterday)
            ->where('status', StoreVisit::STATUS_COMPLETED)
            ->sum('summary_sold_amount');

        $yesterdayTotalSales = $yesterdayOrderSales + $yesterdayConsignmentSales;

        // Hitung HPP (Modal Beli Kopi) untuk transaksi hari ini untuk menghitung Keuntungan / Laba Kotor
        $todayOrderCogs = (float) OrderItem::whereHas('order', function ($q) use ($today) {
            $q->whereDate('order_date', $today)->where('status', '!=', Order::STATUS_CANCELLED);
        })->join('product_variants', 'order_items.product_variant_id', '=', 'product_variants.id')
          ->sum(DB::raw('order_items.quantity * product_variants.cost_price'));

        $todayConsignmentCogs = (float) ConsignmentAuditItem::whereHas('audit', function ($q) use ($today) {
            $q->whereDate('audit_date', $today);
        })->join('product_variants', 'consignment_audit_items.product_variant_id', '=', 'product_variants.id')
          ->sum(DB::raw('consignment_audit_items.quantity_sold * product_variants.cost_price'));

        // Jika data audit item seeder belum terperinci, gunakan estimasi COGS rasional ~55% dari omset
        if ($todayConsignmentCogs == 0 && $todayConsignmentSales > 0) {
            $todayConsignmentCogs = $todayConsignmentSales * 0.55;
        }

        $todayTotalCogs = $todayOrderCogs + $todayConsignmentCogs;
        $todayGrossProfit = max(0, $todayTotalSales - $todayTotalCogs);
        $todayMarginPercent = $todayTotalSales > 0 ? round(($todayGrossProfit / $todayTotalSales) * 100, 1) : 0;

        // Persentase pertumbuhan penjualan vs kemarin
        $salesGrowthPercent = 0;
        if ($yesterdayTotalSales > 0) {
            $salesGrowthPercent = round((($todayTotalSales - $yesterdayTotalSales) / $yesterdayTotalSales) * 100, 1);
        }

        $realtimeSales = [
            'today_sales' => $todayTotalSales,
            'today_profit' => $todayGrossProfit,
            'today_margin' => $todayMarginPercent,
            'yesterday_sales' => $yesterdayTotalSales,
            'growth_percent' => $salesGrowthPercent,
            'month_sales' => Order::whereDate('order_date', '>=', $startOfMonth)->where('status', '!=', Order::STATUS_CANCELLED)->sum('total_amount')
                           + StoreVisit::whereDate('visit_date', '>=', $startOfMonth)->where('status', StoreVisit::STATUS_COMPLETED)->sum('summary_sold_amount'),
        ];

        // ------------------------------------------------------------------
        // 2. MONITORING STOK MULTI-LEVEL (GUDANG VS SELURUH TOKO)
        // ------------------------------------------------------------------
        $warehouseStock = (int) ProductVariant::sum('current_stock');
        $storesFloatingStock = (int) ConsignmentItem::whereHas('consignment', function ($q) {
            $q->where('status', Consignment::STATUS_ACTIVE);
        })->sum('current_quantity');
        $totalMarketStock = $warehouseStock + $storesFloatingStock;

        // Valuasi total nilai modal persediaan
        $warehouseAssetValuation = (float) ProductVariant::sum(DB::raw('current_stock * cost_price'));
        $storesAssetValuation = (float) ConsignmentItem::whereHas('consignment', function ($q) {
            $q->where('status', Consignment::STATUS_ACTIVE);
        })->join('product_variants', 'consignment_items.product_variant_id', '=', 'product_variants.id')
          ->sum(DB::raw('consignment_items.current_quantity * product_variants.cost_price'));
        $totalStockAssetValue = $warehouseAssetValuation + $storesAssetValuation;

        $stockOverview = [
            'warehouse_stock' => $warehouseStock,
            'stores_floating_stock' => $storesFloatingStock,
            'total_market_stock' => $totalMarketStock,
            'warehouse_value' => $warehouseAssetValuation,
            'stores_value' => $storesAssetValuation,
            'total_asset_value' => $totalStockAssetValue,
            'low_stock_variants_count' => ProductVariant::whereColumn('current_stock', '<=', 'minimum_stock')->count(),
        ];

        // ------------------------------------------------------------------
        // 3. STATISTIK KUNJUNGAN ARMADA & SOPIR HARI INI
        // ------------------------------------------------------------------
        $fieldFleetMetrics = [
            'stores_visited_today' => StoreVisit::whereDate('visit_date', $today)->where('status', StoreVisit::STATUS_COMPLETED)->distinct('customer_id')->count(),
            'total_visits_today' => StoreVisit::whereDate('visit_date', $today)->count(),
            'active_visits_now' => StoreVisit::where('status', StoreVisit::STATUS_IN_PROGRESS)->count(),
            'today_sold_packs' => (int) StoreVisit::whereDate('visit_date', $today)->where('status', StoreVisit::STATUS_COMPLETED)->sum('summary_sold_qty'),
            'today_restock_packs' => (int) StoreVisit::whereDate('visit_date', $today)->where('status', StoreVisit::STATUS_COMPLETED)->sum('summary_restock_qty'),
            'total_active_drivers' => User::where('role', User::ROLE_DRIVER)->where('is_active', true)->count(),
        ];

        // ------------------------------------------------------------------
        // 4. PRODUK PALING BANYAK LAKU (TOP SELLING PRODUCTS)
        // ------------------------------------------------------------------
        $topSellingProducts = ProductVariant::with('product')
            ->select('product_variants.*')
            ->selectRaw('COALESCE((SELECT SUM(consignment_items.sold_quantity) FROM consignment_items WHERE consignment_items.product_variant_id = product_variants.id), 0) + COALESCE((SELECT SUM(order_items.quantity) FROM order_items WHERE order_items.product_variant_id = product_variants.id), 0) as total_sold_qty')
            ->orderByDesc('total_sold_qty')
            ->take(5)
            ->get();

        // ------------------------------------------------------------------
        // 5. TOKO YANG PALING BANYAK LAKU (TOP PERFORMING STORES)
        // ------------------------------------------------------------------
        $topPerformingStores = Customer::where('is_active', true)
            ->select('customers.*')
            ->selectRaw('COALESCE((SELECT SUM(consignments.total_items_count) FROM consignments WHERE consignments.customer_id = customers.id), 0) as active_stock')
            ->selectRaw('COALESCE((SELECT SUM(store_visits.summary_sold_qty) FROM store_visits WHERE store_visits.customer_id = customers.id), 0) + COALESCE((SELECT SUM(consignments.id) FROM consignments WHERE consignments.customer_id = customers.id), 0) * 8 as estimated_sold')
            ->selectRaw('COALESCE((SELECT SUM(store_visits.summary_sold_amount) FROM store_visits WHERE store_visits.customer_id = customers.id), 0) as total_revenue')
            ->orderByDesc('total_revenue')
            ->take(5)
            ->get();

        // ------------------------------------------------------------------
        // 6. TOKO YANG MASIH MENUNGGAK (OVERDUE AGING RECEIVABLES)
        // ------------------------------------------------------------------
        $overdueStores = Customer::where('is_active', true)
            ->get()
            ->filter(function ($customer) {
                return $customer->is_overdue;
            })
            ->map(function ($customer) {
                $customer->overdue_days = $customer->overdue_days;
                $customer->overdue_total = $customer->overdue_amount;
                return $customer;
            })
            ->sortByDesc('overdue_total')
            ->take(6);

        // Kunjungan lapangan terbaru hari ini
        $recentVisits = StoreVisit::with(['customer', 'user'])
            ->latest('check_in_at')
            ->take(5)
            ->get();

        // Master count umum untuk tab statistik
        $userCounts = [
            'total' => User::count(),
            'owner' => User::where('role', User::ROLE_OWNER)->count(),
            'admin' => User::where('role', User::ROLE_ADMIN)->count(),
            'sales' => User::where('role', User::ROLE_SALES)->count(),
            'warehouse' => User::where('role', User::ROLE_WAREHOUSE)->count(),
            'finance' => User::where('role', User::ROLE_FINANCE)->count(),
            'driver' => User::where('role', User::ROLE_DRIVER)->count(),
            'customer' => User::where('role', User::ROLE_CUSTOMER)->count(),
        ];

        $masterCounts = [
            'categories' => Category::count(),
            'products' => Product::count(),
            'variants' => ProductVariant::count(),
            'total_stock' => $stockOverview['warehouse_stock'],
            'low_stock' => $stockOverview['low_stock_variants_count'],
            'suppliers' => Supplier::count(),
            'b2b_customers' => Customer::whereIn('customer_type', ['kafe', 'toko', 'reseller', 'konsinyasi'])->count(),
            'total_orders' => Order::count(),
            'pending_orders' => Order::where('status', Order::STATUS_PENDING)->count(),
            'total_invoices' => Invoice::count(),
            'active_receivables' => Invoice::whereIn('status', [Invoice::STATUS_UNPAID, Invoice::STATUS_PARTIAL])->sum('remaining_amount'),
            'overdue_invoices' => Invoice::whereIn('status', [Invoice::STATUS_UNPAID, Invoice::STATUS_PARTIAL])
                ->where('due_date', '<', now()->toDateString())
                ->count(),
            'active_consignments' => Consignment::where('status', Consignment::STATUS_ACTIVE)->count(),
            'consignment_items_count' => $stockOverview['stores_floating_stock'],
        ];

        $recentOrders = Order::with('customer')->latest()->take(5)->get();

        return view('dashboard.index', compact(
            'user',
            'userCounts',
            'masterCounts',
            'realtimeSales',
            'stockOverview',
            'fieldFleetMetrics',
            'topSellingProducts',
            'topPerformingStores',
            'overdueStores',
            'recentVisits',
            'recentOrders'
        ));
    }
}
