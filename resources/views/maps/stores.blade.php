@extends('layouts.app')

@section('title', 'Peta Sebaran Toko Mitra - Kopi Distro')

@push('styles')
<!-- Leaflet CSS -->
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin=""/>

<style>
    #store-map {
        height: 600px;
        width: 100%;
        border-radius: 16px;
        z-index: 1;
        box-shadow: 0 10px 30px rgba(0,0,0,0.08);
    }
    .map-summary-card {
        background: #ffffff;
        border: 1px solid #e2e8f0;
        border-radius: 14px;
        padding: 1rem 1.25rem;
        box-shadow: 0 4px 12px rgba(0,0,0,0.02);
    }
    .legend-box {
        background: rgba(255, 255, 255, 0.95);
        backdrop-filter: blur(8px);
        border: 1px solid #cbd5e1;
        border-radius: 12px;
        padding: 0.85rem 1.15rem;
        box-shadow: 0 4px 15px rgba(0,0,0,0.1);
    }
    .legend-dot {
        width: 14px;
        height: 14px;
        border-radius: 50%;
        display: inline-block;
        margin-right: 6px;
    }
    .leaflet-popup-content-wrapper {
        border-radius: 14px !important;
        box-shadow: 0 10px 25px rgba(0,0,0,0.2) !important;
        padding: 0.25rem;
    }
    .leaflet-popup-content {
        font-family: inherit;
        margin: 12px 14px !important;
    }
</style>
@endpush

@section('content')
<div class="py-2">

    <!-- 1. HEADER & ACTIONS -->
    <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-3">
        <div>
            <h4 class="fw-bold mb-1">Peta Sebaran Toko Mitra</h4>
            <p class="text-muted small mb-0">Visualisasi geolokasi jaringan Alfamart, Indomaret, kafe, dan toko kelontong mitra kopi</p>
        </div>

        <div class="d-flex align-items-center gap-2">
            <a href="{{ route('maps.driver_tracking') }}" class="btn btn-sm btn-dark rounded-pill px-3">
                <i class="bi bi-bicycle me-1 text-warning"></i> Peta Jejak Sopir
            </a>
            <a href="{{ route('customers.index') }}" class="btn btn-sm btn-outline-secondary rounded-pill px-3">
                <i class="bi bi-list-ul me-1"></i> Tabel Toko
            </a>
        </div>
    </div>

    <!-- 2. SUMMARY METRICS -->
    <div class="row g-2 mb-3">
        <div class="col-6 col-md-3">
            <div class="map-summary-card">
                <span class="text-muted small d-block">Total Toko di Peta</span>
                <strong class="fs-4 text-dark">{{ $summary['total_mapped'] }} Toko</strong>
            </div>
        </div>
        <div class="col-6 col-md-2">
            <div class="map-summary-card">
                <span class="text-muted small d-block">Alfamart</span>
                <strong class="fs-4 text-danger">{{ $summary['alfamart_count'] }}</strong>
            </div>
        </div>
        <div class="col-6 col-md-2">
            <div class="map-summary-card">
                <span class="text-muted small d-block">Indomaret</span>
                <strong class="fs-4 text-primary">{{ $summary['indomaret_count'] }}</strong>
            </div>
        </div>
        <div class="col-6 col-md-2">
            <div class="map-summary-card">
                <span class="text-muted small d-block">Toko Menunggak</span>
                <strong class="fs-4 text-danger">{{ $summary['overdue_count'] }}</strong>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="map-summary-card">
                <span class="text-muted small d-block">Dikunjungi Hari Ini</span>
                <strong class="fs-4 text-success">{{ $summary['visited_today_count'] }} Toko</strong>
            </div>
        </div>
    </div>

    <!-- 3. FILTER BAR -->
    <div class="card border-0 shadow-sm rounded-4 p-3 mb-3">
        <form action="{{ route('maps.stores') }}" method="GET" class="row g-2 align-items-center">
            <div class="col-12 col-md-4">
                <div class="input-group input-group-sm">
                    <span class="input-group-text bg-light text-muted"><i class="bi bi-shop"></i></span>
                    <select name="chain" class="form-select form-select-sm" onchange="this.form.submit()">
                        <option value="all" {{ $selectedChain === 'all' ? 'selected' : '' }}>Semua Jenis Jaringan Toko</option>
                        <option value="alfamart" {{ $selectedChain === 'alfamart' ? 'selected' : '' }}>Khusus Alfamart</option>
                        <option value="indomaret" {{ $selectedChain === 'indomaret' ? 'selected' : '' }}>Khusus Indomaret</option>
                        <option value="kelontong" {{ $selectedChain === 'kelontong' ? 'selected' : '' }}>Khusus Toko Kelontong</option>
                        <option value="kafe" {{ $selectedChain === 'kafe' ? 'selected' : '' }}>Khusus Kafe / Kedai Kopi</option>
                    </select>
                </div>
            </div>

            <div class="col-12 col-md-4">
                <div class="input-group input-group-sm">
                    <span class="input-group-text bg-light text-muted"><i class="bi bi-filter"></i></span>
                    <select name="status" class="form-select form-select-sm" onchange="this.form.submit()">
                        <option value="all" {{ $selectedStatus === 'all' ? 'selected' : '' }}>Semua Status Toko</option>
                        <option value="overdue" {{ $selectedStatus === 'overdue' ? 'selected' : '' }}>🔴 Toko Menunggak Tagihan</option>
                        <option value="low_stock" {{ $selectedStatus === 'low_stock' ? 'selected' : '' }}>🟡 Stok Kritis (Perlu Restock)</option>
                        <option value="not_visited" {{ $selectedStatus === 'not_visited' ? 'selected' : '' }}>⚪ Belum Dikunjungi Hari Ini</option>
                        <option value="visited" {{ $selectedStatus === 'visited' ? 'selected' : '' }}>🔵 Sudah Dikunjungi Hari Ini</option>
                    </select>
                </div>
            </div>

            <div class="col-12 col-md-4 text-md-end">
                <span class="badge bg-light text-dark border px-3 py-2 rounded-pill small">
                    Menampilkan <strong>{{ count($stores) }}</strong> titik lokasi
                </span>
            </div>
        </form>
    </div>

    <!-- 4. PETA INTERAKTIF & LEGENDA -->
    <div class="position-relative mb-4">
        <div id="store-map"></div>

        <!-- Floating Legend -->
        <div class="position-absolute bottom-0 start-0 m-3 legend-box no-print" style="z-index: 1000;">
            <strong class="d-block small text-dark mb-1">Legenda Status Toko:</strong>
            <div class="d-flex flex-wrap gap-2 small">
                <div class="d-flex align-items-center">
                    <span class="legend-dot bg-success"></span> Aman
                </div>
                <div class="d-flex align-items-center">
                    <span class="legend-dot bg-warning"></span> Restock
                </div>
                <div class="d-flex align-items-center">
                    <span class="legend-dot bg-danger"></span> Menunggak
                </div>
                <div class="d-flex align-items-center">
                    <span class="legend-dot bg-primary"></span> Dikunjungi
                </div>
            </div>
        </div>
    </div>

</div>
@endsection

@push('scripts')
<!-- Leaflet JS -->
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>

<script>
    const storesData = @json($stores);

    // Titik pusat awal peta (ambil dari toko pertama atau default Denpasar/Bali)
    let defaultLat = -8.653400;
    let defaultLng = 115.228500;

    if (storesData.length > 0) {
        defaultLat = storesData[0].lat;
        defaultLng = storesData[0].lng;
    }

    const map = L.map('store-map').setView([defaultLat, defaultLng], 13);

    // OpenStreetMap Tile Layer (Gratis, Cepat, Tanpa API Key)
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        maxZoom: 19,
        attribution: '&copy; OpenStreetMap contributors &bull; Kopi Distro Fleet'
    }).addTo(map);

    // Fungsi warna icon pin marker
    function createCustomMarker(color) {
        const markerColors = {
            green: '#10b981',
            red: '#ef4444',
            orange: '#f59e0b',
            blue: '#3b82f6',
        };

        const hex = markerColors[color] || '#6b7280';

        const svgIcon = `
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="34" height="34">
                <path fill="${hex}" stroke="#ffffff" stroke-width="1.5" d="M12 2C8.13 2 5 5.13 5 9c0 5.25 7 13 7 13s7-7.75 7-13c0-3.87-3.13-7-7-7zm0 9.5c-1.38 0-2.5-1.12-2.5-2.5s1.12-2.5 2.5-2.5 2.5 1.12 2.5 2.5-1.12 2.5-2.5 2.5z"/>
            </svg>
        `;

        return L.divIcon({
            className: 'custom-leaflet-marker',
            html: svgIcon,
            iconSize: [34, 34],
            iconAnchor: [17, 34],
            popupAnchor: [0, -32]
        });
    }

    const bounds = [];

    // Tambahkan marker untuk tiap toko
    storesData.forEach(st => {
        const marker = L.marker([st.lat, st.lng], {
            icon: createCustomMarker(st.pin_color)
        }).addTo(map);

        bounds.push([st.lat, st.lng]);

        // Konten Popup Interaktif
        let badgeHtml = '';
        if (st.is_overdue) {
            badgeHtml = `<span class="badge bg-danger mb-1">MENUNGGAK: ${st.formatted_overdue}</span>`;
        } else if (st.is_low_stock) {
            badgeHtml = `<span class="badge bg-warning text-dark mb-1">PERLU RESTOCK: Sisa ${st.stock} pack</span>`;
        } else if (st.visited_today) {
            badgeHtml = `<span class="badge bg-primary mb-1">SUDAH DIKUNJUNGI HARI INI</span>`;
        } else {
            badgeHtml = `<span class="badge bg-success mb-1">STOK AMAN: ${st.stock} pack</span>`;
        }

        const popupContent = `
            <div style="min-width: 240px;">
                ${badgeHtml}
                <h6 class="fw-bold mb-1" style="color: #1e130c;">${st.name}</h6>
                <div class="small text-muted mb-2">${st.address}</div>
                <div class="d-flex justify-content-between small border-top pt-2 mb-2">
                    <span>Sisa Stok Rak:</span>
                    <strong>${st.stock} pack</strong>
                </div>
                <div class="d-flex justify-content-between small mb-3">
                    <span>Kontak PIC:</span>
                    <strong>${st.pic} (${st.phone})</strong>
                </div>
                <div class="d-flex gap-1">
                    <a href="${st.google_maps_url}" target="_blank" class="btn btn-sm btn-primary rounded-pill w-100 fw-bold">
                        <i class="bi bi-compass"></i> Buka Rute Maps
                    </a>
                    <a href="${st.print_qr_url}" target="_blank" class="btn btn-sm btn-light border rounded-pill" title="Cetak QR Toko">
                        <i class="bi bi-qr-code"></i>
                    </a>
                </div>
            </div>
        `;

        marker.bindPopup(popupContent);
    });

    // Auto-fit bounds peta agar semua marker terlihat
    if (bounds.length > 0) {
        map.fitBounds(bounds, { padding: [40, 40] });
    }
</script>
@endpush
