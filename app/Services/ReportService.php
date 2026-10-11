<?php

namespace App\Services;

use App\Models\Customer;
use App\Models\Invoice;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\ProductVariant;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ReportService
{
    /**
     * Laporan Penjualan (Sales Report)
     */
    public function getSalesReport(?string $startDate = null, ?string $endDate = null, ?string $channel = null): array
    {
        $start = $startDate ? Carbon::parse($startDate)->startOfDay() : now()->startOfMonth();
        $end = $endDate ? Carbon::parse($endDate)->endOfDay() : now()->endOfMonth();

        $query = Order::with(['customer', 'items.variant.product'])
            ->where('status', '!=', Order::STATUS_CANCELLED)
            ->whereBetween('order_date', [$start->toDateString(), $end->toDateString()]);

        if ($channel && in_array($channel, [Order::TYPE_B2B, Order::TYPE_RETAIL])) {
            $query->where('order_type', $channel);
        }

        $orders = $query->latest('order_date')->get();

        $totalGross = $orders->sum('subtotal');
        $totalDiscount = $orders->sum('discount');
        $totalShipping = $orders->sum('shipping_cost');
        $totalNet = $orders->sum('total_amount');
        $totalPaid = $orders->sum('paid_amount');
        $totalUnpaid = max(0, $totalNet - $totalPaid);

        // Rekapitulasi per Varian SKU
        $orderIds = $orders->pluck('id');
        $skuSummary = OrderItem::with('variant.product')
            ->whereIn('order_id', $orderIds)
            ->select('product_variant_id', DB::raw('SUM(quantity) as total_qty'), DB::raw('SUM(subtotal) as total_revenue'))
            ->groupBy('product_variant_id')
            ->get()
            ->sortByDesc('total_revenue');

        return [
            'start_date' => $start->toDateString(),
            'end_date' => $end->toDateString(),
            'channel' => $channel,
            'orders' => $orders,
            'total_orders' => $orders->count(),
            'total_gross' => $totalGross,
            'total_discount' => $totalDiscount,
            'total_shipping' => $totalShipping,
            'total_net' => $totalNet,
            'total_paid' => $totalPaid,
            'total_unpaid' => $totalUnpaid,
            'sku_summary' => $skuSummary,
        ];
    }

    /**
     * Laporan Laba Kotor & Analisis Margin HPP (Gross Profit & Margin Report)
     */
    public function getProfitLossReport(?string $startDate = null, ?string $endDate = null): array
    {
        $start = $startDate ? Carbon::parse($startDate)->startOfDay() : now()->startOfMonth();
        $end = $endDate ? Carbon::parse($endDate)->endOfDay() : now()->endOfMonth();

        // Ambil order confirmed/completed dalam periode
        $orders = Order::with('items.variant.product')
            ->where('status', '!=', Order::STATUS_CANCELLED)
            ->where('status', '!=', Order::STATUS_PENDING)
            ->whereBetween('order_date', [$start->toDateString(), $end->toDateString()])
            ->get();

        $totalRevenue = 0;
        $totalCogs = 0; // Cost of Goods Sold / HPP

        foreach ($orders as $order) {
            foreach ($order->items as $item) {
                $totalRevenue += (float)$item->subtotal;
                $totalCogs += (float)($item->cost_price * $item->quantity);
            }
        }

        $grossProfit = $totalRevenue - $totalCogs;
        $grossMarginPercent = $totalRevenue > 0 ? round(($grossProfit / $totalRevenue) * 100, 2) : 0;

        // Breakdown per SKU
        $orderIds = $orders->pluck('id');
        $skuBreakdown = OrderItem::with('variant.product')
            ->whereIn('order_id', $orderIds)
            ->select(
                'product_variant_id',
                DB::raw('SUM(quantity) as total_qty'),
                DB::raw('SUM(subtotal) as total_revenue'),
                DB::raw('SUM(cost_price * quantity) as total_cogs'),
                DB::raw('SUM(subtotal - (cost_price * quantity)) as total_profit')
            )
            ->groupBy('product_variant_id')
            ->get()
            ->sortByDesc('total_profit');

        return [
            'start_date' => $start->toDateString(),
            'end_date' => $end->toDateString(),
            'total_orders' => $orders->count(),
            'total_revenue' => $totalRevenue,
            'total_cogs' => $totalCogs,
            'gross_profit' => $grossProfit,
            'gross_margin_percent' => $grossMarginPercent,
            'sku_breakdown' => $skuBreakdown,
        ];
    }

    /**
     * Laporan Persediaan & Nilai Aset Stok (Inventory Valuation Report)
     */
    public function getInventoryValuationReport(): array
    {
        $variants = ProductVariant::with('product.category')
            ->orderBy('product_id')
            ->get()
            ->map(function ($v) {
                $stock = (int)$v->current_stock;
                $cost = (float)$v->cost_price;
                $price = (float)$v->selling_price;
                $totalAssetValue = $stock * $cost;
                $totalRetailValue = $stock * $price;
                $potentialProfit = $totalRetailValue - $totalAssetValue;

                return [
                    'variant' => $v,
                    'stock' => $stock,
                    'cost_price' => $cost,
                    'selling_price' => $price,
                    'asset_value' => $totalAssetValue,
                    'retail_value' => $totalRetailValue,
                    'potential_profit' => $potentialProfit,
                    'is_low_stock' => $v->isLowStock(),
                ];
            });

        $totalUnits = $variants->sum('stock');
        $totalAssetValue = $variants->sum('asset_value');
        $totalRetailValue = $variants->sum('retail_value');
        $lowStockCount = $variants->where('is_low_stock', true)->count();

        return [
            'variants' => $variants,
            'total_skus' => $variants->count(),
            'total_units' => $totalUnits,
            'total_asset_value' => $totalAssetValue,
            'total_retail_value' => $totalRetailValue,
            'low_stock_count' => $lowStockCount,
        ];
    }

    /**
     * Laporan Rekapitulasi Piutang & Umur Piutang (AR Aging Report)
     */
    public function getReceivablesAgingReport(): array
    {
        $customers = Customer::whereIn('customer_type', [Customer::TYPE_KAFE, Customer::TYPE_TOKO, Customer::TYPE_RESELLER])
            ->where('is_active', true)
            ->get()
            ->map(function ($c) {
                $unpaidInvoices = $c->invoices()
                    ->whereIn('status', [Invoice::STATUS_UNPAID, Invoice::STATUS_PARTIAL])
                    ->get();

                $current = 0;       // Belum jatuh tempo
                $aging1_30 = 0;     // Telat 1-30 hari
                $aging31_60 = 0;    // Telat 31-60 hari
                $agingOver60 = 0;   // Telat > 60 hari

                foreach ($unpaidInvoices as $inv) {
                    $rem = (float)$inv->remaining_amount;
                    if (!$inv->isOverdue()) {
                        $current += $rem;
                    } else {
                        $days = $inv->days_overdue;
                        if ($days <= 30) {
                            $aging1_30 += $rem;
                        } elseif ($days <= 60) {
                            $aging31_60 += $rem;
                        } else {
                            $agingOver60 += $rem;
                        }
                    }
                }

                $totalDebt = $current + $aging1_30 + $aging31_60 + $agingOver60;

                return [
                    'customer' => $c,
                    'credit_limit' => (float)$c->credit_limit,
                    'current' => $current,
                    'aging1_30' => $aging1_30,
                    'aging31_60' => $aging31_60,
                    'agingOver60' => $agingOver60,
                    'total_debt' => $totalDebt,
                    'available_credit' => $c->getAvailableCredit(),
                ];
            })
            ->filter(fn($row) => $row['total_debt'] > 0 || $row['credit_limit'] > 0);

        return [
            'customers' => $customers,
            'total_debt' => $customers->sum('total_debt'),
            'total_current' => $customers->sum('current'),
            'total_aging1_30' => $customers->sum('aging1_30'),
            'total_aging31_60' => $customers->sum('aging31_60'),
            'total_agingOver60' => $customers->sum('agingOver60'),
        ];
    }

    /**
     * Stream Download CSV Native
     */
    public function exportToCsv(string $filename, array $headers, array $rows): StreamedResponse
    {
        return response()->streamDownload(function () use ($headers, $rows) {
            $handle = fopen('php://output', 'w');
            
            // UTF-8 BOM untuk kompatibilitas Microsoft Excel Windows
            fputs($handle, "\xEF\xBB\xBF");

            // Tulis Header Kolom
            fputcsv($handle, $headers, ';');

            // Tulis Baris Data
            foreach ($rows as $row) {
                fputcsv($handle, $row, ';');
            }

            fclose($handle);
        }, $filename, [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ]);
    }
}
