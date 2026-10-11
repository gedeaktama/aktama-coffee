@extends('layouts.app')

@section('title', 'Katalog Kopi Pilihan - Kopi Distro')

@section('content')
<div class="container py-3">
    <!-- Hero Banner -->
    <div class="card card-custom p-4 p-md-5 mb-4 border-0 text-white" style="background: linear-gradient(135deg, var(--coffee-dark) 0%, var(--coffee-roast) 100%);">
        <div class="row align-items-center">
            <div class="col-lg-8">
                <span class="badge bg-warning text-dark px-3 py-2 rounded-pill fw-bold mb-2">
                    <i class="bi bi-cup-hot-fill me-1"></i> KOPI NUSANTARA PILIHAN
                </span>
                <h1 class="display-6 fw-bold text-white mb-2">Biji & Bubuk Kopi Segar Langsung dari Petani</h1>
                <p class="text-white-50 lead fs-6 mb-0">Nikmati cita rasa khas Arabika & Robusta nusantara dengan standar pemanggangan premium dan pengemasan higienis.</p>
            </div>
            <div class="col-lg-4 text-lg-end mt-3 mt-lg-0">
                <a href="{{ route('store.cart') }}" class="btn btn-warning btn-lg px-4 fw-bold shadow">
                    <i class="bi bi-cart3 me-1"></i> Keranjang Belanja 
                    @if(session('cart') && count(session('cart')) > 0)
                        <span class="badge bg-danger rounded-pill ms-1">{{ count(session('cart')) }}</span>
                    @endif
                </a>
            </div>
        </div>
    </div>

    <!-- Filter Bar -->
    <div class="card card-custom p-3 mb-4">
        <form method="GET" action="{{ route('store.catalog') }}" class="row g-2 align-items-center">
            <div class="col-md-5">
                <div class="input-group">
                    <span class="input-group-text bg-white border-end-0 text-muted"><i class="bi bi-search"></i></span>
                    <input type="text" name="search" class="form-control border-start-0" placeholder="Cari kopi favorit Anda (Gayo, Toraja, Robusta)..." value="{{ request('search') }}">
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
                    <option value="Arabika" {{ request('coffee_type') === 'Arabika' ? 'selected' : '' }}>Arabika</option>
                    <option value="Robusta" {{ request('coffee_type') === 'Robusta' ? 'selected' : '' }}>Robusta</option>
                    <option value="Blend" {{ request('coffee_type') === 'Blend' ? 'selected' : '' }}>Blend</option>
                </select>
            </div>
            <div class="col-md-2 d-flex gap-2">
                <button type="submit" class="btn btn-outline-coffee flex-grow-1">
                    <i class="bi bi-funnel me-1"></i> Filter
                </button>
                @if(request()->hasAny(['search', 'category_id', 'coffee_type']))
                    <a href="{{ route('store.catalog') }}" class="btn btn-outline-secondary" title="Reset">
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
                <div class="card card-custom h-100 overflow-hidden d-flex flex-column border">
                    <div class="p-4 bg-light border-bottom text-center">
                        <span class="fs-1 text-warning"><i class="bi bi-cup-hot-fill"></i></span>
                        <div class="badge bg-secondary bg-opacity-10 text-secondary border mt-2">
                            {{ $product->category->name ?? 'Kopi Kemasan' }}
                        </div>
                    </div>

                    <div class="p-4 flex-grow-1 d-flex flex-column">
                        <div class="d-flex justify-content-between align-items-center mb-1">
                            <span class="badge bg-coffee text-white">{{ $product->coffee_type }}</span>
                            <small class="text-muted">{{ $product->brand }}</small>
                        </div>
                        <h5 class="fw-bold text-dark mb-2">{{ $product->name }}</h5>
                        <p class="text-muted small mb-3 flex-grow-1">{{ Str::limit($product->description, 90) }}</p>

                        <!-- Variants Picker & Add to Cart -->
                        <div class="border-top pt-3">
                            <h6 class="small fw-bold text-secondary text-uppercase mb-2">Pilihan Kemasan & Gramasi:</h6>
                            @if($product->variants->count() > 0)
                                <form method="POST" action="{{ route('store.cart.add') }}">
                                    @csrf
                                    <div class="mb-2">
                                        <select name="product_variant_id" class="form-select form-select-sm" required>
                                            @foreach($product->variants as $variant)
                                                <option value="{{ $variant->id }}" {{ $variant->current_stock <= 0 ? 'disabled' : '' }}>
                                                    {{ $variant->variant_name }} ({{ $variant->package_weight_grams }}g) &mdash; 
                                                    {{ $variant->formatted_price }}
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
                                        <input type="number" name="quantity" class="form-control form-control-sm text-center" value="1" min="1" style="width: 70px;" required>
                                        <button type="submit" class="btn btn-sm btn-coffee flex-grow-1">
                                            <i class="bi bi-cart-plus me-1"></i> Beli Sekarang
                                        </button>
                                    </div>
                                </form>
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
