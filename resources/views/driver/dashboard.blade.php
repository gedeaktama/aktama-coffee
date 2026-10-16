@extends('layouts.app')

@section('title', 'Portal Sopir & Kurir - Kopi Distro')

@push('styles')
<style>
    /* Styling Mobile-First Driver Portal */
    .driver-hero-card {
        background: linear-gradient(135deg, #1e130c 0%, #3e271a 60%, #b86b35 100%);
        color: #ffffff;
        border-radius: 20px;
        padding: 1.5rem;
        position: relative;
        overflow: hidden;
        box-shadow: 0 10px 25px rgba(30, 19, 12, 0.25);
    }
    .driver-hero-card::after {
        content: '';
        position: absolute;
        right: -30px;
        bottom: -30px;
        width: 140px;
        height: 140px;
        background: radial-gradient(circle, rgba(224, 159, 88, 0.25) 0%, transparent 70%);
        border-radius: 50%;
    }
    .driver-stat-card {
        background: #ffffff;
        border: 1px solid #e2e8f0;
        border-radius: 16px;
        padding: 1.15rem;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.03);
        transition: transform 0.2s ease;
    }
    .driver-stat-card:hover {
        transform: translateY(-2px);
    }
    .driver-quick-btn {
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        padding: 1rem 0.75rem;
        border-radius: 16px;
        text-decoration: none;
        font-weight: 700;
        font-size: 0.85rem;
        transition: all 0.2s ease;
    }
    .driver-quick-btn i {
        font-size: 1.75rem;
        margin-bottom: 0.35rem;
    }
    .btn-scan-main {
        background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);
        color: #ffffff !important;
        border: none;
        box-shadow: 0 8px 20px rgba(245, 158, 11, 0.35);
    }
    .btn-scan-main:hover {
        transform: scale(1.02);
        box-shadow: 0 12px 24px rgba(245, 158, 11, 0.45);
    }
    .store-item-card {
        background: #ffffff;
        border: 1px solid #e2e8f0;
        border-radius: 14px;
        padding: 1rem 1.15rem;
        margin-bottom: 0.85rem;
        transition: all 0.2s ease;
    }
    .store-item-card:hover {
        border-color: #cbd5e1;
        box-shadow: 0 6px 16px rgba(0,0,0,0.05);
    }
    .badge-chain {
        font-size: 0.75rem;
        padding: 0.35rem 0.65rem;
        border-radius: 8px;
        font-weight: 700;
        letter-spacing: 0.5px;
    }
    .chain-alfamart { background-color: #fee2e2; color: #dc2626; border: 1px solid #fca5a5; }
    .chain-indomaret { background-color: #dbeafe; color: #2563eb; border: 1px solid #93c5fd; }
    .chain-kelontong { background-color: #fef3c7; color: #d97706; border: 1px solid #fde68a; }
    .chain-kafe { background-color: #f3e8ff; color: #7e22ce; border: 1px solid #d8b4fe; }
</style>
@endpush

@section('content')
<div class="py-2">

    <!-- 1. DRIVER HEADER & ACTIVE BANNER -->
    <div class="driver-hero-card mb-4">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <div class="d-flex align-items-center gap-2">
                <span class="badge bg-warning text-dark px-3 py-1 rounded-pill fw-bold">
                    <i class="bi bi-bicycle me-1"></i> ARMADA LAPANGAN
                </span>
                <span class="text-white-50 small">{{ now()->translatedFormat('l, d F Y') }}</span>
            </div>
            <a href="{{ route('driver.visits.history') }}" class="btn btn-sm btn-outline-light rounded-pill px-3">
                <i class="bi bi-clock-history me-1"></i> Riwayat
            </a>
        </div>
        <h3 class="fw-bold mb-1">Halo, {{ $user->name }}! 👋</h3>
        <p class="text-white-50 mb-3 small">
            Semangat mengantar kesegaran kopi ke toko-toko mitra hari ini. Jaga keselamatan di jalan!
        </p>

        <!-- Tombol Aksi Cepat Scan Toko -->
        <div class="row g-2 pt-1">
            <div class="col-7">
                <a href="{{ route('driver.scan') }}" class="driver-quick-btn btn-scan-main h-100">
                    <i class="bi bi-qr-code-scan"></i>
                    <span>SCAN TOKO TIBA</span>
                </a>
            </div>
            <div class="col-5">
                <a href="{{ route('maps.stores') }}" class="driver-quick-btn bg-dark bg-opacity-50 text-white h-100 border border-secondary border-opacity-50">
                    <i class="bi bi-geo-alt-fill text-warning"></i>
                    <span>PETA TOKO</span>
                </a>
            </div>
        </div>
    </div>

    <!-- 2. KUNJUNGAN SEDANG AKTIF (JIKA ADA) -->
    @if($activeVisit)
    <div class="alert alert-warning border-2 border-warning shadow-sm rounded-4 p-3 mb-4">
        <div class="d-flex align-items-center justify-content-between flex-wrap gap-2">
            <div class="d-flex align-items-center gap-3">
                <div class="spinner-grow text-warning" role="status" style="width: 2rem; height: 2rem;">
                    <span class="visually-hidden">Loading...</span>
                </div>
                <div>
                    <span class="badge bg-dark text-warning px-2 py-1 mb-1">KUNJUNGAN AKTIF</span>
                    <h6 class="fw-bold mb-0 text-dark">{{ $activeVisit->customer->business_name }}</h6>
                    <small class="text-muted">
                        Check-in: {{ $activeVisit->check_in_at->format('H:i') }} WITA ({{ $activeVisit->check_in_at->diffForHumans() }})
                    </small>
                </div>
            </div>
            <a href="{{ route('driver.visit.action', $activeVisit->id) }}" class="btn btn-dark fw-bold rounded-pill px-4">
                Lanjutkan Kunjungan <i class="bi bi-arrow-right ms-1"></i>
            </a>
        </div>
    </div>
    @endif

    <!-- 3. STATISTIK KINERJA HARI INI -->
    <h6 class="fw-bold text-muted text-uppercase small mb-3">
        <i class="bi bi-speedometer2 me-1"></i> Ringkasan Kinerja Anda Hari Ini
    </h6>
    <div class="row g-2 mb-4">
        <div class="col-6 col-md-3">
            <div class="driver-stat-card">
                <span class="text-muted small d-block">Toko Dikunjungi</span>
                <div class="d-flex align-items-baseline gap-1 mt-1">
                    <span class="fs-3 fw-bold text-primary">{{ $todayStats['stores_visited'] }}</span>
                    <span class="text-muted small">toko</span>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="driver-stat-card">
                <span class="text-muted small d-block">Produk Laku</span>
                <div class="d-flex align-items-baseline gap-1 mt-1">
                    <span class="fs-3 fw-bold text-success">{{ $todayStats['total_sold_qty'] }}</span>
                    <span class="text-muted small">pack</span>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="driver-stat-card">
                <span class="text-muted small d-block">Omset Terkumpul</span>
                <div class="d-flex align-items-baseline gap-1 mt-1">
                    <span class="fs-5 fw-bold text-dark">Rp {{ number_format($todayStats['total_sold_amount'], 0, ',', '.') }}</span>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="driver-stat-card">
                <span class="text-muted small d-block">Barang Restock</span>
                <div class="d-flex align-items-baseline gap-1 mt-1">
                    <span class="fs-3 fw-bold text-warning">{{ $todayStats['total_restock_qty'] }}</span>
                    <span class="text-muted small">pack</span>
                </div>
            </div>
        </div>
    </div>

    <!-- 4. DAFTAR TOKO TARGET KUNJUNGAN & STATUS -->
    <div class="d-flex justify-content-between align-items-center mb-3">
        <div>
            <h5 class="fw-bold mb-0">Daftar Toko & Rute Mitra</h5>
            <small class="text-muted">Pilih toko untuk navigasi Google Maps atau langsung Scan QR</small>
        </div>
        <span class="badge bg-light text-dark border px-3 py-2 rounded-pill">
            Total {{ $allStores->count() }} Toko
        </span>
    </div>

    <div class="row">
        @forelse($allStores as $store)
        <div class="col-12 col-md-6">
            <div class="store-item-card {{ $store->visited_today ? 'border-success bg-light bg-opacity-25' : '' }}">
                <div class="d-flex justify-content-between align-items-start mb-2">
                    <div>
                        <!-- Badge Brand Chain -->
                        @php
                            $chainClass = match($store->store_chain) {
                                'alfamart' => 'chain-alfamart',
                                'indomaret' => 'chain-indomaret',
                                'kafe' => 'chain-kafe',
                                default => 'chain-kelontong'
                            };
                        @endphp
                        <span class="badge-chain {{ $chainClass }} me-1">
                            {{ strtoupper($store->store_chain ?? 'TOKO') }}
                        </span>
                        
                        @if($store->visited_today)
                            <span class="badge bg-success-subtle text-success border border-success border-opacity-25">
                                <i class="bi bi-check-circle-fill me-1"></i> Sudah Dikunjungi
                            </span>
                        @elseif($store->is_low_stock)
                            <span class="badge bg-danger-subtle text-danger border border-danger border-opacity-25">
                                <i class="bi bi-exclamation-triangle-fill me-1"></i> Perlu Restock
                            </span>
                        @else
                            <span class="badge bg-secondary-subtle text-secondary">Belum Dikunjungi</span>
                        @endif
                    </div>

                    <!-- Tombol Navigasi Google Maps -->
                    @if($store->google_maps_url)
                    <a href="{{ $store->google_maps_url }}" target="_blank" class="btn btn-sm btn-outline-primary rounded-pill px-2 py-1" title="Buka Rute di Google Maps">
                        <i class="bi bi-compass me-1"></i> Rute Maps
                    </a>
                    @endif
                </div>

                <h6 class="fw-bold mb-1">{{ $store->business_name }}</h6>
                <p class="text-muted small mb-2 text-truncate" title="{{ $store->shipping_address ?? $store->address }}">
                    <i class="bi bi-geo-alt me-1 text-danger"></i> {{ $store->shipping_address ?? $store->address ?? 'Alamat belum diatur' }}
                </p>

                <div class="d-flex justify-content-between align-items-center pt-2 border-top">
                    <div class="small">
                        <span class="text-muted">Sisa Stok Rak:</span>
                        <strong class="{{ $store->is_low_stock ? 'text-danger' : 'text-dark' }}">
                            {{ $store->active_consignment_stock }} pack
                        </strong>
                    </div>

                    <div class="d-flex gap-1">
                        <a href="{{ route('driver.print_qr', $store->id) }}" target="_blank" class="btn btn-sm btn-light border" title="Cetak / Lihat QR Code Toko">
                            <i class="bi bi-qr-code"></i>
                        </a>

                        <form action="{{ route('driver.checkin') }}" method="POST" class="d-inline">
                            @csrf
                            <input type="hidden" name="customer_id" value="{{ $store->id }}">
                            <input type="hidden" name="visit_purpose" value="audit_delivery">
                            <button type="submit" class="btn btn-sm {{ $store->visited_today ? 'btn-outline-success' : 'btn-warning' }} fw-bold rounded-pill px-3">
                                <i class="bi bi-box-arrow-in-right me-1"></i> Check-in
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
        @empty
        <div class="col-12">
            <div class="alert alert-info text-center py-4 rounded-4">
                <i class="bi bi-info-circle fs-3 d-block mb-2"></i>
                Belum ada data toko mitra yang terdaftar.
            </div>
        </div>
        @endforelse
    </div>

</div>
@endsection
