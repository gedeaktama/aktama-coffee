@extends('layouts.app')

@section('title', 'Dashboard Eksekutif ERP - Kopi Distro')

@push('styles')
<style>
    /* Styling Executive ERP Dashboard */
    .exec-hero-banner {
        background: linear-gradient(135deg, #18181b 0%, #27272a 50%, #3e271a 100%);
        border: 1px solid rgba(245, 158, 11, 0.2);
        color: #ffffff;
        border-radius: 20px;
        padding: 1.75rem 2rem;
        position: relative;
        overflow: hidden;
        box-shadow: 0 15px 35px rgba(0, 0, 0, 0.15);
    }
    .exec-hero-banner::after {
        content: '';
        position: absolute;
        top: -40px;
        right: -40px;
        width: 180px;
        height: 180px;
        background: radial-gradient(circle, rgba(245, 158, 11, 0.2) 0%, transparent 70%);
        border-radius: 50%;
    }
    .kpi-metric-card {
        background: #ffffff;
        border: 1px solid #e2e8f0;
        border-radius: 16px;
        padding: 1.25rem 1.4rem;
        transition: all 0.25s ease;
        position: relative;
        overflow: hidden;
        box-shadow: 0 4px 15px rgba(0, 0, 0, 0.03);
    }
    .kpi-metric-card:hover {
        transform: translateY(-3px);
        box-shadow: 0 12px 28px rgba(0, 0, 0, 0.07);
        border-color: #cbd5e1;
    }
    .kpi-metric-card::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        height: 4px;
    }
    .kpi-card-revenue::before { background: linear-gradient(90deg, #10b981, #059669); }
    .kpi-card-profit::before { background: linear-gradient(90deg, #f59e0b, #d97706); }
    .kpi-card-visits::before { background: linear-gradient(90deg, #3b82f6, #2563eb); }
    .kpi-card-stock::before { background: linear-gradient(90deg, #8b5cf6, #7c3aed); }

    .kpi-icon-pill {
        width: 48px;
        height: 48px;
        border-radius: 14px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.4rem;
    }
    .exec-action-btn {
        background: rgba(255, 255, 255, 0.08);
        border: 1px solid rgba(255, 255, 255, 0.15);
        color: #ffffff;
        padding: 0.6rem 1.15rem;
        border-radius: 12px;
        font-weight: 600;
        font-size: 0.85rem;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        transition: all 0.2s ease;
    }
    .exec-action-btn:hover {
        background: #f59e0b;
        color: #18181b;
        border-color: #f59e0b;
        transform: translateY(-2px);
    }
    .stock-layer-box {
        background: #f8fafc;
        border: 1px solid #edf2f7;
        border-radius: 14px;
        padding: 1rem 1.25rem;
        height: 100%;
    }
    .rank-badge {
        width: 26px;
        height: 26px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: 800;
        font-size: 0.75rem;
    }
    .rank-1 { background: #fef3c7; color: #b45309; border: 1px solid #fde68a; }
    .rank-2 { background: #e2e8f0; color: #475569; border: 1px solid #cbd5e1; }
    .rank-3 { background: #ffedd5; color: #c2410c; border: 1px solid #fed7aa; }
    .rank-other { background: #f1f5f9; color: #64748b; }
</style>
@endpush

@section('content')
<div class="py-2">

    <!-- 1. EXECUTIVE WELCOME BANNER -->
    <div class="exec-hero-banner mb-4">
        <div class="d-flex justify-content-between align-items-center flex-wrap gap-3 mb-3">
            <div class="d-flex align-items-center gap-2">
                <span class="badge bg-warning text-dark px-3 py-1 rounded-pill fw-bold">
                    <i class="bi bi-broadcast me-1"></i> EXECUTIVE LIVE MONITORING
                </span>
                <span class="text-white-50 small">{{ now()->translatedFormat('l, d F Y') }}</span>
            </div>

            <!-- Shortcut Buttons -->
            <div class="d-flex flex-wrap gap-2">
                <a href="{{ route('maps.stores') }}" class="exec-action-btn">
                    <i class="bi bi-geo-alt-fill text-warning"></i> Peta Toko
                </a>
                <a href="{{ route('maps.driver_tracking') }}" class="exec-action-btn">
                    <i class="bi bi-bicycle text-info"></i> Jejak Sopir
                </a>
                <a href="{{ route('driver.dashboard') }}" class="exec-action-btn">
                    <i class="bi bi-qr-code-scan text-success"></i> Portal Sopir
                </a>
            </div>
        </div>

        <div class="row align-items-center">
            <div class="col-12 col-lg-8">
                <h3 class="fw-bold mb-1">Pusat Kendali Distribusi Kopi Distro</h3>
                <p class="text-white-50 mb-0 small">
                    Memantau pasokan ke jaringan Alfamart, Indomaret, kafe, omset harian, keuntungan HPP, dan jejak armada pengantaran secara realtime.
                </p>
            </div>
            <div class="col-12 col-lg-4 text-lg-end mt-3 mt-lg-0">
                <span class="badge bg-dark border border-secondary px-3 py-2 rounded-pill small">
                    <span class="spinner-grow spinner-grow-sm text-success me-1"></span>
                    Armada Aktif: <strong>{{ $fieldFleetMetrics['total_active_drivers'] }} Sopir</strong>
                </span>
            </div>
        </div>
    </div>

    <!-- 2. REALTIME CORE KPI CARDS (JAWABAN KEBUTUHAN UTAMA USER) -->
    <div class="row g-3 mb-4">
        <!-- 1. OMSET HARI INI -->
        <div class="col-12 col-sm-6 col-xl-3">
            <div class="kpi-metric-card kpi-card-revenue h-100">
                <div class="d-flex justify-content-between align-items-start mb-2">
                    <div>
                        <span class="text-muted small text-uppercase fw-bold" style="font-size: 0.72rem; letter-spacing: 0.5px;">Omset Hari Ini</span>
                        <h4 class="fw-bold text-dark mt-1 mb-0">
                            Rp {{ number_format($realtimeSales['today_sales'], 0, ',', '.') }}
                        </h4>
                    </div>
                    <div class="kpi-icon-pill bg-success-subtle text-success">
                        <i class="bi bi-wallet2"></i>
                    </div>
                </div>
                <div class="d-flex align-items-center justify-content-between pt-2 border-top small">
                    <span class="text-muted">Kemarin: Rp {{ number_format($realtimeSales['yesterday_sales'], 0, ',', '.') }}</span>
                    @if($realtimeSales['growth_percent'] >= 0)
                        <span class="text-success fw-bold"><i class="bi bi-arrow-up-right"></i> +{{ $realtimeSales['growth_percent'] }}%</span>
                    @else
                        <span class="text-danger fw-bold"><i class="bi bi-arrow-down-right"></i> {{ $realtimeSales['growth_percent'] }}%</span>
                    @endif
                </div>
            </div>
        </div>

        <!-- 2. KEUNTUNGAN / LABA BERSIH HARI INI -->
        <div class="col-12 col-sm-6 col-xl-3">
            <div class="kpi-metric-card kpi-card-profit h-100">
                <div class="d-flex justify-content-between align-items-start mb-2">
                    <div>
                        <span class="text-muted small text-uppercase fw-bold" style="font-size: 0.72rem; letter-spacing: 0.5px;">Keuntungan HPP Hari Ini</span>
                        <h4 class="fw-bold text-warning-emphasis mt-1 mb-0">
                            Rp {{ number_format($realtimeSales['today_profit'], 0, ',', '.') }}
                        </h4>
                    </div>
                    <div class="kpi-icon-pill bg-warning-subtle text-warning">
                        <i class="bi bi-cash-coin"></i>
                    </div>
                </div>
                <div class="d-flex align-items-center justify-content-between pt-2 border-top small">
                    <span class="text-muted">Margin Laba Harian:</span>
                    <strong class="text-dark">{{ $realtimeSales['today_margin'] }}%</strong>
                </div>
            </div>
        </div>

        <!-- 3. TOKO DIKUNJUNGI HARI INI -->
        <div class="col-12 col-sm-6 col-xl-3">
            <div class="kpi-metric-card kpi-card-visits h-100">
                <div class="d-flex justify-content-between align-items-start mb-2">
                    <div>
                        <span class="text-muted small text-uppercase fw-bold" style="font-size: 0.72rem; letter-spacing: 0.5px;">Kunjungan Toko Hari Ini</span>
                        <h4 class="fw-bold text-primary mt-1 mb-0">
                            {{ $fieldFleetMetrics['stores_visited_today'] }} Toko Selesai
                        </h4>
                    </div>
                    <div class="kpi-icon-pill bg-primary-subtle text-primary">
                        <i class="bi bi-bicycle"></i>
                    </div>
                </div>
                <div class="d-flex align-items-center justify-content-between pt-2 border-top small">
                    <span class="text-muted">Sedang di Lokasi:</span>
                    <span class="badge bg-warning-subtle text-warning">{{ $fieldFleetMetrics['active_visits_now'] }} Toko</span>
                </div>
            </div>
        </div>

        <!-- 4. STOK MENGAPUNG DI TOKO -->
        <div class="col-12 col-sm-6 col-xl-3">
            <div class="kpi-metric-card kpi-card-stock h-100">
                <div class="d-flex justify-content-between align-items-start mb-2">
                    <div>
                        <span class="text-muted small text-uppercase fw-bold" style="font-size: 0.72rem; letter-spacing: 0.5px;">Stok di Rak Semua Toko</span>
                        <h4 class="fw-bold text-info mt-1 mb-0">
                            {{ number_format($stockOverview['stores_floating_stock'], 0, ',', '.') }} Pack
                        </h4>
                    </div>
                    <div class="kpi-icon-pill bg-info-subtle text-info">
                        <i class="bi bi-shop-window"></i>
                    </div>
                </div>
                <div class="d-flex align-items-center justify-content-between pt-2 border-top small">
                    <span class="text-muted">Total Pasar (Gudang+Toko):</span>
                    <strong class="text-dark">{{ number_format($stockOverview['total_market_stock'], 0, ',', '.') }} Pack</strong>
                </div>
            </div>
        </div>
    </div>

    <!-- 3. MULTI-LEVEL INVENTORY OVERVIEW CARD -->
    <div class="card border-0 shadow-sm rounded-4 mb-4">
        <div class="card-body p-3 p-md-4">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <div>
                    <h5 class="fw-bold mb-0">Multi-Layer Persediaan Kopi (Gudang &amp; Pasar)</h5>
                    <small class="text-muted">Visibilitas stok di gudang utama versus stok mengapung di rak Alfamart/Indomaret</small>
                </div>
                <a href="{{ route('inventory.index') }}" class="btn btn-sm btn-outline-secondary rounded-pill">
                    Detail Kartu Stok
                </a>
            </div>

            <div class="row g-3">
                <div class="col-12 col-md-4">
                    <div class="stock-layer-box">
                        <span class="text-muted small d-block">Stok Gudang Utama (Pusat)</span>
                        <h3 class="fw-bold text-dark mt-1 mb-1">{{ number_format($stockOverview['warehouse_stock'], 0, ',', '.') }}</h3>
                        <small class="text-muted">Valuasi Modal: Rp {{ number_format($stockOverview['warehouse_value'], 0, ',', '.') }}</small>
                    </div>
                </div>

                <div class="col-12 col-md-4">
                    <div class="stock-layer-box border-warning bg-warning bg-opacity-10">
                        <span class="text-warning-emphasis small d-block fw-bold">Stok Mengapung di Rak Toko (Konsinyasi)</span>
                        <h3 class="fw-bold text-warning-emphasis mt-1 mb-1">{{ number_format($stockOverview['stores_floating_stock'], 0, ',', '.') }}</h3>
                        <small class="text-muted">Valuasi di Pasar: Rp {{ number_format($stockOverview['stores_value'], 0, ',', '.') }}</small>
                    </div>
                </div>

                <div class="col-12 col-md-4">
                    <div class="stock-layer-box border-primary bg-primary bg-opacity-10">
                        <span class="text-primary small d-block fw-bold">Total Stok Keseluruhan (Pusat + Pasar)</span>
                        <h3 class="fw-bold text-primary mt-1 mb-1">{{ number_format($stockOverview['total_market_stock'], 0, ',', '.') }}</h3>
                        <small class="text-muted">Total Aset Kopi: Rp {{ number_format($stockOverview['total_asset_value'], 0, ',', '.') }}</small>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- 4. TWO COLUMNS RANKING LEADERBOARDS: TOP PRODUK & TOP TOKO -->
    <div class="row g-3 mb-4">
        <!-- TOP SELLING PRODUCTS -->
        <div class="col-12 col-lg-6">
            <div class="card border-0 shadow-sm rounded-4 h-100">
                <div class="card-body p-3 p-md-4">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <div class="d-flex align-items-center gap-2">
                            <span class="badge bg-warning text-dark px-2 py-1 rounded-3"><i class="bi bi-trophy-fill"></i></span>
                            <h5 class="fw-bold mb-0">Produk Paling Banyak Laku</h5>
                        </div>
                        <a href="{{ route('reports.sales') }}" class="small text-decoration-none">Semua Produk &rarr;</a>
                    </div>

                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="table-light small">
                                <tr>
                                    <th style="width: 40px;">#</th>
                                    <th>Varian Produk Kopi</th>
                                    <th class="text-center">Sisa Stok</th>
                                    <th class="text-end">Total Laku</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($topSellingProducts as $idx => $prod)
                                <tr>
                                    <td>
                                        @php
                                            $rankClass = match($idx) { 0 => 'rank-1', 1 => 'rank-2', 2 => 'rank-3', default => 'rank-other' };
                                        @endphp
                                        <div class="rank-badge {{ $rankClass }}">{{ $idx + 1 }}</div>
                                    </td>
                                    <td>
                                        <strong class="d-block text-dark small">{{ $prod->variant_name }}</strong>
                                        <small class="text-muted">{{ $prod->product->name ?? '' }}</small>
                                    </td>
                                    <td class="text-center">
                                        <span class="badge {{ $prod->current_stock <= $prod->minimum_stock ? 'bg-danger' : 'bg-light text-dark border' }}">
                                            {{ $prod->current_stock }} pack
                                        </span>
                                    </td>
                                    <td class="text-end">
                                        <strong class="text-success fs-6">{{ $prod->total_sold_qty }}</strong>
                                        <span class="text-muted small">pack</span>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="4" class="text-center py-3 text-muted">Belum ada data penjualan tercatat.</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- TOP PERFORMING STORES -->
        <div class="col-12 col-lg-6">
            <div class="card border-0 shadow-sm rounded-4 h-100">
                <div class="card-body p-3 p-md-4">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <div class="d-flex align-items-center gap-2">
                            <span class="badge bg-primary text-white px-2 py-1 rounded-3"><i class="bi bi-shop"></i></span>
                            <h5 class="fw-bold mb-0">Toko Paling Banyak Laku</h5>
                        </div>
                        <a href="{{ route('maps.stores') }}" class="small text-decoration-none">Peta Toko &rarr;</a>
                    </div>

                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="table-light small">
                                <tr>
                                    <th style="width: 40px;">#</th>
                                    <th>Nama Toko Mitra</th>
                                    <th class="text-center">Stok Rak</th>
                                    <th class="text-end">Omset Disumbang</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($topPerformingStores as $idx => $st)
                                <tr>
                                    <td>
                                        @php
                                            $rankClass = match($idx) { 0 => 'rank-1', 1 => 'rank-2', 2 => 'rank-3', default => 'rank-other' };
                                        @endphp
                                        <div class="rank-badge {{ $rankClass }}">{{ $idx + 1 }}</div>
                                    </td>
                                    <td>
                                        <strong class="d-block text-dark small">{{ $st->business_name }}</strong>
                                        <small class="badge bg-light text-dark border">{{ strtoupper($st->store_chain ?? 'Retail') }}</small>
                                    </td>
                                    <td class="text-center">
                                        <span class="badge bg-light text-dark border">
                                            {{ $st->getActiveConsignmentStock() }} pack
                                        </span>
                                    </td>
                                    <td class="text-end">
                                        <strong class="text-dark small">Rp {{ number_format($st->total_revenue, 0, ',', '.') }}</strong>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="4" class="text-center py-3 text-muted">Belum ada data transaksi toko.</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- 5. TOKO MENUNGGAK & KUNJUNGAN TERBARU HARI INI -->
    <div class="row g-3">
        <!-- WIDGET TOKO MENUNGGAK (OVERDUE AGING ALERT) -->
        <div class="col-12 col-lg-6">
            <div class="card border-0 shadow-sm rounded-4 h-100 border-start border-4 border-danger">
                <div class="card-body p-3 p-md-4">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <div class="d-flex align-items-center gap-2">
                            <span class="badge bg-danger text-white px-2 py-1 rounded-3"><i class="bi bi-exclamation-triangle-fill"></i></span>
                            <h5 class="fw-bold mb-0">Peringatan Toko Menunggak Tagihan</h5>
                        </div>
                        <a href="{{ route('finance.receivables.index') }}" class="small text-danger text-decoration-none fw-bold">Kelola Piutang &rarr;</a>
                    </div>

                    @if($overdueStores->isNotEmpty())
                        <div class="list-group list-group-flush">
                            @foreach($overdueStores as $ost)
                            <div class="list-group-item px-0 py-2 d-flex justify-content-between align-items-center">
                                <div>
                                    <strong class="d-block text-dark small">{{ $ost->business_name }}</strong>
                                    <small class="text-danger">
                                        <i class="bi bi-clock-history me-1"></i> Lewat jatuh tempo {{ $ost->overdue_days }} hari
                                    </small>
                                </div>
                                <div class="text-end">
                                    <strong class="d-block text-danger fs-6">
                                        Rp {{ number_format($ost->overdue_total, 0, ',', '.') }}
                                    </strong>
                                    <a href="tel:{{ $ost->phone }}" class="btn btn-sm btn-outline-danger rounded-pill px-2 py-0" style="font-size: 0.72rem;">
                                        <i class="bi bi-telephone-fill"></i> Hubungi
                                    </a>
                                </div>
                            </div>
                            @endforeach
                        </div>
                    @else
                        <div class="alert alert-success d-flex align-items-center gap-2 rounded-4 py-3 mb-0 small">
                            <i class="bi bi-check-circle-fill fs-5"></i>
                            <div>Semua toko mitra tertib dan tidak ada tagihan menunggak yang melewati jatuh tempo!</div>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- WIDGET KUNJUNGAN LAPANGAN TERBARU -->
        <div class="col-12 col-lg-6">
            <div class="card border-0 shadow-sm rounded-4 h-100">
                <div class="card-body p-3 p-md-4">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <div class="d-flex align-items-center gap-2">
                            <span class="badge bg-dark text-warning px-2 py-1 rounded-3"><i class="bi bi-clock-history"></i></span>
                            <h5 class="fw-bold mb-0">Aktivitas Armada Lapangan Hari Ini</h5>
                        </div>
                        <a href="{{ route('maps.driver_tracking') }}" class="small text-decoration-none">Peta Jejak &rarr;</a>
                    </div>

                    <div class="list-group list-group-flush">
                        @forelse($recentVisits as $rv)
                        <div class="list-group-item px-0 py-2 d-flex justify-content-between align-items-center">
                            <div>
                                <div class="d-flex align-items-center gap-2">
                                    <strong class="text-dark small">{{ $rv->customer->business_name }}</strong>
                                    <span class="badge {{ $rv->status_badge_class }}" style="font-size: 0.65rem;">
                                        {{ $rv->status_label }}
                                    </span>
                                </div>
                                <small class="text-muted">
                                    Sopir: {{ $rv->user->name }} &bull; Check-in: {{ $rv->check_in_at->format('H:i') }} WITA
                                </small>
                            </div>
                            <div class="text-end">
                                <span class="badge bg-success-subtle text-success small">
                                    {{ $rv->summary_sold_qty }} pack laku
                                </span>
                                @if($rv->summary_restock_qty > 0)
                                    <span class="badge bg-warning-subtle text-warning small d-block mt-1">
                                        +{{ $rv->summary_restock_qty }} restock
                                    </span>
                                @endif
                            </div>
                        </div>
                        @empty
                        <div class="text-center py-4 text-muted small">
                            Belum ada aktivitas kunjungan toko hari ini.
                        </div>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
    </div>

</div>
@endsection
