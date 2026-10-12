@extends('layouts.app')

@section('title', 'Kopi Distro - Roastery & Distribusi Kopi Nusantara')

@push('styles')
<style>
    /* Hero Section Luxury Styling */
    .hero-luxury {
        position: relative;
        background: linear-gradient(135deg, rgba(14, 8, 5, 0.95) 0%, rgba(26, 15, 9, 0.88) 55%, rgba(45, 25, 15, 0.75) 100%),
                    url('{{ asset('images/hero-roastery.jpg') }}') center/cover no-repeat;
        color: #ffffff;
        padding: 5.5rem 0 4.5rem 0;
        overflow: hidden;
    }

    .hero-badge {
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        background: rgba(245, 158, 11, 0.15);
        color: #f59e0b;
        border: 1px solid rgba(245, 158, 11, 0.35);
        padding: 0.45rem 1rem;
        border-radius: 50px;
        font-size: 0.82rem;
        font-weight: 700;
        letter-spacing: 0.8px;
        text-transform: uppercase;
    }

    .hero-title {
        font-size: 3.25rem;
        font-weight: 800;
        line-height: 1.15;
        letter-spacing: -0.02em;
        color: #ffffff;
    }

    @media (max-width: 768px) {
        .hero-luxury {
            padding: 3.5rem 0 2.5rem 0 !important;
        }
        .hero-title {
            font-size: 1.85rem !important;
            line-height: 1.25 !important;
        }
        .hero-description {
            font-size: 0.95rem !important;
            line-height: 1.55 !important;
        }
        .hero-stat-card {
            padding: 0.85rem 0.65rem !important;
        }
        .hero-stat-card .fs-4 {
            font-size: 1.25rem !important;
        }
        .hero-stat-card small {
            font-size: 0.72rem !important;
        }
        .coffee-img-wrap {
            height: 220px !important;
        }
    }

    @media (max-width: 576px) {
        .hero-action-group {
            flex-direction: column !important;
            width: 100% !important;
        }
        .hero-action-group .btn {
            width: 100% !important;
            justify-content: center !important;
        }
    }

    .hero-description {
        font-size: 1.125rem;
        line-height: 1.65;
        color: rgba(255, 255, 255, 0.8);
    }

    .btn-gold {
        background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);
        color: #1a0f07;
        font-weight: 700;
        border: none;
        padding: 0.75rem 1.8rem;
        border-radius: 10px;
        box-shadow: 0 4px 20px rgba(245, 158, 11, 0.35);
        transition: all 0.25s ease;
    }
    .btn-gold:hover {
        background: linear-gradient(135deg, #fbbf24 0%, #f59e0b 100%);
        color: #1a0f07;
        transform: translateY(-2px);
        box-shadow: 0 8px 25px rgba(245, 158, 11, 0.45);
    }

    .hero-stat-card {
        background: rgba(255, 255, 255, 0.06);
        backdrop-filter: blur(12px);
        -webkit-backdrop-filter: blur(12px);
        border: 1px solid rgba(255, 255, 255, 0.12);
        border-radius: 14px;
        padding: 1.25rem;
        transition: transform 0.2s ease, border-color 0.2s ease;
    }
    .hero-stat-card:hover {
        transform: translateY(-3px);
        border-color: rgba(245, 158, 11, 0.5);
    }

    /* Product Card Styling */
    .coffee-card {
        background: #ffffff;
        border: 1px solid #e2e8f0;
        border-radius: 16px;
        overflow: hidden;
        transition: all 0.3s cubic-bezier(0.16, 1, 0.3, 1);
        display: flex;
        flex-direction: column;
        height: 100%;
    }
    .coffee-card:hover {
        transform: translateY(-6px);
        box-shadow: 0 16px 32px rgba(30, 19, 12, 0.1);
        border-color: #cbd5e1;
    }

    .coffee-img-wrap {
        position: relative;
        width: 100%;
        height: 260px;
        overflow: hidden;
        background-color: #f1f5f9;
    }
    .coffee-img-wrap img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        transition: transform 0.5s ease;
    }
    .coffee-card:hover .coffee-img-wrap img {
        transform: scale(1.06);
    }

    .coffee-badge-type {
        position: absolute;
        top: 12px;
        left: 12px;
        background: rgba(18, 11, 7, 0.85);
        color: #f59e0b;
        backdrop-filter: blur(8px);
        font-size: 0.75rem;
        font-weight: 700;
        padding: 0.35rem 0.75rem;
        border-radius: 30px;
        border: 1px solid rgba(245, 158, 11, 0.3);
    }

    .feature-icon-box {
        width: 56px;
        height: 56px;
        border-radius: 14px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.5rem;
    }
</style>
@endpush

@section('content')

<!-- ============================================== -->
<!-- 1. HERO SECTION LUXURY (ROASTERY & DISTRIBUSI) -->
<!-- ============================================== -->
<section class="hero-luxury">
    <div class="container position-relative">
        <div class="row align-items-center gy-5">
            <div class="col-lg-7">
                <div class="mb-3">
                    <span class="hero-badge">
                        <i class="bi bi-award-fill"></i> Roastery &amp; Distribusi Kopi Pilihan
                    </span>
                </div>
                <h1 class="hero-title mb-3">
                    Cita Rasa Kopi Asli Sulawesi Tengah, dari Petani ke Cangkir Anda.
                </h1>
                <p class="hero-description mb-4">
                    Menyediakan aneka produk kopi berkualitas khas Palu, Sulawesi Tengah: Kopi ATAPMERAH, Kopi Robusta Bintang Harapan, dan Kopi Gula Aren Bintang Sulawesi kemasan praktis sachet, renceng, hingga pack. Terintegrasi untuk belanja retail online dan distribusi grosir mitra warkop &amp; toko.
                </p>

                <!-- Tombol Aksi Utama -->
                <div class="d-flex flex-wrap gap-3 mb-5 hero-action-group">
                    <a href="{{ route('store.catalog') }}" class="btn btn-gold btn-lg d-flex align-items-center gap-2">
                        <i class="bi bi-shop fs-5"></i>
                        <span>Jelajahi Katalog Kopi</span>
                    </a>
                    
                    @auth
                        @if(auth()->user()->canAccessDashboard())
                            <a href="{{ route('dashboard') }}" class="btn btn-outline-light btn-lg px-4 d-flex align-items-center gap-2">
                                <i class="bi bi-speedometer2 text-warning fs-5"></i>
                                <span>Panel Dashboard ERP</span>
                            </a>
                        @else
                            <a href="{{ route('store.my_orders') }}" class="btn btn-outline-light btn-lg px-4 d-flex align-items-center gap-2">
                                <i class="bi bi-bag-check text-warning fs-5"></i>
                                <span>Pesanan Saya</span>
                            </a>
                        @endif
                    @else
                        <a href="{{ route('login') }}" class="btn btn-outline-light btn-lg px-4 d-flex align-items-center gap-2">
                            <i class="bi bi-box-arrow-in-right text-warning fs-5"></i>
                            <span>Masuk / Akses Bisnis</span>
                        </a>
                    @endauth
                </div>

                <!-- 4 Nilai Keunggulan Utama -->
                <div class="row g-3">
                    <div class="col-6 col-md-3">
                        <div class="hero-stat-card">
                            <div class="fw-bold fs-4 text-warning">100%</div>
                            <small class="text-white-50 d-block lh-sm">Kopi Asli Sulawesi Tengah</small>
                        </div>
                    </div>
                    <div class="col-6 col-md-3">
                        <div class="hero-stat-card">
                            <div class="fw-bold fs-4 text-warning">4 Produk</div>
                            <small class="text-white-50 d-block lh-sm">Varian Resmi Etalase</small>
                        </div>
                    </div>
                    <div class="col-6 col-md-3">
                        <div class="hero-stat-card">
                            <div class="fw-bold fs-4 text-warning">TOP 14</div>
                            <small class="text-white-50 d-block lh-sm">Termin Tempo Grosir B2B</small>
                        </div>
                    </div>
                    <div class="col-6 col-md-3">
                        <div class="hero-stat-card">
                            <div class="fw-bold fs-4 text-warning">Realtime</div>
                            <small class="text-white-50 d-block lh-sm">Audit Stok Anti-Minus</small>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Visual Showcase Kartu Unggulan -->
            <div class="col-lg-5 text-center">
                <div class="p-4 rounded-4 position-relative" style="background: rgba(255, 255, 255, 0.04); border: 1px solid rgba(255, 255, 255, 0.1); backdrop-filter: blur(16px);">
                    <div class="d-inline-flex align-items-center justify-content-center p-3 rounded-circle bg-warning bg-opacity-20 text-warning mb-3">
                        <i class="bi bi-cup-hot-fill fs-1"></i>
                    </div>
                    <h3 class="text-white fw-bold mb-2">Aroma Khas &amp; Gurih Mantap</h3>
                    <p class="text-white-50 small mb-4">
                        Diproduksi dengan resep tradisi pilihan dari Palu, Sulawesi Tengah. Kemasan rapi menjaga kesegaran rasa kopi hingga ke cangkir seduhan Anda.
                    </p>
                    <div class="d-flex justify-content-center flex-wrap gap-2">
                        <span class="badge bg-white bg-opacity-10 text-white px-3 py-2 border border-white border-opacity-25">10g Sachet</span>
                        <span class="badge bg-white bg-opacity-10 text-white px-3 py-2 border border-white border-opacity-25">20g Aren</span>
                        <span class="badge bg-white bg-opacity-10 text-white px-3 py-2 border border-white border-opacity-25">35g Kemasan</span>
                        <span class="badge bg-white bg-opacity-10 text-white px-3 py-2 border border-white border-opacity-25">135g Pack</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- ============================================== -->
<!-- 2. KOLEKSI KOPI KEMASAN UNGGULAN (REAL IMAGES) -->
<!-- ============================================== -->
<section class="py-5" style="background-color: #ffffff;">
    <div class="container py-4">
        <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-end mb-4">
            <div>
                <span class="text-uppercase fw-bold text-warning small tracking-wide">Produk Resmi Kemasan</span>
                <h2 class="fw-bold text-dark mb-1">Koleksi Kopi Etalase Resmi</h2>
                <p class="text-muted mb-0">Diproduksi di Palu, Sulawesi Tengah dengan cita rasa khas dan mutu terjamin.</p>
            </div>
            <div class="mt-3 mt-md-0">
                <a href="{{ route('store.catalog') }}" class="btn btn-outline-dark fw-semibold px-4">
                    Lihat Semua Katalog &rarr;
                </a>
            </div>
        </div>

        @if($isDevMode)
            <div class="alert alert-danger d-flex align-items-center rounded-3 p-3 mb-4 border-0" style="background-color: #fee2e2; border-left: 5px solid #ef4444 !important;">
                <i class="bi bi-shield-lock-fill fs-3 text-danger me-3 flex-shrink-0"></i>
                <div class="small text-danger">
                    <strong>Mode Pengembang Aktif:</strong> Etalase produk saat ini dalam mode pratinjau. Seluruh transaksi pembelian dinonaktifkan sementara.
                </div>
            </div>
        @endif

        <div class="row g-4">
            @foreach($featuredProducts as $prod)
                <div class="col-sm-6 col-lg-3">
                    <div class="coffee-card">
                        <!-- Foto Kemasan Asli HD -->
                        <div class="coffee-img-wrap">
                            <a href="{{ route('store.product', $prod->slug) }}">
                                <img src="{{ $prod->image_url }}" alt="{{ $prod->name }}" loading="lazy">
                            </a>
                            <span class="coffee-badge-type">
                                <i class="bi bi-tag-fill me-1"></i> {{ $prod->brand }}
                            </span>
                        </div>

                        <!-- Info Produk -->
                        <div class="p-3 d-flex flex-column flex-grow-1">
                            <div class="small text-muted mb-1">{{ $prod->category->name ?? 'Kopi Kemasan' }}</div>
                            <h5 class="fw-bold text-dark mb-2 fs-6">
                                <a href="{{ route('store.product', $prod->slug) }}" class="text-dark text-decoration-none">
                                    {{ $prod->name }}
                                </a>
                            </h5>
                            <p class="text-muted small mb-3 flex-grow-1" style="line-height: 1.45;">
                                {{ Str::limit($prod->description, 85) }}
                            </p>

                            <!-- Harga & Varian -->
                            <div class="pt-2 border-top mt-auto">
                                <div class="d-flex justify-content-between align-items-baseline mb-2">
                                    <small class="text-muted">Mulai dari</small>
                                    <span class="fw-bold text-dark fs-6">
                                        Rp {{ number_format($prod->variants->min('selling_price') ?? 0, 0, ',', '.') }}
                                    </span>
                                </div>
                                <div class="d-flex gap-2">
                                    @if($isDevMode)
                                        <a href="{{ route('store.product', $prod->slug) }}" class="btn btn-outline-dark btn-sm w-100 fw-bold shadow-sm">
                                            <i class="bi bi-eye me-1"></i> Lihat Spesifikasi
                                        </a>
                                    @else
                                        <a href="{{ route('store.product', $prod->slug) }}" class="btn btn-warning btn-sm w-100 fw-bold text-dark shadow-sm">
                                            <i class="bi bi-eye me-1"></i> Detail &amp; Beli
                                        </a>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</section>

<!-- ============================================== -->
<!-- 3. DUA JALUR PENJUALAN, SATU SISTEM PERSEDIAAN -->
<!-- ============================================== -->
<section class="py-5" style="background-color: var(--coffee-cream);">
    <div class="container py-4">
        <div class="text-center max-w-700 mx-auto mb-5">
            <span class="text-uppercase fw-bold text-warning small tracking-wide">Model Bisnis Terpadu</span>
            <h2 class="fw-bold text-dark mb-2">Dua Jalur Penjualan, Satu Sistem Persediaan</h2>
            <p class="text-muted">Satu database stok terpusat yang melayani pembeli retail perorangan sekaligus ratusan mitra kafe dan reseller tanpa risiko *overselling*.</p>
        </div>

        <div class="row g-4">
            <!-- Jalur 1: Retail Online B2C -->
            <div class="col-lg-6">
                <div class="card card-custom p-4 p-md-5 h-100 border-0 shadow-sm" style="background: #ffffff;">
                    <div class="d-flex align-items-center gap-3 mb-4">
                        <div class="feature-icon-box bg-warning bg-opacity-15 text-warning">
                            <i class="bi bi-shop"></i>
                        </div>
                        <div>
                            <span class="badge bg-warning text-dark px-3 py-1 fw-bold mb-1">JALUR 1 (B2C)</span>
                            <h3 class="fw-bold text-dark mb-0 fs-4">Penjualan Retail Online</h3>
                        </div>
                    </div>
                    <p class="text-muted mb-4">
                        Bagi pecinta kopi rumahan, penikmat seduhan manual brew, dan kebutuhan kantor harian. Pelanggan dapat berbelanja langsung secara online tanpa syarat minimum kuantitas pesanan.
                    </p>
                    <ul class="list-unstyled d-flex flex-column gap-2 mb-4">
                        <li class="d-flex align-items-center gap-2 text-dark">
                            <i class="bi bi-check-circle-fill text-success"></i>
                            <span>Tersedia kemasan praktis <strong>100 gram &amp; 250 gram</strong> siap seduh.</span>
                        </li>
                        <li class="d-flex align-items-center gap-2 text-dark">
                            <i class="bi bi-check-circle-fill text-success"></i>
                            <span>Pilihan bentuk seduhan: <strong>Biji Utuh</strong> atau <strong>Bubuk Halus/Sedang</strong>.</span>
                        </li>
                        <li class="d-flex align-items-center gap-2 text-dark">
                            <i class="bi bi-check-circle-fill text-success"></i>
                            <span>Checkout transparan dengan kalkulasi estimasi ongkos kirim.</span>
                        </li>
                    </ul>
                    <div class="mt-auto pt-3 border-top">
                        <a href="{{ route('store.catalog') }}" class="btn btn-outline-dark fw-bold px-4">
                            Mulai Belanja Online &rarr;
                        </a>
                    </div>
                </div>
            </div>

            <!-- Jalur 2: Grosir & Kemitraan B2B -->
            <div class="col-lg-6">
                <div class="card card-custom p-4 p-md-5 h-100 border-0 shadow-sm" style="background: #ffffff;">
                    <div class="d-flex align-items-center gap-3 mb-4">
                        <div class="feature-icon-box bg-primary bg-opacity-10 text-primary">
                            <i class="bi bi-buildings"></i>
                        </div>
                        <div>
                            <span class="badge bg-primary text-white px-3 py-1 fw-bold mb-1">JALUR 2 (B2B)</span>
                            <h3 class="fw-bold text-dark mb-0 fs-4">Penjualan Grosir Mitra</h3>
                        </div>
                    </div>
                    <p class="text-muted mb-4">
                        Dirancang khusus untuk mitra bisnis: kafe, *coffee shop*, resto, toko kelontong, dan *reseller* dengan fasilitas harga standar per SKU dan pembayaran tempo terkelola.
                    </p>
                    <ul class="list-unstyled d-flex flex-column gap-2 mb-4">
                        <li class="d-flex align-items-center gap-2 text-dark">
                            <i class="bi bi-check-circle-fill text-primary"></i>
                            <span>Kemasan ekonomis volume besar: <strong>500 gram &amp; 1 Kilogram</strong>.</span>
                        </li>
                        <li class="d-flex align-items-center gap-2 text-dark">
                            <i class="bi bi-check-circle-fill text-primary"></i>
                            <span>Plafon kredit otomatis (*Credit Limit*) &amp; termin pembayaran tempo (TOP 14 - 45 hari).</span>
                        </li>
                        <li class="d-flex align-items-center gap-2 text-dark">
                            <i class="bi bi-check-circle-fill text-primary"></i>
                            <span>Surat Jalan resmi 3 pihak, faktur penjualan, dan kartu piutang pelanggan (*Ledger*).</span>
                        </li>
                    </ul>
                    <div class="mt-auto pt-3 border-top">
                        <a href="{{ route('login') }}" class="btn btn-primary fw-bold px-4">
                            Masuk Portal Grosir &rarr;
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- ============================================== -->
<!-- 4. ALUR OPERASIONAL BISNIS LENGKAP & AMAN      -->
<!-- ============================================== -->
<section class="py-5 bg-white">
    <div class="container py-4">
        <div class="text-center max-w-700 mx-auto mb-5">
            <span class="text-uppercase fw-bold text-warning small tracking-wide">Tata Kelola Terstruktur</span>
            <h2 class="fw-bold text-dark mb-2">Alur Bisnis End-to-End Tanpa Celah</h2>
            <p class="text-muted">Dari penerimaan bahan baku biji kopi di gudang hingga pelunasan piutang dan laporan laba kotor HPP.</p>
        </div>

        <div class="row g-4 text-center">
            <div class="col-sm-6 col-lg-3">
                <div class="p-4 rounded-4 bg-light h-100 border">
                    <div class="d-inline-flex align-items-center justify-content-center p-3 rounded-circle bg-primary bg-opacity-10 text-primary mb-3" style="width: 60px; height: 60px;">
                        <i class="bi bi-truck fs-4"></i>
                    </div>
                    <h5 class="fw-bold text-dark mb-2">1. Pasokan &amp; Gudang</h5>
                    <p class="text-muted small mb-0">Penerimaan fisik PO dari supplier menambah stok otomatis dengan kartu mutasi real-time dan alasan opname audit.</p>
                </div>
            </div>

            <div class="col-sm-6 col-lg-3">
                <div class="p-4 rounded-4 bg-light h-100 border">
                    <div class="d-inline-flex align-items-center justify-content-center p-3 rounded-circle bg-success bg-opacity-10 text-success mb-3" style="width: 60px; height: 60px;">
                        <i class="bi bi-shield-check fs-4"></i>
                    </div>
                    <h5 class="fw-bold text-dark mb-2">2. Validasi Kredit</h5>
                    <p class="text-muted small mb-0">Pesanan grosir otomatis memvalidasi sisa plafon kredit mitra kafe sebelum pesanan dapat disetujui gudang.</p>
                </div>
            </div>

            <div class="col-sm-6 col-lg-3">
                <div class="p-4 rounded-4 bg-light h-100 border">
                    <div class="d-inline-flex align-items-center justify-content-center p-3 rounded-circle bg-warning bg-opacity-10 text-warning mb-3" style="width: 60px; height: 60px;">
                        <i class="bi bi-box-seam fs-4"></i>
                    </div>
                    <h5 class="fw-bold text-dark mb-2">3. Surat Jalan Resmi</h5>
                    <p class="text-muted small mb-0">Gudang menerbitkan Surat Jalan (SJ) berformat resmi dengan kolom serah terima Pengirim, Sopir, dan Penerima Toko.</p>
                </div>
            </div>

            <div class="col-sm-6 col-lg-3">
                <div class="p-4 rounded-4 bg-light h-100 border">
                    <div class="d-inline-flex align-items-center justify-content-center p-3 rounded-circle bg-info bg-opacity-10 text-info mb-3" style="width: 60px; height: 60px;">
                        <i class="bi bi-graph-up-arrow fs-4"></i>
                    </div>
                    <h5 class="fw-bold text-dark mb-2">4. Keuangan &amp; Laba</h5>
                    <p class="text-muted small mb-0">Faktur penagihan, kuitansi resmi, pemulihan plafon instan pasca-pelunasan, dan laporan laba kotor berbasis HPP aktual.</p>
                </div>
            </div>
        </div>
    </div>
</section>

@endsection
