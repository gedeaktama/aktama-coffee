@extends('layouts.app')

@section('title', 'Scan QR Code Toko - Kopi Distro')

@push('styles')
<style>
    .scanner-container {
        max-width: 520px;
        margin: 0 auto;
    }
    .scanner-box {
        position: relative;
        background: #000000;
        border-radius: 20px;
        overflow: hidden;
        border: 2px solid #334155;
        box-shadow: 0 15px 35px rgba(0, 0, 0, 0.35);
        min-height: 320px;
    }
    #reader {
        width: 100% !important;
        border: none !important;
    }
    #reader video {
        object-fit: cover !important;
        border-radius: 18px;
    }
    .scan-reticle {
        position: absolute;
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%);
        width: 220px;
        height: 220px;
        border: 2px solid rgba(245, 158, 11, 0.85);
        border-radius: 16px;
        box-shadow: 0 0 0 9999px rgba(0, 0, 0, 0.45);
        pointer-events: none;
        z-index: 10;
    }
    .scan-laser {
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        height: 3px;
        background: #f59e0b;
        box-shadow: 0 0 8px #f59e0b;
        animation: scanMove 2s infinite ease-in-out;
    }
    @keyframes scanMove {
        0% { top: 0%; opacity: 0.2; }
        50% { top: 100%; opacity: 1; }
        100% { top: 0%; opacity: 0.2; }
    }
    .gps-badge {
        background: #ffffff;
        border: 1px solid #e2e8f0;
        border-radius: 12px;
        padding: 0.75rem 1rem;
    }
</style>
@endpush

@section('content')
<div class="scanner-container py-2">

    <!-- Top Navigation -->
    <div class="d-flex align-items-center justify-content-between mb-3">
        <a href="{{ route('driver.dashboard') }}" class="btn btn-sm btn-outline-secondary rounded-pill px-3">
            <i class="bi bi-arrow-left me-1"></i> Kembali
        </a>
        <h5 class="fw-bold mb-0">Scan Toko Tiba</h5>
        <span class="badge bg-warning text-dark px-3 py-1 rounded-pill fw-bold">
            <i class="bi bi-camera me-1"></i> LIVE
        </span>
    </div>

    <!-- Peringatan Status GPS -->
    <div class="gps-badge mb-3 shadow-sm d-flex align-items-center justify-content-between">
        <div class="d-flex align-items-center gap-2">
            <i class="bi bi-geo-alt-fill text-danger fs-5" id="gps-icon"></i>
            <div>
                <strong class="d-block small text-dark" id="gps-status-text">Mendeteksi Posisi GPS HP...</strong>
                <span class="text-muted" style="font-size: 0.75rem;" id="gps-coords-text">Mohon izinkan akses lokasi (GPS) browser</span>
            </div>
        </div>
        <span class="badge bg-secondary-subtle text-secondary" id="gps-accuracy-badge">GPS Mencari</span>
    </div>

    <!-- Scanner Box Viewfinder -->
    <div class="scanner-box mb-3 position-relative">
        <div id="reader"></div>
        <div class="scan-reticle" id="reticle">
            <div class="scan-laser"></div>
        </div>
    </div>

    <!-- Instruction Note -->
    <div class="alert alert-light border shadow-sm text-center py-2 px-3 rounded-4 mb-3 small text-muted">
        <i class="bi bi-info-circle text-warning me-1"></i>
        Arahkan kamera ke stiker <strong>QR Code Toko</strong> yang tertempel di rak kopi atau meja kasir toko.
    </div>

    <!-- Form Tersembunyi untuk Submit Check-in Otomatis saat QR Terbaca -->
    <form id="checkin-form" action="{{ route('driver.checkin') }}" method="POST">
        @csrf
        <input type="hidden" name="qr_token" id="form-qr-token">
        <input type="hidden" name="latitude" id="form-latitude">
        <input type="hidden" name="longitude" id="form-longitude">
        <input type="hidden" name="visit_purpose" value="audit_delivery">
    </form>

    <!-- Fallback Manual: Pilih Toko Jika Kamera HP Tidak Bisa Digunakan -->
    <div class="card border-0 shadow-sm rounded-4 mt-4">
        <div class="card-body p-3">
            <div class="d-flex align-items-center justify-content-between mb-2">
                <span class="small fw-bold text-muted text-uppercase">
                    <i class="bi bi-hand-index me-1"></i> Alternatif Manual
                </span>
                <span class="text-muted" style="font-size: 0.75rem;">Jika kamera bermasalah</span>
            </div>

            <form action="{{ route('driver.checkin') }}" method="POST">
                @csrf
                <input type="hidden" name="latitude" id="manual-latitude">
                <input type="hidden" name="longitude" id="manual-longitude">
                <div class="input-group">
                    <select name="customer_id" class="form-select" required>
                        <option value="">-- Pilih Toko yang Sedang Dikunjungi --</option>
                        @foreach($stores as $st)
                            <option value="{{ $st->id }}">
                                {{ $st->business_name }} ({{ strtoupper($st->store_chain ?? 'Toko') }})
                            </option>
                        @endforeach
                    </select>
                    <button type="submit" class="btn btn-warning fw-bold px-3">
                        Check-in <i class="bi bi-arrow-right"></i>
                    </button>
                </div>
            </form>
        </div>
    </div>

</div>
@endsection

@push('scripts')
<!-- Library HTML5 QR Code Scanner dari CDN -->
<script src="https://unpkg.com/html5-qrcode" type="text/javascript"></script>

<script>
    let currentLat = null;
    let currentLng = null;
    let isSubmitting = false;

    // 1. Dapatkan Posisi Geolokasi GPS HP Sopir
    function fetchGPSLocation() {
        const gpsStatus = document.getElementById('gps-status-text');
        const gpsCoords = document.getElementById('gps-coords-text');
        const gpsBadge = document.getElementById('gps-accuracy-badge');
        const gpsIcon = document.getElementById('gps-icon');

        if ("geolocation" in navigator) {
            navigator.geolocation.getCurrentPosition(
                (position) => {
                    currentLat = position.coords.latitude;
                    currentLng = position.coords.longitude;
                    const accuracy = Math.round(position.coords.accuracy);

                    gpsStatus.textContent = "GPS Terkunci (Akurat)";
                    gpsCoords.textContent = `${currentLat.toFixed(5)}, ${currentLng.toFixed(5)} (Akurasi: ±${accuracy}m)`;
                    gpsBadge.className = "badge bg-success-subtle text-success";
                    gpsBadge.textContent = "GPS Aktif";
                    gpsIcon.className = "bi bi-geo-alt-fill text-success fs-5";

                    document.getElementById('form-latitude').value = currentLat;
                    document.getElementById('form-longitude').value = currentLng;
                    document.getElementById('manual-latitude').value = currentLat;
                    document.getElementById('manual-longitude').value = currentLng;
                },
                (error) => {
                    console.warn("GPS Warning:", error.message);
                    gpsStatus.textContent = "GPS Kurang Akurat";
                    gpsCoords.textContent = "Tetap bisa scan, koordinat default digunakan.";
                    gpsBadge.className = "badge bg-warning-subtle text-warning";
                    gpsBadge.textContent = "GPS Terbatas";
                },
                { enableHighAccuracy: true, timeout: 8000, maximumAge: 0 }
            );
        } else {
            gpsStatus.textContent = "GPS Tidak Didukung Browser";
            gpsCoords.textContent = "Gunakan browser Chrome / Safari mobile";
        }
    }

    // Panggil GPS saat halaman dibuka
    fetchGPSLocation();

    // 2. Inisialisasi Scanner Kamera
    function onScanSuccess(decodedText, decodedResult) {
        if (isSubmitting) return;
        isSubmitting = true;

        console.log("QR Code Terbaca:", decodedText);

        // Feedback Getar (jika didukung HP)
        if (navigator.vibrate) {
            navigator.vibrate(200);
        }

        // Tampilkan feedback visual
        document.getElementById('gps-status-text').textContent = "QR Code Terdeteksi!";
        document.getElementById('gps-coords-text').textContent = "Menghubungkan ke toko...";

        // Isi form dan submit otomatis
        document.getElementById('form-qr-token').value = decodedText;
        document.getElementById('checkin-form').submit();
    }

    function onScanFailure(error) {
        // Biarkan scanning terus berjalan
    }

    const html5QrcodeScanner = new Html5Qrcode("reader");
    const config = { 
        fps: 15, 
        qrbox: { width: 220, height: 220 },
        aspectRatio: 1.0
    };

    // Jalankan kamera belakang (environment) secara otomatis
    html5QrcodeScanner.start(
        { facingMode: "environment" },
        config,
        onScanSuccess,
        onScanFailure
    ).catch(err => {
        console.warn("Kamera tidak dapat dimulai:", err);
        document.getElementById('reader').innerHTML = `
            <div class="text-center text-white py-5 px-3">
                <i class="bi bi-camera-video-off fs-1 text-warning mb-2 d-block"></i>
                <h6 class="fw-bold">Kamera Belum Aktif</h6>
                <p class="small text-muted mb-0">Klik tombol di browser untuk memberikan izin akses kamera, atau gunakan pilihan manual di bawah.</p>
            </div>
        `;
        document.getElementById('reticle').style.display = 'none';
    });
</script>
@endpush
