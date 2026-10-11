@extends('layouts.app')

@section('title', 'Pusat Laporan & Analitik Bisnis - Kopi Distro')

@section('content')
<div class="container py-3">
    <!-- Header Page -->
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-4">
        <div>
            <div class="d-flex align-items-center gap-2">
                <span class="badge bg-success bg-opacity-10 text-success px-3 py-2 rounded-pill fw-semibold">
                    <i class="bi bi-graph-up me-1"></i> Modul Analitik & Laporan Bisnis
                </span>
                <span class="text-muted">&bull;</span>
                <span class="small text-muted">Tahap 7: Laporan Bisnis & Evaluasi Margin</span>
            </div>
            <h2 class="fw-bold text-dark mt-2 mb-1">Pusat Laporan & Analitik Bisnis</h2>
            <p class="text-muted mb-0">Pantau performa penjualan, hitung laba kotor & margin HPP, valuasi aset persediaan gudang, serta rekapitulasi penagihan piutang.</p>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('dashboard') }}" class="btn btn-outline-coffee d-inline-flex align-items-center gap-2">
                <i class="bi bi-speedometer2"></i>
                <span>Dashboard Internal</span>
            </a>
        </div>
    </div>

    <!-- 4 Executive Summary Highlight Cards -->
    <div class="row g-3 mb-4">
        <div class="col-sm-6 col-lg-3">
            <div class="card card-custom p-3 border-start border-primary border-4 h-100">
                <span class="text-muted small fw-semibold text-uppercase">Omset Penjualan (Bulan Ini)</span>
                <h3 class="fw-bold mb-0 text-dark mt-1">Rp {{ number_format($salesReport['total_net'], 0, ',', '.') }}</h3>
                <small class="text-muted mt-2 d-block border-top pt-2">Dari {{ $salesReport['total_orders'] }} transaksi confirmed</small>
            </div>
        </div>

        @if(auth()->user()->hasRole(['owner', 'admin', 'finance']))
            <div class="col-sm-6 col-lg-3">
                <div class="card card-custom p-3 border-start border-success border-4 h-100">
                    <span class="text-muted small fw-semibold text-uppercase">Laba Kotor (Bulan Ini)</span>
                    <h3 class="fw-bold mb-0 text-success mt-1">Rp {{ number_format($profitReport['gross_profit'], 0, ',', '.') }}</h3>
                    <div class="mt-2 border-top pt-2 d-flex justify-content-between small">
                        <span class="text-muted">Margin Kotor:</span>
                        <strong class="text-success">{{ $profitReport['gross_margin_percent'] }}%</strong>
                    </div>
                </div>
            </div>
        @endif

        <div class="col-sm-6 col-lg-3">
            <div class="card card-custom p-3 border-start border-warning border-4 h-100">
                <span class="text-muted small fw-semibold text-uppercase">Nilai Aset Stok Gudang</span>
                <h3 class="fw-bold mb-0 text-dark mt-1">Rp {{ number_format($inventoryReport['total_asset_value'], 0, ',', '.') }}</h3>
                <small class="text-muted mt-2 d-block border-top pt-2">{{ $inventoryReport['total_units'] }} pack di {{ $inventoryReport['total_skus'] }} SKU</small>
            </div>
        </div>

        <div class="col-sm-6 col-lg-3">
            <div class="card card-custom p-3 border-start border-danger border-4 h-100">
                <span class="text-muted small fw-semibold text-uppercase">Piutang Aktif Mitra B2B</span>
                <h3 class="fw-bold mb-0 text-danger mt-1">Rp {{ number_format($receivablesReport['total_debt'], 0, ',', '.') }}</h3>
                <small class="text-muted mt-2 d-block border-top pt-2">Dari seluruh tagihan tempo berjalan</small>
            </div>
        </div>
    </div>

    <!-- 4 Report Module Navigation Cards -->
    <div class="row g-4">
        <!-- Module 1: Laporan Penjualan -->
        <div class="col-md-6">
            <div class="card card-custom p-4 h-100 d-flex flex-column justify-content-between border-0 shadow-sm">
                <div>
                    <div class="d-flex align-items-center justify-content-between mb-3">
                        <div class="p-3 rounded-3 bg-primary bg-opacity-10 text-primary">
                            <i class="bi bi-receipt fs-3"></i>
                        </div>
                        <span class="badge bg-light text-secondary border">Format CSV / Web</span>
                    </div>
                    <h4 class="fw-bold text-dark mb-2">Laporan Penjualan & Omset</h4>
                    <p class="text-muted small mb-3">
                        Rincian seluruh transaksi pesanan kopi kemasan, pemisahan kanal grosir (B2B) vs retail (B2C), perhitungan diskon, ongkos kirim, dan rekapitulasi volume per SKU.
                    </p>
                </div>
                <div class="d-flex gap-2 border-top pt-3">
                    <a href="{{ route('reports.sales') }}" class="btn btn-coffee btn-sm px-3 flex-grow-1">
                        <i class="bi bi-eye me-1"></i> Buka Laporan Penjualan
                    </a>
                    <a href="{{ route('reports.sales', ['export' => 'csv']) }}" class="btn btn-outline-secondary btn-sm px-3">
                        <i class="bi bi-download me-1"></i> CSV
                    </a>
                </div>
            </div>
        </div>

        <!-- Module 2: Laporan Laba Kotor & HPP -->
        @if(auth()->user()->hasRole(['owner', 'admin', 'finance']))
            <div class="col-md-6">
                <div class="card card-custom p-4 h-100 d-flex flex-column justify-content-between border-0 shadow-sm">
                    <div>
                        <div class="d-flex align-items-center justify-content-between mb-3">
                            <div class="p-3 rounded-3 bg-success bg-opacity-10 text-success">
                                <i class="bi bi-cash-coin fs-3"></i>
                            </div>
                            <span class="badge bg-success bg-opacity-10 text-success border border-success border-opacity-25">Owner & Finance</span>
                        </div>
                        <h4 class="fw-bold text-dark mb-2">Laporan Laba Kotor & Analisis Margin HPP</h4>
                        <p class="text-muted small mb-3">
                            Analisis profitabilitas berbasis snapshot HPP (*Cost of Goods Sold*), perbandingan omset vs modal per bungkus kopi, persentase margin laba kotor, dan SKU paling menghasilkan keuntungan.
                        </p>
                    </div>
                    <div class="d-flex gap-2 border-top pt-3">
                        <a href="{{ route('reports.profit') }}" class="btn btn-coffee btn-sm px-3 flex-grow-1">
                            <i class="bi bi-eye me-1"></i> Buka Analisis Margin
                        </a>
                        <a href="{{ route('reports.profit', ['export' => 'csv']) }}" class="btn btn-outline-secondary btn-sm px-3">
                            <i class="bi bi-download me-1"></i> CSV
                        </a>
                    </div>
                </div>
            </div>
        @endif

        <!-- Module 3: Laporan Persediaan & Nilai Aset -->
        <div class="col-md-6">
            <div class="card card-custom p-4 h-100 d-flex flex-column justify-content-between border-0 shadow-sm">
                <div>
                    <div class="d-flex align-items-center justify-content-between mb-3">
                        <div class="p-3 rounded-3 bg-warning bg-opacity-10 text-warning">
                            <i class="bi bi-box-seam fs-3"></i>
                        </div>
                        <span class="badge bg-light text-secondary border">Audit Gudang</span>
                    </div>
                    <h4 class="fw-bold text-dark mb-2">Valuasi Persediaan & Nilai Aset Stok</h4>
                    <p class="text-muted small mb-3">
                        Perhitungan nilai modal rupiah persediaan kopi di gudang, estimasi nilai jual ritel, potensi keuntungan saat stok habis terjual, dan deteksi varian dengan persediaan menipis.
                    </p>
                </div>
                <div class="d-flex gap-2 border-top pt-3">
                    <a href="{{ route('reports.inventory') }}" class="btn btn-coffee btn-sm px-3 flex-grow-1">
                        <i class="bi bi-eye me-1"></i> Buka Valuasi Stok
                    </a>
                    <a href="{{ route('reports.inventory', ['export' => 'csv']) }}" class="btn btn-outline-secondary btn-sm px-3">
                        <i class="bi bi-download me-1"></i> CSV
                    </a>
                </div>
            </div>
        </div>

        <!-- Module 4: Laporan Aging Piutang -->
        <div class="col-md-6">
            <div class="card card-custom p-4 h-100 d-flex flex-column justify-content-between border-0 shadow-sm">
                <div>
                    <div class="d-flex align-items-center justify-content-between mb-3">
                        <div class="p-3 rounded-3 bg-danger bg-opacity-10 text-danger">
                            <i class="bi bi-hourglass-split fs-3"></i>
                        </div>
                        <span class="badge bg-light text-secondary border">Manajemen Kredit</span>
                    </div>
                    <h4 class="fw-bold text-dark mb-2">Rekapitulasi Umur Piutang (AR Aging)</h4>
                    <p class="text-muted small mb-3">
                        Pengelompokan piutang tempo mitra bisnis berdasarkan umur tagihan (Lancar / Belum Jatuh Tempo, 1-30 Hari, 31-60 Hari, >60 Hari) untuk memprioritaskan penagihan piutang bermasalah.
                    </p>
                </div>
                <div class="d-flex gap-2 border-top pt-3">
                    <a href="{{ route('reports.receivables') }}" class="btn btn-coffee btn-sm px-3 flex-grow-1">
                        <i class="bi bi-eye me-1"></i> Buka Rekap Aging AR
                    </a>
                    <a href="{{ route('reports.receivables', ['export' => 'csv']) }}" class="btn btn-outline-secondary btn-sm px-3">
                        <i class="bi bi-download me-1"></i> CSV
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
