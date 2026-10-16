<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Stiker QR Code Rak - {{ $customer->business_name }}</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f1f5f9;
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif;
        }
        .sticker-card {
            max-width: 480px;
            margin: 2rem auto;
            background: #ffffff;
            border: 3px dashed #b45309;
            border-radius: 20px;
            padding: 2rem;
            box-shadow: 0 10px 25px rgba(0,0,0,0.08);
            text-align: center;
        }
        .qr-image {
            width: 220px;
            height: 220px;
            border: 4px solid #18181b;
            border-radius: 16px;
            padding: 8px;
            background: #ffffff;
            margin: 1rem auto;
            display: block;
        }
        @media print {
            body {
                background: #ffffff;
            }
            .no-print {
                display: none !important;
            }
            .sticker-card {
                box-shadow: none;
                margin: 0 auto;
                border: 2px solid #000000;
            }
        }
    </style>
</head>
<body>

<div class="container py-3">

    <div class="text-center no-print mb-3">
        <button onclick="window.print()" class="btn btn-warning fw-bold px-4 py-2 rounded-pill shadow-sm">
            <i class="bi bi-printer-fill me-1"></i> CETAK STIKER SEKARANG
        </button>
        <button onclick="window.close()" class="btn btn-outline-secondary px-3 py-2 rounded-pill ms-2">
            Tutup
        </button>
    </div>

    <div class="sticker-card">
        <!-- Brand Header -->
        <div class="d-flex align-items-center justify-content-center gap-2 mb-2">
            <i class="bi bi-cup-hot-fill text-warning fs-3"></i>
            <h5 class="fw-bold mb-0 text-dark">KOPI DISTRO</h5>
        </div>
        <span class="badge bg-warning text-dark text-uppercase fw-bold px-3 py-1 rounded-pill mb-3">
            STIKER IDENTITAS RAK DISPLAY
        </span>

        <!-- Store Name -->
        <h3 class="fw-bold text-dark mb-1">{{ $customer->business_name }}</h3>
        <p class="text-muted small mb-1">
            Kode Toko: <strong class="text-dark">{{ $customer->code }}</strong> &bull; {{ strtoupper($customer->store_chain ?? 'Retail') }}
        </p>
        <p class="text-muted small mb-3">
            {{ $customer->shipping_address ?? $customer->address }}
        </p>

        <!-- QR Code Image -->
        @php
            $qrUrl = "https://api.qrserver.com/v1/create-qr-code/?size=250x250&data=" . urlencode($customer->qr_token);
        @endphp
        <img src="{{ $qrUrl }}" alt="QR Code Toko" class="qr-image shadow-sm">

        <!-- QR Token String -->
        <div class="font-monospace fw-bold text-muted small mb-3">
            {{ $customer->qr_token }}
        </div>

        <!-- Instructions -->
        <div class="alert alert-light border small text-muted text-start mb-0 py-2 px-3 rounded-3">
            <i class="bi bi-info-circle text-warning me-1"></i>
            <strong>Instruksi Sopir / Sales:</strong> Pindai QR Code ini menggunakan Portal Sopir Kopi Distro saat tiba di toko untuk Check-In Kunjungan, Audit Sisa Stok Rak, & Restock Produk.
        </div>
    </div>

</div>

</body>
</html>
