@extends('layouts.app')

@section('title', 'Laporan Laba Kotor & Margin HPP - Kopi Distro')

@section('content')
<div class="container py-3">
    <!-- Header Page -->
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-4">
        <div>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-1">
                    <li class="breadcrumb-item"><a href="{{ route('reports.index') }}" class="text-decoration-none">Laporan</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Laba Kotor & HPP</li>
                </ol>
            </nav>
            <div class="d-flex align-items-center gap-2">
                <h2 class="fw-bold text-dark mb-0">Laporan Laba Kotor & Analisis Margin HPP</h2>
                <span class="badge bg-success bg-opacity-10 text-success border border-success border-opacity-25 px-2 py-1">
                    Profit & Loss
                </span>
            </div>
            <p class="text-muted small mb-0">Periode: <strong>{{ \Carbon\Carbon::parse($startDate)->translatedFormat('d F Y') }}</strong> s/d <strong>{{ \Carbon\Carbon::parse($endDate)->translatedFormat('d F Y') }}</strong></p>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('reports.profit', array_merge(request()->all(), ['export' => 'csv'])) }}" class="btn btn-outline-success d-inline-flex align-items-center gap-2">
                <i class="bi bi-file-earmark-spreadsheet"></i>
                <span>Download Analisis (CSV)</span>
            </a>
            <a href="{{ route('reports.index') }}" class="btn btn-outline-coffee">
                <i class="bi bi-arrow-left me-1"></i> Kembali
            </a>
        </div>
    </div>

    <!-- Filter Toolbar -->
    <div class="card card-custom p-3 mb-4">
        <form method="GET" action="{{ route('reports.profit') }}" class="row g-2 align-items-end">
            <div class="col-md-4">
                <label for="start_date" class="form-label small fw-semibold text-muted mb-1">Dari Tanggal</label>
                <input type="date" name="start_date" id="start_date" class="form-control form-control-sm" value="{{ $startDate }}">
            </div>
            <div class="col-md-4">
                <label for="end_date" class="form-label small fw-semibold text-muted mb-1">Sampai Tanggal</label>
                <input type="date" name="end_date" id="end_date" class="form-control form-control-sm" value="{{ $endDate }}">
            </div>
            <div class="col-md-4 d-flex gap-2">
                <button type="submit" class="btn btn-coffee btn-sm flex-grow-1">
                    <i class="bi bi-filter me-1"></i> Terapkan Periode
                </button>
                <a href="{{ route('reports.profit') }}" class="btn btn-outline-secondary btn-sm">Reset</a>
            </div>
        </form>
    </div>

    <!-- Executive Profitability Metrics -->
    <div class="row g-3 mb-4">
        <div class="col-sm-6 col-lg-3">
            <div class="card card-custom p-3 border-start border-primary border-4">
                <span class="text-muted small fw-semibold text-uppercase">Total Omset (Revenue)</span>
                <h4 class="fw-bold mb-0 text-dark">Rp {{ number_format($data['total_revenue'], 0, ',', '.') }}</h4>
                <small class="text-muted mt-2 d-block border-top pt-2">Dari {{ $data['total_orders'] }} pesanan selesai</small>
            </div>
        </div>

        <div class="col-sm-6 col-lg-3">
            <div class="card card-custom p-3 border-start border-danger border-4">
                <span class="text-muted small fw-semibold text-uppercase">Beban Pokok Penjualan (HPP)</span>
                <h4 class="fw-bold mb-0 text-danger">Rp {{ number_format($data['total_cogs'], 0, ',', '.') }}</h4>
                <small class="text-muted mt-2 d-block border-top pt-2">Total modal biaya biji kopi</small>
            </div>
        </div>

        <div class="col-sm-6 col-lg-3">
            <div class="card card-custom p-3 border-start border-success border-4">
                <span class="text-muted small fw-semibold text-uppercase">Laba Kotor (Gross Profit)</span>
                <h3 class="fw-bold mb-0 text-success">Rp {{ number_format($data['gross_profit'], 0, ',', '.') }}</h3>
                <small class="text-muted mt-2 d-block border-top pt-2">Selisih Omset dikurangi HPP</small>
            </div>
        </div>

        <div class="col-sm-6 col-lg-3">
            <div class="card card-custom p-3 border-start border-info border-4">
                <span class="text-muted small fw-semibold text-uppercase">Margin Laba Kotor</span>
                <h3 class="fw-bold mb-0 text-primary">{{ $data['gross_margin_percent'] }}%</h3>
                <small class="text-muted mt-2 d-block border-top pt-2">Efisiensi margin rata-rata produk</small>
            </div>
        </div>
    </div>

    <!-- Table: Profitability Breakdown per SKU -->
    <div class="card card-custom overflow-hidden border-0 shadow-sm">
        <div class="card-header bg-white py-3 border-bottom d-flex justify-content-between align-items-center">
            <h5 class="fw-bold text-dark mb-0">
                <i class="bi bi-pie-chart text-success me-2"></i> Perincian Profitabilitas & Margin per Varian SKU Kopi
            </h5>
            <span class="badge bg-light text-secondary border">{{ $data['sku_breakdown']->count() }} SKU Terjual</span>
        </div>
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th class="ps-3 py-3 text-secondary text-uppercase small fw-bold">Varian Kopi</th>
                        <th class="py-3 text-secondary text-uppercase small fw-bold">SKU</th>
                        <th class="py-3 text-secondary text-uppercase small fw-bold text-center">Qty Terjual</th>
                        <th class="py-3 text-secondary text-uppercase small fw-bold text-end">Omset (Rp)</th>
                        <th class="py-3 text-secondary text-uppercase small fw-bold text-end">HPP / Modal (Rp)</th>
                        <th class="py-3 text-secondary text-uppercase small fw-bold text-end">Laba Kotor (Rp)</th>
                        <th class="pe-3 py-3 text-secondary text-uppercase small fw-bold text-center">Margin (%)</th>
                    </tr>
                </thead>
                <tbody class="border-top-0">
                    @forelse($data['sku_breakdown'] as $row)
                        @php
                            $margin = $row->total_revenue > 0 ? round(($row->total_profit / $row->total_revenue) * 100, 1) : 0;
                        @endphp
                        <tr>
                            <td class="ps-3">
                                <strong class="text-dark">{{ $row->variant->product->name ?? 'Produk' }}</strong>
                                <small class="text-muted d-block">{{ $row->variant->variant_name ?? '' }} ({{ $row->variant->package_weight_grams ?? 0 }}g)</small>
                            </td>
                            <td class="font-monospace small text-secondary">
                                {{ $row->variant->sku ?? '-' }}
                            </td>
                            <td class="text-center font-monospace fw-bold text-dark">
                                {{ $row->total_qty }} pack
                            </td>
                            <td class="text-end font-monospace text-dark">
                                Rp {{ number_format($row->total_revenue, 0, ',', '.') }}
                            </td>
                            <td class="text-end font-monospace text-danger">
                                Rp {{ number_format($row->total_cogs, 0, ',', '.') }}
                            </td>
                            <td class="text-end font-monospace fw-bold text-success fs-6">
                                Rp {{ number_format($row->total_profit, 0, ',', '.') }}
                            </td>
                            <td class="pe-3 text-center">
                                <span class="badge {{ $margin >= 40 ? 'bg-success' : ($margin >= 25 ? 'bg-primary' : 'bg-warning text-dark') }} px-2 py-1">
                                    {{ $margin }}%
                                </span>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center py-5 text-muted">
                                Belum ada data transaksi yang dapat dihitung profitabilitasnya pada periode ini.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
                @if($data['sku_breakdown']->count() > 0)
                    <tfoot class="table-light">
                        <tr>
                            <th colspan="3" class="ps-3 fw-bold text-end">TOTAL KESELURUHAN:</th>
                            <th class="text-end font-monospace fw-bold text-dark">Rp {{ number_format($data['total_revenue'], 0, ',', '.') }}</th>
                            <th class="text-end font-monospace fw-bold text-danger">Rp {{ number_format($data['total_cogs'], 0, ',', '.') }}</th>
                            <th class="text-end font-monospace fw-bold text-success fs-6">Rp {{ number_format($data['gross_profit'], 0, ',', '.') }}</th>
                            <th class="text-center font-monospace fw-bold text-primary">{{ $data['gross_margin_percent'] }}%</th>
                        </tr>
                    </tfoot>
                @endif
            </table>
        </div>
    </div>
</div>
@endsection
