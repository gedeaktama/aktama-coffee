@extends('layouts.app')

@section('title', $product->name . ' — Kopi Distro')

@push('styles')
<style>
    .product-main-img-box {
        position: relative;
        background: #f8fafc;
        border-radius: 20px;
        overflow: hidden;
        border: 1px solid #e2e8f0;
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.05);
        display: flex;
        align-items: center;
        justify-content: center;
        min-height: 420px;
    }
    .product-main-img-box img {
        width: 100%;
        max-height: 520px;
        object-fit: contain;
        transition: transform 0.4s ease;
    }
    .product-main-img-box:hover img {
        transform: scale(1.03);
    }
    .thumb-item {
        width: 80px;
        height: 80px;
        border-radius: 12px;
        overflow: hidden;
        border: 2px solid transparent;
        cursor: pointer;
        background: #f8fafc;
        transition: all 0.2s ease;
    }
    .thumb-item img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }
    .thumb-item:hover, .thumb-item.active {
        border-color: #f59e0b;
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(245, 158, 11, 0.25);
    }
    .badge-brand {
        background: rgba(245, 158, 11, 0.12);
        color: #b45309;
        font-weight: 700;
        letter-spacing: 0.5px;
        border: 1px solid rgba(245, 158, 11, 0.3);
        padding: 0.4rem 0.85rem;
        border-radius: 50px;
    }
    .spec-item {
        background: #f8fafc;
        border: 1px solid #e2e8f0;
        border-radius: 12px;
        padding: 0.85rem 1.15rem;
    }
    .instruction-card {
        background: linear-gradient(135deg, #fdfbf7 0%, #fef3c7 100%);
        border: 1px solid #fde68a;
        border-radius: 16px;
        padding: 1.25rem 1.5rem;
    }
</style>
@endpush

@section('content')
<div class="container py-4">
    <!-- Breadcrumb -->
    <nav aria-label="breadcrumb" class="mb-4">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('home') }}" class="text-decoration-none text-muted">Beranda</a></li>
            <li class="breadcrumb-item"><a href="{{ route('store.catalog') }}" class="text-decoration-none text-muted">Katalog Kopi</a></li>
            <li class="breadcrumb-item active text-dark fw-semibold" aria-current="page">{{ $product->name }}</li>
        </ol>
    </nav>

    <div class="row g-5">
        <!-- Kolom Foto Produk & Galeri -->
        <div class="col-lg-6">
            <div class="sticky-top" style="top: 90px;">
                <!-- Foto Utama -->
                <div class="product-main-img-box mb-3">
                    <img id="mainProductImage" src="{{ $product->image_url }}" alt="{{ $product->name }}">
                </div>

                <!-- Thumbnail Galeri (Foto Depan, Belakang, Instruksi) -->
                @if(count($product->all_images) > 1)
                    <div class="d-flex flex-wrap gap-2 justify-content-center">
                        @foreach($product->all_images as $index => $imgUrl)
                            <div class="thumb-item {{ $index === 0 ? 'active' : '' }}" onclick="switchImage('{{ $imgUrl }}', this)">
                                <img src="{{ $imgUrl }}" alt="Foto {{ $index + 1 }}">
                            </div>
                        @endforeach
                    </div>
                    <div class="text-center mt-2">
                        <small class="text-muted"><i class="bi bi-images me-1"></i> Klik thumbnail untuk melihat foto kemasan depan, belakang &amp; instruksi detail</small>
                    </div>
                @endif
            </div>
        </div>

        <!-- Kolom Informasi & Checkout Produk -->
        <div class="col-lg-6">
            <!-- Badges -->
            <div class="d-flex flex-wrap align-items-center gap-2 mb-2">
                <span class="badge-brand">
                    <i class="bi bi-patch-check-fill me-1"></i> MEREK: {{ $product->brand }}
                </span>
                <span class="badge bg-secondary bg-opacity-10 text-secondary border px-3 py-2 rounded-pill">
                    {{ $product->category->name ?? 'Kopi Kemasan' }}
                </span>
                <span class="badge bg-warning bg-opacity-15 text-dark border border-warning px-3 py-2 rounded-pill fw-bold">
                    <i class="bi bi-tag-fill me-1 text-warning"></i> {{ $product->coffee_type }}
                </span>
            </div>

            <!-- Judul Produk -->
            <h1 class="h2 fw-bold text-dark mb-2">{{ $product->name }}</h1>
            
            <p class="text-muted fs-6 mb-4 leading-relaxed">
                {{ $product->description }}
            </p>

            <!-- Form Pilih Varian & Tambah ke Keranjang -->
            <div class="card p-4 border border-light-subtle shadow-sm rounded-4 mb-4" style="background: #ffffff;">
                @if($isDevMode)
                    <div class="alert alert-danger d-flex align-items-center rounded-3 p-3 mb-3 border-0" style="background-color: #fee2e2; border-left: 5px solid #ef4444 !important;">
                        <i class="bi bi-shield-lock-fill fs-3 text-danger me-3 flex-shrink-0"></i>
                        <div>
                            <strong class="d-block text-danger">Mode Pengembang Aktif &mdash; Pembelian Dinonaktifkan</strong>
                            <span class="small text-danger text-opacity-75">Toko sedang dalam pemeliharaan. Anda tetap dapat melihat spesifikasi harga &amp; kemasan, namun tombol pembelian dikunci sementara.</span>
                        </div>
                    </div>
                @endif

                <form method="POST" action="{{ route('store.cart.add') }}" id="addToCartForm">
                    @csrf
                    
                    <label class="form-label fw-bold text-dark mb-2">
                        <i class="bi bi-boxes me-1 text-warning"></i> Pilih Kemasan / Varian:
                    </label>

                    @if($product->variants->count() > 0)
                        <div class="d-flex flex-column gap-2 mb-3">
                            @foreach($product->variants as $variant)
                                <label class="border rounded-3 p-3 d-flex justify-content-between align-items-center cursor-pointer variant-label" style="cursor: pointer;">
                                    <div class="d-flex align-items-center gap-3">
                                        <input type="radio" name="product_variant_id" value="{{ $variant->id }}" 
                                            class="form-check-input mt-0 variant-radio" 
                                            data-price="{{ $variant->selling_price }}" 
                                            data-stock="{{ $variant->current_stock }}"
                                            {{ $loop->first ? 'checked' : '' }}
                                            {{ $variant->current_stock <= 0 ? 'disabled' : '' }}>
                                        <div>
                                            <span class="fw-bold text-dark d-block">{{ $variant->variant_name }}</span>
                                            <small class="text-muted">Berat: {{ $variant->package_weight_grams }}g &bull; Gilingan: {{ $variant->grind_form }}</small>
                                        </div>
                                    </div>
                                    <div class="text-end">
                                        <span class="fw-bold text-dark fs-5">{{ $variant->formatted_price }}</span>
                                        <small class="d-block {{ $variant->current_stock <= 0 ? 'text-danger fw-bold' : 'text-success' }}">
                                            {{ $variant->current_stock <= 0 ? 'Stok Habis' : 'Stok: ' . $variant->current_stock . ' pack' }}
                                        </small>
                                    </div>
                                </label>
                            @endforeach
                        </div>

                        <div class="row g-3 align-items-center mb-3">
                            <div class="col-sm-4">
                                <label class="form-label small text-muted mb-1">Jumlah Pesanan:</label>
                                <div class="input-group">
                                    <button class="btn btn-outline-secondary" type="button" onclick="decrementQty()" {{ $isDevMode ? 'disabled' : '' }}>-</button>
                                    <input type="number" id="qtyInput" name="quantity" class="form-control text-center fw-bold" value="1" min="1" {{ $isDevMode ? 'disabled' : 'required' }}>
                                    <button class="btn btn-outline-secondary" type="button" onclick="incrementQty()" {{ $isDevMode ? 'disabled' : '' }}>+</button>
                                </div>
                            </div>
                            <div class="col-sm-8 pt-sm-4">
                                @if($isDevMode)
                                    <button type="button" class="btn btn-secondary btn-lg w-100 fw-bold py-2 opacity-75 shadow-sm" disabled title="Mode Pengembang Aktif (Pembelian Dinonaktifkan)">
                                        <i class="bi bi-lock-fill me-1"></i> Pembelian Dinonaktifkan
                                    </button>
                                @else
                                    <button type="submit" class="btn btn-warning btn-lg w-100 fw-bold text-dark shadow-sm py-2">
                                        <i class="bi bi-cart-plus me-1"></i> Masukkan ke Keranjang
                                    </button>
                                @endif
                            </div>
                        </div>
                    @else
                        <div class="alert alert-warning mb-0">Varian produk belum tersedia.</div>
                    @endif
                </form>

                <!-- Tombol Beli Cepat via Keranjang -->
                <div class="d-flex justify-content-between align-items-center pt-3 border-top mt-2">
                    <span class="small text-muted"><i class="bi bi-truck me-1"></i> Pengiriman aman ke seluruh Nusantara</span>
                    <a href="{{ route('store.cart') }}" class="btn btn-sm btn-outline-dark fw-semibold">
                        <i class="bi bi-cart3 me-1"></i> Buka Keranjang
                    </a>
                </div>
            </div>

            <!-- Spesifikasi Resmi Produk -->
            <h5 class="fw-bold text-dark mb-3"><i class="bi bi-info-circle me-1 text-warning"></i> Informasi Resmi Kemasan</h5>
            <div class="row g-2 mb-4">
                <div class="col-sm-6">
                    <div class="spec-item">
                        <small class="text-muted d-block">Berat Bersih</small>
                        <strong class="text-dark">{{ $product->net_weight ?: '-' }}</strong>
                    </div>
                </div>
                <div class="col-sm-6">
                    <div class="spec-item">
                        <small class="text-muted d-block">Komposisi Kemasan</small>
                        <strong class="text-dark">{{ $product->composition ?: '-' }}</strong>
                    </div>
                </div>
                <div class="col-sm-6">
                    <div class="spec-item">
                        <small class="text-muted d-block">Produsen Resmi</small>
                        <strong class="text-dark">{{ $product->producer ?: '-' }}</strong>
                    </div>
                </div>
                <div class="col-sm-6">
                    <div class="spec-item">
                        <small class="text-muted d-block">Asal Produksi</small>
                        <strong class="text-dark">{{ $product->origin ?: 'Palu, Sulawesi Tengah' }}</strong>
                    </div>
                </div>
            </div>

            <!-- Petunjuk Penyajian -->
            @if($product->serving_suggestion)
                <div class="instruction-card mb-3">
                    <h6 class="fw-bold text-dark mb-2">
                        <i class="bi bi-cup-hot-fill me-1 text-warning"></i> Cara Penyajian:
                    </h6>
                    <p class="small text-secondary mb-0 leading-relaxed">{{ $product->serving_suggestion }}</p>
                </div>
            @endif

            <!-- Petunjuk Penyimpanan -->
            @if($product->storage_instruction)
                <div class="p-3 bg-light rounded-3 border d-flex align-items-center gap-3">
                    <i class="bi bi-shield-check fs-4 text-success"></i>
                    <div>
                        <strong class="small text-dark d-block">Petunjuk Penyimpanan:</strong>
                        <span class="small text-muted">{{ $product->storage_instruction }}</span>
                    </div>
                </div>
            @endif
        </div>
    </div>

    <!-- Produk Kopi Pilihan Lainnya -->
    @if(isset($relatedProducts) && $relatedProducts->count() > 0)
        <div class="mt-5 pt-5 border-top">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h3 class="fw-bold text-dark mb-1">Pilihan Kopi Lainnya</h3>
                    <p class="text-muted small mb-0">Jelajahi ragam cita rasa kopi pilihan khas Sulawesi Tengah.</p>
                </div>
                <a href="{{ route('store.catalog') }}" class="btn btn-outline-dark btn-sm fw-semibold">
                    Semua Produk &rarr;
                </a>
            </div>

            <div class="row g-4">
                @foreach($relatedProducts as $rel)
                    <div class="col-md-4">
                        <div class="card h-100 border rounded-4 overflow-hidden shadow-sm">
                            <div style="height: 200px; background: #f8fafc; overflow: hidden;" class="position-relative">
                                <a href="{{ route('store.product', $rel->slug) }}">
                                    <img src="{{ $rel->image_url }}" alt="{{ $rel->name }}" style="width: 100%; height: 100%; object-fit: cover;">
                                </a>
                                <span class="badge bg-dark bg-opacity-75 text-warning position-absolute top-0 start-0 m-2">
                                    {{ $rel->brand }}
                                </span>
                            </div>
                            <div class="p-3 d-flex flex-column flex-grow-1">
                                <span class="small text-muted mb-1">{{ $rel->category->name ?? 'Kopi Kemasan' }}</span>
                                <h5 class="fw-bold fs-6 mb-2">
                                    <a href="{{ route('store.product', $rel->slug) }}" class="text-dark text-decoration-none">
                                        {{ $rel->name }}
                                    </a>
                                </h5>
                                <div class="mt-auto pt-2 border-top d-flex justify-content-between align-items-center">
                                    <div>
                                        <small class="text-muted d-block" style="font-size: 0.75rem;">Mulai dari</small>
                                        <strong class="text-dark">Rp {{ number_format($rel->variants->min('selling_price') ?? 0, 0, ',', '.') }}</strong>
                                    </div>
                                    <a href="{{ route('store.product', $rel->slug) }}" class="btn btn-sm btn-warning fw-bold text-dark">
                                        Lihat Detail
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    @endif
</div>
@endsection

@push('scripts')
<script>
    function switchImage(src, element) {
        document.getElementById('mainProductImage').src = src;
        document.querySelectorAll('.thumb-item').forEach(el => el.classList.remove('active'));
        if (element) {
            element.classList.add('active');
        }
    }

    function incrementQty() {
        const input = document.getElementById('qtyInput');
        input.value = parseInt(input.value || 1) + 1;
    }

    function decrementQty() {
        const input = document.getElementById('qtyInput');
        if (parseInt(input.value) > 1) {
            input.value = parseInt(input.value) - 1;
        }
    }
</script>
@endpush
