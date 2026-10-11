@extends('layouts.app')

@section('title', 'Kopi Distro - Distribusi & Penjualan Kopi Kemasan')

@section('content')
<!-- Hero Section -->
<section class="py-5 text-white" style="background: linear-gradient(135deg, var(--coffee-dark) 0%, #2f1b13 100%);">
    <div class="container py-4">
        <div class="row align-items-center gy-4">
            <div class="col-lg-7">
                <span class="badge bg-warning text-dark px-3 py-2 fw-bold text-uppercase mb-3" style="letter-spacing: 0.5px;">
                    <i class="bi bi-star-fill me-1"></i> Kopi Kemasan Premium
                </span>
                <h1 class="display-5 fw-bold mb-3 text-white">
                    Distribusi & Penjualan Kopi Pilihan Untuk Kafe, Toko & Retail
                </h1>
                <p class="lead text-white-50 mb-4">
                    Menyediakan varian Kopi Arabika dan Robusta kemasan berkualitas (100g, 250g, 500g, 1kg). Melayani pemesanan langsung untuk konsumen retail serta sistem grosir terpadu untuk kafe, toko kelontong, dan reseller kopi.
                </p>
                <div class="d-flex flex-wrap gap-2">
                    <a href="{{ route('store.catalog') }}" class="btn btn-warning btn-lg px-4 fw-bold shadow text-dark">
                        <i class="bi bi-shop me-2"></i> Belanja Kopi Online
                    </a>
                    @auth
                        @if(auth()->user()->canAccessDashboard())
                            <a href="{{ route('dashboard') }}" class="btn btn-coffee btn-lg px-4">
                                <i class="bi bi-speedometer2 me-2"></i> Buka Dashboard Internal
                            </a>
                        @endif
                    @else
                        <a href="{{ route('login') }}" class="btn btn-coffee btn-lg px-4">
                            <i class="bi bi-box-arrow-in-right me-2"></i> Masuk ke Sistem
                        </a>
                        <a href="{{ route('register') }}" class="btn btn-outline-coffee btn-lg px-4">
                            <i class="bi bi-person-plus me-2"></i> Daftar Akun Retail
                        </a>
                    @endauth
                </div>
            </div>
            <div class="col-lg-5 text-center">
                <div class="p-4 p-md-5 rounded-4 border border-white border-opacity-25" style="background: rgba(255, 255, 255, 0.05); backdrop-filter: blur(10px);">
                    <i class="bi bi-cup-hot display-1 text-warning d-block mb-3"></i>
                    <h4 class="text-white fw-bold mb-2">100% Kopi Kemasan Asli</h4>
                    <p class="text-white-50 small mb-0">Diperoleh dari pemasok terpercaya & didistribusikan dengan manajemen mutu terjamin.</p>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Business Pillars Section -->
<section class="py-5">
    <div class="container py-3">
        <div class="text-center mb-5">
            <span class="text-uppercase fw-bold text-warning small tracking-wide">Model Penjualan Terintegrasi</span>
            <h2 class="fw-bold text-dark mt-1">Dua Jalur Penjualan, Satu Sistem Persediaan</h2>
            <p class="text-muted">Seluruh transaksi terhubung ke satu database stok terpusat untuk menghindari persediaan minus.</p>
        </div>

        <div class="row g-4">
            <div class="col-md-6">
                <div class="card card-custom p-4 h-100">
                    <div class="d-flex align-items-center gap-3 mb-3">
                        <div class="p-3 rounded-3 bg-primary bg-opacity-10 text-primary">
                            <i class="bi bi-shop fs-3"></i>
                        </div>
                        <div>
                            <h4 class="fw-bold mb-0">1. Penjualan Retail (B2C)</h4>
                            <small class="text-muted">Untuk Konsumsi Pribadi & Pecinta Kopi</small>
                        </div>
                    </div>
                    <p class="text-muted">
                        Katalog terbuka untuk masyarakat umum tanpa wajib login. Pengunjung dapat memilih varian kemasan, melihat harga standar per SKU, memasukkan ke keranjang belanja, melakukan checkout transfer bank, atau memesan via WhatsApp.
                    </p>
                    <ul class="list-unstyled small text-muted mb-0">
                        <li class="mb-1"><i class="bi bi-check2 text-success me-2"></i>Katalog kopi tanpa wajib registrasi awal</li>
                        <li class="mb-1"><i class="bi bi-check2 text-success me-2"></i>Pilihan kemasan 100g, 250g, 500g, 1kg</li>
                        <li class="mb-1"><i class="bi bi-check2 text-success me-2"></i>Checkout transfer bank & integrasi WhatsApp</li>
                    </ul>
                </div>
            </div>

            <div class="col-md-6">
                <div class="card card-custom p-4 h-100">
                    <div class="d-flex align-items-center gap-3 mb-3">
                        <div class="p-3 rounded-3 bg-success bg-opacity-10 text-success">
                            <i class="bi bi-buildings fs-3"></i>
                        </div>
                        <div>
                            <h4 class="fw-bold mb-0">2. Penjualan Grosir (B2B)</h4>
                            <small class="text-muted">Kafe, Kedai Kopi, Toko Kelontong & Reseller</small>
                        </div>
                    </div>
                    <p class="text-muted">
                        Pesanan grosir dimasukkan langsung oleh Admin atau Sales via dashboard. Dilengkapi dengan pengelolaan plafon batas kredit (*credit limit*), termin pembayaran tempo (*terms of payment*), serta histori piutang pelanggan bisnis.
                    </p>
                    <ul class="list-unstyled small text-muted mb-0">
                        <li class="mb-1"><i class="bi bi-check2 text-success me-2"></i>Input PO B2B oleh Sales/Admin</li>
                        <li class="mb-1"><i class="bi bi-check2 text-success me-2"></i>Satu harga jual standar per SKU transparan</li>
                        <li class="mb-1"><i class="bi bi-check2 text-success me-2"></i>Pembayaran langsung atau tempo kredit teregulasi</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- System Status Alert -->
<section class="py-4 bg-light border-top">
    <div class="container text-center">
        <div class="d-inline-flex align-items-center gap-2 p-2 px-3 rounded-pill bg-white border shadow-sm">
            <span class="badge bg-success rounded-pill px-2 py-1"><i class="bi bi-check-circle-fill"></i></span>
            <small class="fw-semibold text-secondary">
                Fondasi Sistem & Otorisasi 6 Role (Owner, Admin, Sales, Warehouse, Finance, Customer) Aktif
            </small>
        </div>
    </div>
</section>
@endsection
