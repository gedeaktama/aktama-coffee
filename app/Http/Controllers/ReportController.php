<?php

namespace App\Http\Controllers;

use App\Services\ReportService;
use Illuminate\Http\Request;

class ReportController extends Controller
{
    protected ReportService $reportService;

    public function __construct(ReportService $reportService)
    {
        $this->reportService = $reportService;
    }

    /**
     * Hub Utama Laporan & Analitik Bisnis
     */
    public function index()
    {
        $salesReport = $this->reportService->getSalesReport(now()->startOfMonth()->toDateString(), now()->endOfMonth()->toDateString());
        $profitReport = $this->reportService->getProfitLossReport(now()->startOfMonth()->toDateString(), now()->endOfMonth()->toDateString());
        $inventoryReport = $this->reportService->getInventoryValuationReport();
        $receivablesReport = $this->reportService->getReceivablesAgingReport();

        return view('reports.index', compact('salesReport', 'profitReport', 'inventoryReport', 'receivablesReport'));
    }

    /**
     * Laporan Penjualan (Harian / Bulanan / Per Kanal Grosir & Retail)
     */
    public function sales(Request $request)
    {
        $startDate = $request->input('start_date', now()->startOfMonth()->toDateString());
        $endDate = $request->input('end_date', now()->endOfMonth()->toDateString());
        $channel = $request->input('channel');

        $data = $this->reportService->getSalesReport($startDate, $endDate, $channel);

        if ($request->input('export') === 'csv') {
            $headers = ['No. Order', 'Tanggal', 'Kanal', 'Pelanggan / Penerima', 'Subtotal (Rp)', 'Diskon (Rp)', 'Ongkir (Rp)', 'Total Bersih (Rp)', 'Terbayar (Rp)', 'Status Bayar'];
            $rows = [];
            foreach ($data['orders'] as $ord) {
                $rows[] = [
                    $ord->order_number,
                    $ord->order_date->format('Y-m-d'),
                    strtoupper($ord->order_type),
                    $ord->customer->business_name ?? ($ord->recipient_name ?? '-'),
                    $ord->subtotal,
                    $ord->discount,
                    $ord->shipping_cost,
                    $ord->total_amount,
                    $ord->paid_amount,
                    $ord->payment_status_label,
                ];
            }
            return $this->reportService->exportToCsv("laporan_penjualan_{$startDate}_{$endDate}.csv", $headers, $rows);
        }

        return view('reports.sales', compact('data', 'startDate', 'endDate', 'channel'));
    }

    /**
     * Laporan Laba Kotor & Analisis Margin HPP
     */
    public function profit(Request $request)
    {
        $startDate = $request->input('start_date', now()->startOfMonth()->toDateString());
        $endDate = $request->input('end_date', now()->endOfMonth()->toDateString());

        $data = $this->reportService->getProfitLossReport($startDate, $endDate);

        if ($request->input('export') === 'csv') {
            $headers = ['Kode SKU', 'Nama Produk Kopi', 'Gramasi', 'Qty Terjual', 'Omset Penjualan (Rp)', 'Total HPP (Rp)', 'Laba Kotor (Rp)', 'Margin (%)'];
            $rows = [];
            foreach ($data['sku_breakdown'] as $row) {
                $margin = $row->total_revenue > 0 ? round(($row->total_profit / $row->total_revenue) * 100, 2) : 0;
                $rows[] = [
                    $row->variant->sku ?? '-',
                    $row->variant->product->name ?? 'Produk',
                    ($row->variant->package_weight_grams ?? 0) . 'g',
                    $row->total_qty,
                    $row->total_revenue,
                    $row->total_cogs,
                    $row->total_profit,
                    $margin . '%',
                ];
            }
            return $this->reportService->exportToCsv("laporan_laba_kotor_{$startDate}_{$endDate}.csv", $headers, $rows);
        }

        return view('reports.profit', compact('data', 'startDate', 'endDate'));
    }

    /**
     * Laporan Persediaan & Nilai Aset Stok Gudang
     */
    public function inventory(Request $request)
    {
        $data = $this->reportService->getInventoryValuationReport();

        if ($request->input('export') === 'csv') {
            $headers = ['Kode SKU', 'Nama Produk Kopi', 'Gramasi', 'Bentuk', 'Sisa Stok (Pack)', 'HPP Beli (Rp)', 'Harga Jual (Rp)', 'Nilai Aset Modal (Rp)', 'Nilai Retail (Rp)', 'Potensi Laba (Rp)'];
            $rows = [];
            foreach ($data['variants'] as $item) {
                $v = $item['variant'];
                $rows[] = [
                    $v->sku,
                    $v->product->name,
                    $v->package_weight_grams . 'g',
                    $v->grind_form,
                    $item['stock'],
                    $item['cost_price'],
                    $item['selling_price'],
                    $item['asset_value'],
                    $item['retail_value'],
                    $item['potential_profit'],
                ];
            }
            return $this->reportService->exportToCsv("laporan_aset_persediaan_" . date('Y-m-d') . ".csv", $headers, $rows);
        }

        return view('reports.inventory', compact('data'));
    }

    /**
     * Laporan Rekapitulasi Piutang & Aging AR
     */
    public function receivables(Request $request)
    {
        $data = $this->reportService->getReceivablesAgingReport();

        if ($request->input('export') === 'csv') {
            $headers = ['Kode Mitra', 'Nama Bisnis / Kafe', 'PIC', 'Plafon Kredit (Rp)', 'Lancar / Belum Tempo (Rp)', 'Telat 1-30 Hari (Rp)', 'Telat 31-60 Hari (Rp)', 'Telat > 60 Hari (Rp)', 'Total Piutang (Rp)', 'Sisa Plafon (Rp)'];
            $rows = [];
            foreach ($data['customers'] as $cRow) {
                $c = $cRow['customer'];
                $rows[] = [
                    $c->code,
                    $c->business_name,
                    $c->pic_name,
                    $cRow['credit_limit'],
                    $cRow['current'],
                    $cRow['aging1_30'],
                    $cRow['aging31_60'],
                    $cRow['agingOver60'],
                    $cRow['total_debt'],
                    $cRow['available_credit'],
                ];
            }
            return $this->reportService->exportToCsv("laporan_aging_piutang_" . date('Y-m-d') . ".csv", $headers, $rows);
        }

        return view('reports.receivables', compact('data'));
    }
}
