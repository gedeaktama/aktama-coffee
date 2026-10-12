@extends('layouts.app')

@section('title', 'Pengaturan Sistem & Mode Pengembang')

@section('content')
<div class="container-fluid py-2">
    <!-- Header Page -->
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-4">
        <div>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-1 small">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}" class="text-decoration-none">Dashboard ERP</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Pengaturan Sistem</li>
                </ol>
            </nav>
            <h1 class="h3 fw-bold text-dark mb-1">
                <i class="bi bi-sliders me-2 text-warning"></i> Pengaturan Sistem &amp; Mode Pengembang
            </h1>
            <p class="text-muted small mb-0">Kontrol operasional toko online, pembatasan transaksi etalase, dan konfigurasi server.</p>
        </div>

        <div class="d-flex gap-2">
            <a href="{{ route('store.catalog') }}" target="_blank" class="btn btn-outline-secondary btn-sm d-flex align-items-center gap-2 shadow-sm">
                <i class="bi bi-box-arrow-up-right"></i>
                <span>Cek Tampilan Toko</span>
            </a>
            <a href="{{ route('dashboard') }}" class="btn btn-light border btn-sm d-flex align-items-center gap-2 shadow-sm">
                <i class="bi bi-speedometer2"></i>
                <span>Kembali ke Dashboard</span>
            </a>
        </div>
    </div>

    <!-- Alert Status -->
    <div class="row g-4 mb-4">
        <div class="col-lg-8">
            <!-- CARD UTAMA: ON/OFF MODE PENGEMBANG -->
            <div class="card border-0 shadow-sm rounded-4 overflow-hidden mb-4" style="border-left: 6px solid {{ $isDevMode ? '#ef4444' : '#10b981' }} !important;">
                <div class="card-body p-4">
                    <div class="d-flex flex-column flex-sm-row justify-content-between align-items-sm-start gap-3 pb-3 border-bottom">
                        <div>
                            <div class="d-flex align-items-center gap-2 mb-2">
                                <span class="badge rounded-pill px-3 py-2 fs-6 fw-bold {{ $isDevMode ? 'bg-danger text-white' : 'bg-success text-white' }}">
                                    <i class="bi {{ $isDevMode ? 'bi-shield-lock-fill' : 'bi-check-circle-fill' }} me-1"></i>
                                    {{ $isDevMode ? 'MODE PENGEMBANG: AKTIF (ON)' : 'MODE NORMAL: AKTIF (OFF)' }}
                                </span>
                                @if($isDevMode)
                                    <span class="badge bg-warning text-dark px-2 py-1 small fw-semibold">
                                        <i class="bi bi-cone-striped me-1"></i> Pembelian Dinonaktifkan
                                    </span>
                                @else
                                    <span class="badge bg-success bg-opacity-10 text-success border border-success border-opacity-25 px-2 py-1 small fw-semibold">
                                        <i class="bi bi-cart-check me-1"></i> Toko Siap Transaksi
                                    </span>
                                @endif
                            </div>
                            <h4 class="fw-bold text-dark mb-1">Saklar Mode Pengembang (Developer Mode)</h4>
                            <p class="text-muted small mb-0">
                                Gunakan saklar ini untuk mengunci seluruh transaksi pembelian pada etalase toko online ketika sedang melakukan uji coba, audit sistem, atau pemeliharaan katalog.
                            </p>
                        </div>

                        <!-- Form Toggle On/Off -->
                        <form method="POST" action="{{ route('settings.dev_mode.toggle') }}" class="m-0 flex-shrink-0">
                            @csrf
                            <input type="hidden" name="state" value="{{ $isDevMode ? '0' : '1' }}">
                            <button type="submit" class="btn {{ $isDevMode ? 'btn-success' : 'btn-danger' }} px-4 py-2 fw-bold shadow-sm d-flex align-items-center gap-2">
                                @if($isDevMode)
                                    <i class="bi bi-toggle-on fs-5"></i>
                                    <span>Matikan Mode Pengembang (Buka Toko)</span>
                                @else
                                    <i class="bi bi-toggle-off fs-5"></i>
                                    <span>Aktifkan Mode Pengembang (Kunci Pembelian)</span>
                                @endif
                            </button>
                        </form>
                    </div>

                    <!-- Penjelasan Dampak Langsung -->
                    <div class="mt-4">
                        <h6 class="fw-bold text-dark mb-3">
                            <i class="bi bi-info-circle me-1 text-primary"></i> Dampak Mode Pengembang Terhadap Sistem:
                        </h6>
                        <div class="row g-3">
                            <div class="col-md-6">
                                <div class="p-3 rounded-3 h-100 {{ $isDevMode ? 'bg-danger bg-opacity-10 border border-danger border-opacity-25' : 'bg-light border' }}">
                                    <div class="d-flex align-items-start gap-2">
                                        <i class="bi {{ $isDevMode ? 'bi-x-octagon-fill text-danger' : 'bi-check2-circle text-muted' }} fs-5 mt-n1"></i>
                                        <div>
                                            <strong class="d-block small text-dark mb-1">Fitur Tambah ke Keranjang &amp; Beli</strong>
                                            <p class="small text-secondary mb-0">
                                                @if($isDevMode)
                                                    <span class="text-danger fw-bold">DINONAKTIFKAN.</span> Seluruh tombol beli pada Beranda, Katalog, dan Detail Produk dikunci dengan label <em>"Pembelian Dinonaktifkan"</em>.
                                                @else
                                                    <span class="text-success fw-bold">AKTIF NORMAL.</span> Pengunjung bebas memilih varian kopi dan menambahkannya ke keranjang belanja.
                                                @endif
                                            </p>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="p-3 rounded-3 h-100 {{ $isDevMode ? 'bg-danger bg-opacity-10 border border-danger border-opacity-25' : 'bg-light border' }}">
                                    <div class="d-flex align-items-start gap-2">
                                        <i class="bi {{ $isDevMode ? 'bi-shield-slash-fill text-danger' : 'bi-shield-check text-muted' }} fs-5 mt-n1"></i>
                                        <div>
                                            <strong class="d-block small text-dark mb-1">Keamanan Jalur Checkout (Server-Side)</strong>
                                            <p class="small text-secondary mb-0">
                                                @if($isDevMode)
                                                    <span class="text-danger fw-bold">DIBLOKIR TOTAL.</span> Bila ada yang mencoba mengakses URL <code>/checkout</code> atau mengirim payload form keranjang, sistem otomatis menolaknya dengan pesan peringatan.
                                                @else
                                                    <span class="text-success fw-bold">DAPAT DIAKSES.</span> Pelanggan dapat menyelesaikan proses pemesanan retail online.
                                                @endif
                                            </p>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="p-3 rounded-3 h-100 bg-light border">
                                    <div class="d-flex align-items-start gap-2">
                                        <i class="bi bi-eye-fill text-warning fs-5 mt-n1"></i>
                                        <div>
                                            <strong class="d-block small text-dark mb-1">Visibilitas Etalase &amp; Informasi Produk</strong>
                                            <p class="small text-secondary mb-0">
                                                Katalog kopi, galeri foto kemasan HD, berat bersih, produsen, dan resep seduh <strong>tetap dapat dibaca</strong> oleh pengunjung dalam mode pratinjau (preview mode).
                                            </p>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="p-3 rounded-3 h-100 bg-light border">
                                    <div class="d-flex align-items-start gap-2">
                                        <i class="bi bi-person-badge-fill text-info fs-5 mt-n1"></i>
                                        <div>
                                            <strong class="d-block small text-dark mb-1">Hak Kelola Owner &amp; Admin</strong>
                                            <p class="small text-secondary mb-0">
                                                Hanya pengguna dengan wewenang <strong>Owner</strong> dan <strong>Admin</strong> yang dapat mengubah saklar ini. Staf lain atau pelanggan tidak dapat mengubah pengaturan.
                                            </p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Footer Card -->
                <div class="card-footer bg-light px-4 py-3 border-top d-flex flex-column flex-sm-row justify-content-between align-items-center gap-2">
                    <small class="text-muted">
                        <i class="bi bi-clock-history me-1"></i> Terakhir diubah: {{ now()->translatedFormat('d F Y, H:i') }} WIB
                    </small>
                    <div class="d-flex align-items-center gap-2">
                        <span class="small fw-semibold text-secondary">Status saat ini:</span>
                        <span class="badge {{ $isDevMode ? 'bg-danger' : 'bg-success' }}">
                            {{ $isDevMode ? 'LOCKED / DISABLED' : 'OPEN / OPERATIONAL' }}
                        </span>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <!-- CARD INFO SISTEM & LINGKUNGAN -->
            <div class="card border-0 shadow-sm rounded-4 mb-4">
                <div class="card-header bg-white border-bottom py-3">
                    <h5 class="fw-bold text-dark mb-0 fs-6">
                        <i class="bi bi-hdd-network me-2 text-warning"></i> Ringkasan Lingkungan Server
                    </h5>
                </div>
                <div class="card-body p-3">
                    <ul class="list-group list-group-flush small">
                        <li class="list-group-item d-flex justify-content-between align-items-center px-0 py-2">
                            <span class="text-muted">Aplikasi</span>
                            <strong class="text-dark">Kopi Distro v1.0</strong>
                        </li>
                        <li class="list-group-item d-flex justify-content-between align-items-center px-0 py-2">
                            <span class="text-muted">Framework</span>
                            <span class="badge bg-danger bg-opacity-10 text-danger fw-semibold">Laravel v{{ Illuminate\Foundation\Application::VERSION }}</span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between align-items-center px-0 py-2">
                            <span class="text-muted">PHP Engine</span>
                            <span class="badge bg-primary bg-opacity-10 text-primary fw-semibold">PHP {{ PHP_VERSION }}</span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between align-items-center px-0 py-2">
                            <span class="text-muted">Database Server</span>
                            <span class="badge bg-info bg-opacity-10 text-info fw-semibold">MySQL / MariaDB</span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between align-items-center px-0 py-2">
                            <span class="text-muted">Lingkungan Web</span>
                            <span class="badge bg-success bg-opacity-10 text-success fw-semibold">XAMPP Apache Windows</span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between align-items-center px-0 py-2">
                            <span class="text-muted">Mode Pengembang</span>
                            <span class="badge {{ $isDevMode ? 'bg-danger' : 'bg-secondary' }}">
                                {{ $isDevMode ? 'ON' : 'OFF' }}
                            </span>
                        </li>
                    </ul>
                </div>
            </div>

            <!-- CARD PETUNJUK OPERASIONAL -->
            <div class="card border-0 shadow-sm rounded-4 bg-primary bg-opacity-10 border border-primary border-opacity-25">
                <div class="card-body p-4">
                    <h6 class="fw-bold text-primary mb-2">
                        <i class="bi bi-lightbulb-fill me-1"></i> Kapan Mode Pengembang Digunakan?
                    </h6>
                    <ul class="small text-secondary ps-3 mb-0" style="line-height: 1.6;">
                        <li>Saat memasukkan produk baru atau merubah harga kemasan agar tidak terjadi salah beli sebelum data valid.</li>
                        <li>Saat tim sedang melakukan stock opname fisik gudang bulanan.</li>
                        <li>Saat mendemonstrasikan sistem ke mitra bisnis tanpa membuat order riil.</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
