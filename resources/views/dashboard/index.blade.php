@extends('layouts.app')

@section('title', 'Dashboard Internal - Kopi Distro')

@section('content')
<div class="container py-3">
    <!-- Welcome Banner -->
    <div class="card card-custom p-4 mb-4 border-0 text-white" style="background: linear-gradient(135deg, var(--coffee-dark) 0%, var(--coffee-roast) 100%);">
        <div class="row align-items-center">
            <div class="col-lg-8">
                <div class="d-flex align-items-center gap-2 mb-2">
                    <span class="badge badge-role badge-role-{{ $user->role }} px-3 py-1">
                        ROLE: {{ strtoupper($user->role) }}
                    </span>
                    <span class="badge bg-success bg-opacity-75">Sistem Aktif</span>
                </div>
                <h2 class="fw-bold text-white mb-2">Selamat Datang, {{ $user->name }}!</h2>
                <p class="text-white-50 mb-0">
                    @if($user->isOwner())
                        Anda memiliki <strong>akses penuh</strong> atas seluruh konfigurasi bisnis, persetujuan batas kredit, audit stok, dan seluruh laporan laba kotor & HPP.
                    @elseif($user->isAdmin())
                        Anda memiliki wewenang untuk mengelola master data produk, varian/SKU, supplier, data pelanggan, dan operasional transaksi harian.
                    @elseif($user->isSales())
                        Anda memiliki wewenang untuk mengelola data pelanggan bisnis binaan dan membuat Pesanan Penjualan Grosir (B2B).
                    @elseif($user->isWarehouse())
                        Anda bertanggung jawab atas konfirmasi penerimaan barang dari supplier, penyiapan pesanan, mutasi fisik, dan surat jalan pengiriman.
                    @elseif($user->isFinance())
                        Anda mengelola verifikasi pembayaran tunai/transfer, penerbitan faktur penjualan, rekonsiliasi piutang tempo, dan kartu piutang pelanggan.
                    @else
                        Anda terhubung ke sistem internal Kopi Distro.
                    @endif
                </p>
            </div>
            <div class="col-lg-4 text-lg-end mt-3 mt-lg-0">
                <div class="d-inline-block text-start p-3 rounded-3 bg-white bg-opacity-10 border border-white border-opacity-25">
                    <small class="text-white-50 d-block">Waktu Login</small>
                    <span class="fw-bold text-white fs-6">{{ now()->translatedFormat('d F Y, H:i') }} WIB</span>
                    <small class="text-white-50 d-block mt-1">Status Akun: <span class="text-success fw-bold">Aktif</span></small>
                </div>
            </div>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="row g-3 mb-4">
        <div class="col-sm-6 col-lg-3">
            <div class="card card-custom p-3">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <span class="text-muted small fw-semibold text-uppercase">Total Pengguna</span>
                        <h3 class="fw-bold mb-0 text-dark">{{ $userCounts['total'] }}</h3>
                    </div>
                    <div class="p-3 rounded-3 bg-primary bg-opacity-10 text-primary">
                        <i class="bi bi-people-fill fs-4"></i>
                    </div>
                </div>
                <small class="text-muted mt-2 d-block border-top pt-2">Terdaftar di sistem database</small>
            </div>
        </div>

        <div class="col-sm-6 col-lg-3">
            <div class="card card-custom p-3">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <span class="text-muted small fw-semibold text-uppercase">Staf Internal</span>
                        <h3 class="fw-bold mb-0 text-success">{{ $userCounts['total'] - $userCounts['customer'] }}</h3>
                    </div>
                    <div class="p-3 rounded-3 bg-success bg-opacity-10 text-success">
                        <i class="bi bi-person-badge-fill fs-4"></i>
                    </div>
                </div>
                <small class="text-muted mt-2 d-block border-top pt-2">Owner, Admin, Sales, Gudang, Finance</small>
            </div>
        </div>

        <div class="col-sm-6 col-lg-3">
            <div class="card card-custom p-3">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <span class="text-muted small fw-semibold text-uppercase">Produk & Varian</span>
                        <h3 class="fw-bold mb-0 text-primary">{{ $masterCounts['products'] }} <small class="fs-6 text-muted">({{ $masterCounts['variants'] }} SKU)</small></h3>
                    </div>
                    <div class="p-3 rounded-3 bg-primary bg-opacity-10 text-primary">
                        <i class="bi bi-box-seam fs-4"></i>
                    </div>
                </div>
                <div class="mt-2 pt-2 border-top d-flex justify-content-between small">
                    <span class="text-muted">Total Stok: <strong>{{ $masterCounts['total_stock'] }} pack</strong></span>
                    <a href="{{ route('products.index') }}" class="text-decoration-none fw-semibold">Kelola &rarr;</a>
                </div>
            </div>
        </div>

        <div class="col-sm-6 col-lg-3">
            <div class="card card-custom p-3">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <span class="text-muted small fw-semibold text-uppercase">Pelanggan Grosir (B2B)</span>
                        <h3 class="fw-bold mb-0 text-success">{{ $masterCounts['b2b_customers'] }} <small class="fs-6 text-muted">Mitra</small></h3>
                    </div>
                    <div class="p-3 rounded-3 bg-success bg-opacity-10 text-success">
                        <i class="bi bi-buildings-fill fs-4"></i>
                    </div>
                </div>
                <div class="mt-2 pt-2 border-top d-flex justify-content-between small">
                    <span class="text-muted">Kafe, Toko & Reseller</span>
                    <a href="{{ route('customers.index') }}" class="text-decoration-none fw-semibold">Kelola &rarr;</a>
                </div>
            </div>
        </div>

        <div class="col-sm-6 col-lg-3">
            <div class="card card-custom p-3">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <span class="text-muted small fw-semibold text-uppercase">Pemasok / Supplier</span>
                        <h3 class="fw-bold mb-0 text-info">{{ $masterCounts['suppliers'] }} <small class="fs-6 text-muted">Pemasok</small></h3>
                    </div>
                    <div class="p-3 rounded-3 bg-info bg-opacity-10 text-info">
                        <i class="bi bi-truck fs-4"></i>
                    </div>
                </div>
                <div class="mt-2 pt-2 border-top d-flex justify-content-between small">
                    <span class="text-muted">Sumber Kopi Kemasan</span>
                    @if(auth()->user()->hasRole(['owner', 'admin', 'warehouse']))
                        <a href="{{ route('suppliers.index') }}" class="text-decoration-none fw-semibold">Kelola &rarr;</a>
                    @else
                        <span class="text-muted">-</span>
                    @endif
                </div>
            </div>
        </div>

        <div class="col-sm-6 col-lg-3">
            <div class="card card-custom p-3">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <span class="text-muted small fw-semibold text-uppercase">Kategori Kopi</span>
                        <h3 class="fw-bold mb-0 text-warning">{{ $masterCounts['categories'] }} <small class="fs-6 text-muted">Kategori</small></h3>
                    </div>
                    <div class="p-3 rounded-3 bg-warning bg-opacity-10 text-warning">
                        <i class="bi bi-tags-fill fs-4"></i>
                    </div>
                </div>
                <div class="mt-2 pt-2 border-top d-flex justify-content-between small">
                    <span class="text-muted">Single Origin, Robusta, Blend</span>
                    @if(auth()->user()->hasRole(['owner', 'admin']))
                        <a href="{{ route('categories.index') }}" class="text-decoration-none fw-semibold">Kelola &rarr;</a>
                    @else
                        <span class="text-muted">-</span>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- System Roadmap & Phase Status -->
    <div class="row g-4">
        <div class="col-lg-8">
            <div class="card card-custom p-4">
                <div class="d-flex align-items-center justify-content-between mb-3 pb-2 border-bottom">
                    <div>
                        <h5 class="fw-bold mb-0 text-dark">
                            <i class="bi bi-diagram-3 me-2 text-warning"></i> Status Pembangunan Tahapan Sistem
                        </h5>
                        <small class="text-muted">Progres implementasi modular sesuai Master Prompt</small>
                    </div>
                    <span class="badge bg-success px-3 py-2">Tahap 1 s/d 4 Selesai</span>
                </div>

                <div class="list-group list-group-flush">
                    <!-- Tahap 1 -->
                    <div class="list-group-item px-0 py-3 d-flex align-items-start gap-3">
                        <span class="badge bg-success rounded-pill p-2"><i class="bi bi-check-lg fs-6"></i></span>
                        <div class="flex-grow-1">
                            <div class="d-flex align-items-center justify-content-between">
                                <h6 class="fw-bold mb-1">Tahap 1 — Fondasi & Autentikasi Multi-Role</h6>
                                <span class="badge bg-success">AKTIF & TERUJI</span>
                            </div>
                            <p class="text-muted small mb-0">Setup Laravel 11, koneksi DB MySQL `kopi`, migration tabel users dengan 6 role (Owner, Admin, Sales, Warehouse, Finance, Customer), middleware otorisasi, dan seeder data awal.</p>
                        </div>
                    </div>

                    <!-- Tahap 2 -->
                    <div class="list-group-item px-0 py-3 d-flex align-items-start gap-3">
                        <span class="badge bg-success rounded-pill p-2"><i class="bi bi-check-lg fs-6"></i></span>
                        <div class="flex-grow-1">
                            <div class="d-flex align-items-center justify-content-between">
                                <h6 class="fw-bold mb-1">Tahap 2 — Master Data (Produk, Varian SKU, Supplier, Pelanggan Bisnis)</h6>
                                <span class="badge bg-success">AKTIF & TERUJI</span>
                            </div>
                            <p class="text-muted small mb-0">CRUD Kategori, Produk & Varian (Arabika & Robusta gramasi 100g, 250g, 500g, 1kg, satu harga jual standar per SKU), Supplier kopi, dan Data Pelanggan Grosir B2B (kafe, toko kelontong, reseller beserta batas plafon kredit & termin tempo).</p>
                        </div>
                    </div>

                    <!-- Tahap 3 -->
                    <div class="list-group-item px-0 py-3 d-flex align-items-start gap-3">
                        <span class="badge bg-success rounded-pill p-2"><i class="bi bi-check-lg fs-6"></i></span>
                        <div class="flex-grow-1">
                            <div class="d-flex align-items-center justify-content-between">
                                <h6 class="fw-bold mb-1">Tahap 3 — Persediaan & Pembelian Supplier</h6>
                                <span class="badge bg-success">AKTIF & TERUJI</span>
                            </div>
                            <p class="text-muted small mb-0">Order pembelian ke supplier (PO), konfirmasi penerimaan fisik menambah saldo stok, kartu mutasi stok real-time, monitoring stok minimum & alert, serta stock opname audit.</p>
                        </div>
                    </div>

                    <!-- Tahap 4 -->
                    <div class="list-group-item px-0 py-3 d-flex align-items-start gap-3">
                        <span class="badge bg-success rounded-pill p-2"><i class="bi bi-check-lg fs-6"></i></span>
                        <div class="flex-grow-1">
                            <div class="d-flex align-items-center justify-content-between">
                                <h6 class="fw-bold mb-1">Tahap 4 — Penjualan Grosir B2B & Retail Online</h6>
                                <span class="badge bg-success">AKTIF & TERUJI</span>
                            </div>
                            <p class="text-muted small mb-0">Input order sales B2B dengan kontrol plafon kredit & termin tempo, katalog & checkout online retail (B2C), snapshot harga standar per SKU, serta pemotongan stok otomatis anti-stok minus.</p>
                        </div>
                    </div>

                    <!-- Tahap 5 -->
                    <div class="list-group-item px-0 py-3 d-flex align-items-start gap-3">
                        <span class="badge bg-light text-muted border rounded-pill p-2"><i class="bi bi-hourglass-split fs-6"></i></span>
                        <div class="flex-grow-1">
                            <div class="d-flex align-items-center justify-content-between">
                                <h6 class="fw-semibold mb-1 text-secondary">Tahap 5 — Pembayaran & Manajemen Piutang Tempo</h6>
                                <span class="badge bg-light text-secondary border">TERJADWAL</span>
                            </div>
                            <p class="text-muted small mb-0">Faktur penjualan, verifikasi transfer, pembayaran tempo dengan plafon kredit, kartu piutang, dan penagihan jatuh tempo.</p>
                        </div>
                    </div>

                    <!-- Tahap 6 & 7 -->
                    <div class="list-group-item px-0 py-3 d-flex align-items-start gap-3">
                        <span class="badge bg-light text-muted border rounded-pill p-2"><i class="bi bi-hourglass-split fs-6"></i></span>
                        <div class="flex-grow-1">
                            <div class="d-flex align-items-center justify-content-between">
                                <h6 class="fw-semibold mb-1 text-secondary">Tahap 6 & 7 — Pengiriman, Laporan Bisnis & Pengujian Akhir</h6>
                                <span class="badge bg-light text-secondary border">TERJADWAL</span>
                            </div>
                            <p class="text-muted small mb-0">Surat jalan, pelacakan pesanan, laporan HPP & laba kotor, ekspor CSV, pengujian otomatis penuh, dan panduan deployment.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Sidebar / User Info -->
        <div class="col-lg-4">
            <div class="card card-custom p-4 mb-4">
                <h6 class="fw-bold mb-3 pb-2 border-bottom">Profil Pengguna Saat Ini</h6>
                <div class="mb-2">
                    <small class="text-muted d-block">Nama Lengkap</small>
                    <span class="fw-semibold">{{ $user->name }}</span>
                </div>
                <div class="mb-2">
                    <small class="text-muted d-block">Email</small>
                    <span class="fw-semibold">{{ $user->email }}</span>
                </div>
                <div class="mb-2">
                    <small class="text-muted d-block">Nomor Telepon</small>
                    <span class="fw-semibold">{{ $user->phone ?? '-' }}</span>
                </div>
                <div class="mb-3">
                    <small class="text-muted d-block">Peran Otoritas (Role)</small>
                    <span class="badge badge-role badge-role-{{ $user->role }}">{{ strtoupper($user->role) }}</span>
                </div>

                <div class="border-top pt-3">
                    <small class="text-muted d-block mb-2">Simulasi Pengujian Akun Lain:</small>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="btn btn-outline-danger btn-sm w-100">
                            <i class="bi bi-box-arrow-right me-1"></i> Ganti Akun / Logout
                        </button>
                    </form>
                </div>
            </div>

            <div class="card card-custom p-4 bg-light border-0">
                <h6 class="fw-bold mb-2"><i class="bi bi-shield-check text-success me-1"></i> Keamanan Role Aktif</h6>
                <p class="small text-muted mb-0">
                    Sistem ini dilindungi oleh middleware <code>role</code> di tingkat server. Akses tidak hanya disembunyikan dari UI, melainkan divalidasi langsung sebelum controller dipanggil.
                </p>
            </div>
        </div>
    </div>
</div>
@endsection
