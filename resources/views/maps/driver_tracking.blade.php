@extends('layouts.app')

@section('title', 'Peta Jejak Kunjungan Sopir - Kopi Distro')

@push('styles')
<!-- Leaflet CSS -->
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin=""/>

<style>
    #driver-map {
        height: 600px;
        width: 100%;
        border-radius: 16px;
        box-shadow: 0 10px 30px rgba(0,0,0,0.08);
    }
    .timeline-card {
        max-height: 600px;
        overflow-y: auto;
    }
    .timeline-item {
        position: relative;
        padding-left: 28px;
        padding-bottom: 20px;
        border-left: 2px dashed #cbd5e1;
    }
    .timeline-item:last-child {
        border-left: 2px solid transparent;
        padding-bottom: 0;
    }
    .timeline-dot {
        position: absolute;
        left: -11px;
        top: 0;
        width: 20px;
        height: 20px;
        border-radius: 50%;
        background: #f59e0b;
        color: #ffffff;
        font-size: 0.7rem;
        font-weight: bold;
        display: flex;
        align-items: center;
        justify-content: center;
        box-shadow: 0 0 0 4px #fef3c7;
    }
</style>
@endpush

@section('content')
<div class="py-2">

    <!-- 1. HEADER & ACTIONS -->
    <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-3">
        <div>
            <h4 class="fw-bold mb-1">Peta Jejak & Rute Kunjungan Sopir</h4>
            <p class="text-muted small mb-0">Pantau perjalanan armada lapangan dari toko ke toko secara transparan dan terverifikasi GPS</p>
        </div>

        <div class="d-flex align-items-center gap-2">
            <a href="{{ route('maps.stores') }}" class="btn btn-sm btn-outline-secondary rounded-pill px-3">
                <i class="bi bi-geo-alt me-1"></i> Peta Sebaran Toko
            </a>
            <a href="{{ route('driver.visits.history') }}" class="btn btn-sm btn-outline-secondary rounded-pill px-3">
                <i class="bi bi-clock-history me-1"></i> Log Kunjungan
            </a>
        </div>
    </div>

    <!-- 2. FILTER TANGGAL & SOPIR -->
    <div class="card border-0 shadow-sm rounded-4 p-3 mb-3">
        <form action="{{ route('maps.driver_tracking') }}" method="GET" class="row g-2 align-items-center">
            <div class="col-12 col-md-4">
                <div class="input-group input-group-sm">
                    <span class="input-group-text bg-light text-muted"><i class="bi bi-person-badge"></i></span>
                    <select name="driver_id" class="form-select form-select-sm" onchange="this.form.submit()">
                        <option value="all" {{ $driverId === 'all' ? 'selected' : '' }}>Semua Sopir / Sales Lapangan</option>
                        @foreach($drivers as $d)
                            <option value="{{ $d->id }}" {{ $driverId == $d->id ? 'selected' : '' }}>
                                {{ $d->name }} ({{ ucfirst($d->role) }})
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="col-12 col-md-3">
                <div class="input-group input-group-sm">
                    <span class="input-group-text bg-light text-muted"><i class="bi bi-calendar3"></i></span>
                    <input type="date" name="date" class="form-control form-control-sm" value="{{ $date }}" onchange="this.form.submit()">
                </div>
            </div>

            <div class="col-12 col-md-5 text-md-end">
                <span class="badge bg-warning-subtle text-warning border border-warning px-3 py-2 rounded-pill small">
                    <i class="bi bi-check2-circle me-1"></i> <strong>{{ $stats['total_visits'] }}</strong> Toko Dikunjungi &bull; <strong>{{ $stats['total_sold_qty'] }}</strong> Pack Laku (Rp {{ number_format($stats['total_sold_amount'], 0, ',', '.') }})
                </span>
            </div>
        </form>
    </div>

    <!-- 3. MAIN MAP & TIMELINE -->
    <div class="row g-3 mb-4">
        <!-- Peta Jejak Rute Leaflet -->
        <div class="col-12 col-lg-8">
            <div id="driver-map"></div>
        </div>

        <!-- Panel Timeline Kunjungan -->
        <div class="col-12 col-lg-4">
            <div class="card border-0 shadow-sm rounded-4 timeline-card p-3">
                <div class="d-flex justify-content-between align-items-center mb-3 pb-2 border-bottom">
                    <h6 class="fw-bold mb-0">Timeline Kunjungan Hari Ini</h6>
                    <small class="text-muted">{{ \Carbon\Carbon::parse($date)->translatedFormat('d F Y') }}</small>
                </div>

                @forelse($visits as $v)
                <div class="timeline-item">
                    <div class="timeline-dot">{{ $v['index'] }}</div>
                    <div class="d-flex justify-content-between align-items-start mb-1">
                        <strong class="text-dark small">{{ $v['store_name'] }}</strong>
                        <span class="badge bg-light text-dark border small" style="font-size: 0.7rem;">
                            {{ $v['check_in_time'] }} WITA
                        </span>
                    </div>
                    <p class="text-muted small mb-1" style="font-size: 0.75rem;">
                        <i class="bi bi-person me-1"></i> {{ $v['driver_name'] }} &bull; Durasi: {{ $v['duration'] }}
                    </p>
                    <div class="d-flex gap-1 mb-1">
                        <span class="badge bg-success-subtle text-success small" style="font-size: 0.7rem;">
                            Laku: {{ $v['sold_qty'] }} pack ({{ $v['formatted_sold_amount'] }})
                        </span>
                        @if($v['restock_qty'] > 0)
                        <span class="badge bg-warning-subtle text-warning small" style="font-size: 0.7rem;">
                            +{{ $v['restock_qty'] }} Restock
                        </span>
                        @endif
                    </div>
                    @if($v['distance_meters'] !== null)
                    <div style="font-size: 0.7rem;" class="{{ $v['is_geofence_valid'] ? 'text-success' : 'text-danger' }}">
                        <i class="bi bi-geo-alt"></i> Jarak GPS: {{ $v['distance_meters'] }}m {{ $v['is_geofence_valid'] ? '(Valid)' : '(Jauh dari toko)' }}
                    </div>
                    @endif
                </div>
                @empty
                <div class="text-center py-5 text-muted">
                    <i class="bi bi-bicycle fs-1 d-block mb-2 text-secondary"></i>
                    Belum ada riwayat kunjungan sopir pada tanggal ini.
                </div>
                @endforelse
            </div>
        </div>
    </div>

</div>
@endsection

@push('scripts')
<!-- Leaflet JS -->
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>

<script>
    const visitsData = @json($visits);

    let defaultLat = -8.653400;
    let defaultLng = 115.228500;

    if (visitsData.length > 0) {
        defaultLat = visitsData[0].lat;
        defaultLng = visitsData[0].lng;
    }

    const map = L.map('driver-map').setView([defaultLat, defaultLng], 13);

    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        maxZoom: 19,
        attribution: '&copy; OpenStreetMap contributors &bull; Kopi Distro Fleet Tracker'
    }).addTo(map);

    // Numbered Marker Icon
    function createNumberedMarker(number) {
        return L.divIcon({
            className: 'custom-numbered-marker',
            html: `
                <div style="
                    background: #f59e0b;
                    color: #ffffff;
                    width: 32px;
                    height: 32px;
                    border-radius: 50%;
                    border: 3px solid #ffffff;
                    box-shadow: 0 4px 12px rgba(0,0,0,0.3);
                    display: flex;
                    align-items: center;
                    justify-content: center;
                    font-weight: 800;
                    font-size: 13px;
                ">
                    ${number}
                </div>
            `,
            iconSize: [32, 32],
            iconAnchor: [16, 16],
            popupAnchor: [0, -18]
        });
    }

    const pathCoordinates = [];
    const bounds = [];

    visitsData.forEach((v, idx) => {
        const marker = L.marker([v.lat, v.lng], {
            icon: createNumberedMarker(v.index)
        }).addTo(map);

        pathCoordinates.push([v.lat, v.lng]);
        bounds.push([v.lat, v.lng]);

        const popupContent = `
            <div style="min-width: 220px;">
                <span class="badge bg-warning text-dark mb-1">STOP #${v.index} &bull; ${v.check_in_time} WITA</span>
                <h6 class="fw-bold mb-1" style="color: #1e130c;">${v.store_name}</h6>
                <div class="small text-muted mb-2">${v.address}</div>
                <div class="small border-top pt-2">
                    <div>Sopir: <strong>${v.driver_name}</strong></div>
                    <div>Laku: <strong class="text-success">${v.sold_qty} pack (${v.formatted_sold_amount})</strong></div>
                    <div>Restock: <strong class="text-warning">+${v.restock_qty} pack</strong></div>
                    <div>Durasi: <strong>${v.duration}</strong></div>
                </div>
            </div>
        `;

        marker.bindPopup(popupContent);
    });

    // Gambar Garis Polyline Jejak Rute Perjalanan Karyawan
    if (pathCoordinates.length > 1) {
        const routeLine = L.polyline(pathCoordinates, {
            color: '#b45309',
            weight: 4,
            opacity: 0.8,
            dashArray: '8, 8',
            smoothFactor: 1
        }).addTo(map);
    }

    if (bounds.length > 0) {
        map.fitBounds(bounds, { padding: [40, 40] });
    }
</script>
@endpush
