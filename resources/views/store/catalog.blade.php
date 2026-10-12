@extends('layouts.app')

@section('title', 'Katalog Kopi Pilihan - Kopi Distro')

@push('styles')
<style>
    .catalog-card {
        background: #ffffff;
        border: 1px solid #e2e8f0;
        border-radius: 16px;
        overflow: hidden;
        transition: all 0.3s ease;
        display: flex;
        flex-direction: column;
        height: 100%;
    }
    .catalog-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 16px 32px rgba(30, 19, 12, 0.08);
        border-color: #cbd5e1;
    }
    .catalog-img-wrap {
        position: relative;
        height: 240px;
        background-color: #f8fafc;
        overflow: hidden;
    }
    .catalog-img-wrap img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        transition: transform 0.5s ease;
    }
    .catalog-card:hover .catalog-img-wrap img {
        transform: scale(1.05);
    }
    .badge-origin {
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
</style>
@endpush

@section('content')
<div class="container py-4">
    <!-- Header Banner -->
    <div class="card p-4 p-md-5 mb-4 border-0 text-white shadow-sm" style="background: linear-gradient(135deg, #18110c 0%, #2e1a11 60%, #432416 100%); border-radius: 18px;">
        <div class="row align-items-center">
            <div class="col-lg-8">
                <span class="badge bg-warning text-dark px-3 py-2 rounded-pill fw-bold mb-2">
                    <i class="bi bi-cup-hot-fill me-1"></i> KOLEKSI BIJI &amp; BUBUK KOPI PILIHAN
                </span>
                <h1 class="display-6 fw-bold text-white mb-2">Pilihan Kopi Nusantara Terbaik</h1>
                <p class="text-white-50 lead fs-6 mb-0">Nikmati cita rasa khas Arabika &amp; Robusta nusantara dengan profil sangrai konsisten dan kemasan katup udara berdaya simpan optimal.</p>
            </div>
            <div class="col-lg-4 text-lg-end mt-3 mt-lg-0">
                <a href="{{ route('store.cart') }}" class="btn btn-warning btn-lg px-4 fw-bold text-dark shadow">
                    <i class="bi bi-cart3 me-1"></i> Keranjang Belanja 
                    @if(session('cart') && count(session('cart')) > 0)
                        <span class="badge bg-danger rounded-pill ms-1">{{ count(session('cart')) }}</span>
                    @endif
                </a>
            </div>
        </div>
    </div>

    @if($isDevMode)
        <!-- Banner Peringatan Mode Pengembang -->
        <div class="alert alert-danger d-flex align-items-center rounded-4 shadow-sm mb-4 border-0" style="background-color: #fee2e2; border-left: 6px solid #ef4444 !important;" role="alert">
            <i class="bi bi-shield-lock-fill fs-2 me-3 flex-shrink-0 text-danger"></i>
            <div class="flex-grow-1">
                <h6 class="alert-heading fw-bold text-danger mb-1">Mode Pengembang Aktif &mdash; Transaksi Pembelian Dinonaktifkan</h6>
                <p class="small text-danger text-opacity-75 mb-0">Toko saat ini sedang dalam mode pengujian &amp; pemeliharaan. Anda dapat melihat spesifikasi dan galeri foto produk, namun tombol pembelian produk dinonaktifkan sementara.</p>
            </div>
        </div>
    @endif

    <!-- Banner Info Skema Harga Pelanggan (Retail, Toko, Kafe) -->
    @if($currentCustomerType === 'toko' || $currentCustomerType === 'reseller')
        <div class="alert alert-success d-flex flex-column flex-md-row align-items-md-center justify-content-between p-3 mb-4 rounded-4 shadow-sm border-0" style="background-color: #ecfdf5; border-left: 6px solid #10b981 !important;">
            <div class="d-flex align-items-center gap-3 mb-2 mb-md-0">
                <i class="bi bi-shop fs-2 text-success"></i>
                <div>
                    <h6 class="fw-bold text-success mb-0">SKEMA HARGA MITRA TOKO / WARUNG AKTIF</h6>
                    <small class="text-success text-opacity-75">Seluruh harga produk di bawah otomatis disesuaikan dengan Harga Grosir Toko.</small>
                </div>
            </div>
            <span class="badge bg-success px-3 py-2 fw-bold">HARGA TOKO</span>
        </div>
    @elseif($currentCustomerType === 'kafe')
        <div class="alert alert-warning d-flex flex-column flex-md-row align-items-md-center justify-content-between p-3 mb-4 rounded-4 shadow-sm border-0" style="background-color: #fffbeb; border-left: 6px solid #f59e0b !important;">
            <div class="d-flex align-items-center gap-3 mb-2 mb-md-0">
                <i class="bi bi-cup-hot-fill fs-2 text-warning"></i>
                <div>
                    <h6 class="fw-bold text-dark mb-0">SKEMA HARGA MITRA KAFE / KEDAI KOPI AKTIF</h6>
                    <small class="text-muted">Seluruh harga produk di bawah otomatis disesuaikan dengan Harga Khusus Kafe &amp; Barista Horeca.</small>
                </div>
            </div>
            <span class="badge bg-warning text-dark px-3 py-2 fw-bold">HARGA KAFE</span>
        </div>
    @else
        <div class="card p-3 mb-4 rounded-4 border-0 shadow-sm" style="background: linear-gradient(90deg, #f8fafc 0%, #f1f5f9 100%); border-left: 6px solid #b86b35 !important;">
            <div class="d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-3">
                <div class="d-flex align-items-center gap-3">
                    <i class="bi bi-tags-fill fs-2 text-warning"></i>
                    <div>
                        <strong class="text-dark d-block">Menampilkan Harga Konsumen Retail (Umum)</strong>
                        <small class="text-muted">Punya usaha Warung Kelontong, Toko, atau Kafe? Dapatkan potongan harga grosir yang lebih murah!</small>
                    </div>
                </div>
                @guest
                    <a href="{{ route('register') }}" class="btn btn-sm btn-warning fw-bold text-dark px-3 shadow-sm flex-shrink-0">
                        <i class="bi bi-person-plus me-1"></i> Daftar Akun Toko / Kafe &rarr;
                    </a>
                @endguest
            </div>
        </div>
    @endif

    <!-- Filter Bar -->
    <div class="card p-3 mb-4 border border-light-subtle shadow-sm rounded-4">
        <form method="GET" action="{{ route('store.catalog') }}" class="row g-2 align-items-center">
            <div class="col-md-5">
                <div class="input-group">
                    <span class="input-group-text bg-white border-end-0 text-muted"><i class="bi bi-search"></i></span>
                    <input type="text" name="search" class="form-control border-start-0" placeholder="Cari kopi favorit Anda (ATAPMERAH, Robusta, Bintang Harapan, Gula Aren)..." value="{{ request('search') }}">
                </div>
            </div>
            <div class="col-md-3">
                <select name="category_id" class="form-select">
                    <option value="">-- Semua Kategori --</option>
                    @foreach($categories as $cat)
                        <option value="{{ $cat->id }}" {{ request('category_id') == $cat->id ? 'selected' : '' }}>{{ $cat->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2">
                <select name="coffee_type" class="form-select">
                    <option value="">-- Jenis Kopi --</option>
                    <option value="Robusta" {{ request('coffee_type') === 'Robusta' ? 'selected' : '' }}>Robusta</option>
                    <option value="Blend" {{ request('coffee_type') === 'Blend' ? 'selected' : '' }}>Campur / Blend</option>
                </select>
            </div>
            <div class="col-md-2 d-flex gap-2">
                <button type="submit" class="btn btn-warning fw-bold text-dark flex-grow-1">
                    <i class="bi bi-funnel me-1"></i> Filter
                </button>
                @if(request()->hasAny(['search', 'category_id', 'coffee_type']))
                    <a href="{{ route('store.catalog') }}" class="btn btn-outline-secondary" title="Reset Filter">
                        <i class="bi bi-x-circle"></i>
                    </a>
                @endif
            </div>
        </form>
    </div>

    <!-- Products Grid -->
    <div class="row g-4">
        @forelse($products as $product)
            <div class="col-md-6 col-lg-4">
                <div class="catalog-card">
                    <!-- Foto Kemasan Asli HD -->
                    <div class="catalog-img-wrap">
                        <a href="{{ route('store.product', $product->slug) }}">
                            <img src="{{ $product->image_url }}" alt="{{ $product->name }}" loading="lazy">
                        </a>
                        <span class="badge-origin">
                            <i class="bi bi-tag-fill me-1"></i> {{ $product->brand }}
                        </span>
                    </div>

                    <div class="p-4 flex-grow-1 d-flex flex-column">
                        <div class="d-flex justify-content-between align-items-center mb-1">
                            <span class="badge bg-secondary bg-opacity-10 text-secondary border">
                                {{ $product->category->name ?? 'Kopi Kemasan' }}
                            </span>
                            <small class="text-muted fw-semibold">{{ $product->producer ?? 'Palu, Sulawesi Tengah' }}</small>
                        </div>
                        <h4 class="fw-bold text-dark mb-2 fs-5">
                            <a href="{{ route('store.product', $product->slug) }}" class="text-dark text-decoration-none">
                                {{ $product->name }}
                            </a>
                        </h4>
                        <p class="text-muted small mb-3 flex-grow-1">{{ Str::limit($product->description, 95) }}</p>

                        <!-- Pilihan Varian Gramasi & Tambah Keranjang -->
                        <div class="border-top pt-3 mt-auto">
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <h6 class="small fw-bold text-secondary text-uppercase mb-0">
                                    <i class="bi bi-boxes me-1 text-warning"></i> Pilih Gramasi:
                                </h6>
                                <a href="{{ route('store.product', $product->slug) }}" class="small fw-semibold text-warning text-decoration-none">
                                    Detail &amp; Foto &rarr;
                                </a>
                            </div>
                            @if($product->variants->count() > 0)
                                @if($isDevMode)
                                    <div class="mb-2">
                                        <select class="form-select form-select-sm bg-light" disabled>
                                            @foreach($product->variants as $variant)
                                                <option>
                                                    {{ $variant->variant_name }} ({{ $variant->package_weight_grams }}g) &mdash; {{ $variant->getFormattedPriceFor($currentCustomerType) }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="d-flex gap-2">
                                        <button type="button" class="btn btn-sm btn-secondary w-100 fw-bold opacity-75" disabled title="Mode Pengembang Aktif (Pembelian Dinonaktifkan)">
                                            <i class="bi bi-lock-fill me-1"></i> Pembelian Dinonaktifkan
                                        </button>
                                        <a href="{{ route('store.product', $product->slug) }}" class="btn btn-sm btn-outline-dark" title="Pratinjau Detail">
                                            <i class="bi bi-eye"></i>
                                        </a>
                                    </div>
                                @else
                                    <form method="POST" action="{{ route('store.cart.add') }}">
                                        @csrf
                                        <div class="mb-2">
                                            <select name="product_variant_id" class="form-select form-select-sm" required>
                                                @foreach($product->variants as $variant)
                                                    <option value="{{ $variant->id }}" {{ $variant->current_stock <= 0 ? 'disabled' : '' }}>
                                                        {{ $variant->variant_name }} ({{ $variant->package_weight_grams }}g) &mdash; 
                                                        {{ $variant->getFormattedPriceFor($currentCustomerType) }}
                                                        @if($variant->hasSpecialPriceFor($currentCustomerType))
                                                            [Harga Khusus]
                                                        @endif
                                                        @if($variant->current_stock <= 0)
                                                            [HABIS]
                                                        @else
                                                            (Stok: {{ $variant->current_stock }})
                                                        @endif
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="d-flex gap-2">
                                            <div class="input-group input-group-sm" style="width: 100px;">
                                                <span class="input-group-text bg-light text-muted px-2">Qty</span>
                                                <input type="number" name="quantity" class="form-control text-center px-1" value="1" min="1" required>
                                            </div>
                                            <button type="submit" class="btn btn-sm btn-warning fw-bold text-dark flex-grow-1 shadow-sm">
                                                <i class="bi bi-cart-plus me-1"></i> Beli Sekarang
                                            </button>
                                        </div>
                                    </form>
                                @endif
                            @else
                                <span class="badge bg-secondary">Varian Belum Tersedia</span>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-12 py-5 text-center text-muted">
                <i class="bi bi-cup fs-1 text-secondary opacity-50 mb-3 d-block"></i>
                <h5 class="fw-semibold text-dark">Kopi yang Anda cari belum ditemukan</h5>
                <p class="small text-muted mb-0">Coba ubah kata kunci pencarian atau reset filter kategori.</p>
                <div class="mt-3">
                    <a href="{{ route('store.catalog') }}" class="btn btn-outline-dark btn-sm">
                        <i class="bi bi-arrow-clockwise me-1"></i> Reset Pencarian
                    </a>
                </div>
            </div>
        @endforelse
    </div>

    @if($products->hasPages())
        <div class="mt-4 d-flex justify-content-center">
            {{ $products->links() }}
        </div>
    @endif
</div>
@endsection
