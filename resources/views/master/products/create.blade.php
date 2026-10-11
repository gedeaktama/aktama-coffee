@extends('layouts.app')

@section('title', 'Tambah Produk Kopi - Kopi Distro')

@section('content')
<div class="container py-3">
    <!-- Breadcrumb & Header -->
    <div class="mb-4">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-1">
                <li class="breadcrumb-item"><a href="{{ route('dashboard') }}" class="text-decoration-none">Dashboard</a></li>
                <li class="breadcrumb-item"><a href="{{ route('products.index') }}" class="text-decoration-none">Master Produk</a></li>
                <li class="breadcrumb-item active">Tambah Produk</li>
            </ol>
        </nav>
        <h3 class="fw-bold mb-0 text-dark"><i class="bi bi-plus-circle me-2 text-warning"></i> Tambah Produk Kopi Baru</h3>
    </div>

    <form method="POST" action="{{ route('products.store') }}">
        @csrf

        <div class="row g-4">
            <!-- Product Information -->
            <div class="col-lg-6">
                <div class="card card-custom p-4 h-100">
                    <h5 class="fw-bold mb-3 pb-2 border-bottom text-dark">
                        <i class="bi bi-info-circle me-2 text-primary"></i> 1. Informasi Utama Produk
                    </h5>

                    <div class="mb-3">
                        <label class="form-label fw-semibold">Kategori Kopi <span class="text-danger">*</span></label>
                        <select name="category_id" class="form-select @error('category_id') is-invalid @enderror" required>
                            <option value="">-- Pilih Kategori --</option>
                            @foreach($categories as $cat)
                                <option value="{{ $cat->id }}" {{ old('category_id') == $cat->id ? 'selected' : '' }}>
                                    {{ $cat->name }}
                                </option>
                            @endforeach
                        </select>
                        @error('category_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold">Nama Produk <span class="text-danger">*</span></label>
                        <input type="text" name="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name') }}" placeholder="Contoh: Kopi Arabika Aceh Gayo" required>
                        @error('name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-semibold">Merek / Brand <span class="text-danger">*</span></label>
                            <input type="text" name="brand" class="form-control @error('brand') is-invalid @enderror" value="{{ old('brand', 'Kopi Distro Premium') }}" required>
                            @error('brand') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-semibold">Jenis Kopi <span class="text-danger">*</span></label>
                            <select name="coffee_type" class="form-select" required>
                                <option value="Arabika" {{ old('coffee_type') == 'Arabika' ? 'selected' : '' }}>Arabika</option>
                                <option value="Robusta" {{ old('coffee_type') == 'Robusta' ? 'selected' : '' }}>Robusta</option>
                                <option value="Blend" {{ old('coffee_type') == 'Blend' ? 'selected' : '' }}>House Blend / Campuran</option>
                            </select>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold">Bentuk Fisik <span class="text-danger">*</span></label>
                        <select name="form" class="form-select" required>
                            <option value="both" {{ old('form') == 'both' ? 'selected' : '' }}>Tersedia Biji & Bubuk</option>
                            <option value="bean" {{ old('form') == 'bean' ? 'selected' : '' }}>Khusus Biji Kopi (Whole Beans)</option>
                            <option value="ground" {{ old('form') == 'ground' ? 'selected' : '' }}>Khusus Kopi Bubuk (Ground)</option>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold">Deskripsi & Catatan Rasa (Tasting Notes)</label>
                        <textarea name="description" class="form-control" rows="4" placeholder="Profil aroma, keasaman (acidity), body, dan karakteristik kopi...">{{ old('description') }}</textarea>
                    </div>
                </div>
            </div>

            <!-- Initial Variant Information -->
            <div class="col-lg-6">
                <div class="card card-custom p-4 h-100">
                    <h5 class="fw-bold mb-3 pb-2 border-bottom text-dark">
                        <i class="bi bi-upc-scan me-2 text-success"></i> 2. Varian Kemasan & SKU Awal
                    </h5>
                    <p class="text-muted small">Setiap ukuran kemasan memiliki SKU unik tersendiri. Anda dapat menambahkan varian ukuran lainnya setelah produk disimpan.</p>

                    <div class="mb-3">
                        <label class="form-label fw-semibold">Nama Varian <span class="text-danger">*</span></label>
                        <input type="text" name="variant_name" class="form-control @error('variant_name') is-invalid @enderror" value="{{ old('variant_name', 'Kemasan 250g (Biji/Bubuk)') }}" required>
                        @error('variant_name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-semibold">Kode SKU Unik <span class="text-danger">*</span></label>
                            <input type="text" name="sku" class="form-control @error('sku') is-invalid @enderror" value="{{ old('sku') }}" placeholder="Contoh: SKU-AG-250G" required>
                            <small class="text-muted" style="font-size: 0.72rem;">Identifier unik untuk stok & transaksi</small>
                            @error('sku') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-semibold">Ukuran Kemasan (Gram) <span class="text-danger">*</span></label>
                            <select name="package_weight_grams" class="form-select" required>
                                <option value="100" {{ old('package_weight_grams') == 100 ? 'selected' : '' }}>100 Gram</option>
                                <option value="250" {{ old('package_weight_grams', 250) == 250 ? 'selected' : '' }}>250 Gram</option>
                                <option value="500" {{ old('package_weight_grams') == 500 ? 'selected' : '' }}>500 Gram</option>
                                <option value="1000" {{ old('package_weight_grams') == 1000 ? 'selected' : '' }}>1 Kilogram (1000g)</option>
                            </select>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold">Bentuk Gilingan / Roast <span class="text-danger">*</span></label>
                        <select name="grind_form" class="form-select" required>
                            <option value="Biji Kopi" {{ old('grind_form') == 'Biji Kopi' ? 'selected' : '' }}>Biji Kopi (Roasted Beans)</option>
                            <option value="Bubuk Halus" {{ old('grind_form') == 'Bubuk Halus' ? 'selected' : '' }}>Bubuk Halus (Fine - Espresso/Tubruk)</option>
                            <option value="Bubuk Sedang" {{ old('grind_form') == 'Bubuk Sedang' ? 'selected' : '' }}>Bubuk Sedang (Medium - V60/Filter)</option>
                            <option value="Bubuk Kasar" {{ old('grind_form') == 'Bubuk Kasar' ? 'selected' : '' }}>Bubuk Kasar (Coarse - French Press/Cold Brew)</option>
                        </select>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-semibold">Harga Beli Supplier (Rp) <span class="text-danger">*</span></label>
                            <input type="number" step="100" name="cost_price" class="form-control @error('cost_price') is-invalid @enderror" value="{{ old('cost_price', 65000) }}" required>
                            <small class="text-muted" style="font-size: 0.72rem;">Dasar HPP pembelian</small>
                            @error('cost_price') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-semibold">Satu Harga Jual Standar (Rp) <span class="text-danger">*</span></label>
                            <input type="number" step="100" name="selling_price" class="form-control @error('selling_price') is-invalid @enderror" value="{{ old('selling_price', 90000) }}" required>
                            <small class="text-success fw-semibold" style="font-size: 0.72rem;">Satu harga berlaku untuk retail & grosir</small>
                            @error('selling_price') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-semibold">Saldo Stok Awal <span class="text-danger">*</span></label>
                            <input type="number" name="current_stock" class="form-control" value="{{ old('current_stock', 0) }}" min="0" required>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-semibold">Peringatan Stok Minimum <span class="text-danger">*</span></label>
                            <input type="number" name="minimum_stock" class="form-control" value="{{ old('minimum_stock', 10) }}" min="1" required>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="d-flex justify-content-end gap-2 mt-4">
            <a href="{{ route('products.index') }}" class="btn btn-light px-4">Batal</a>
            <button type="submit" class="btn btn-coffee px-5">
                <i class="bi bi-check-circle me-1"></i> Simpan Produk & Varian
            </button>
        </div>
    </form>
</div>
@endsection
