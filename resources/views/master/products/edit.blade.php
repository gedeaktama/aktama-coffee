@extends('layouts.app')

@section('title', 'Edit Produk - Kopi Distro')

@section('content')
<div class="container py-3">
    <!-- Breadcrumb & Header -->
    <div class="mb-4">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-1">
                <li class="breadcrumb-item"><a href="{{ route('dashboard') }}" class="text-decoration-none">Dashboard</a></li>
                <li class="breadcrumb-item"><a href="{{ route('products.index') }}" class="text-decoration-none">Master Produk</a></li>
                <li class="breadcrumb-item"><a href="{{ route('products.show', $product) }}" class="text-decoration-none">{{ $product->name }}</a></li>
                <li class="breadcrumb-item active">Edit</li>
            </ol>
        </nav>
        <h3 class="fw-bold mb-0 text-dark"><i class="bi bi-pencil-square me-2 text-primary"></i> Edit Informasi Produk</h3>
    </div>

    <div class="card card-custom p-4">
        <form method="POST" action="{{ route('products.update', $product) }}">
            @csrf
            @method('PUT')

            <div class="row g-3">
                <div class="col-md-6">
                    <label class="form-label fw-semibold">Kategori Kopi <span class="text-danger">*</span></label>
                    <select name="category_id" class="form-select @error('category_id') is-invalid @enderror" required>
                        @foreach($categories as $cat)
                            <option value="{{ $cat->id }}" {{ old('category_id', $product->category_id) == $cat->id ? 'selected' : '' }}>
                                {{ $cat->name }}
                            </option>
                        @endforeach
                    </select>
                    @error('category_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>

                <div class="col-md-6">
                    <label class="form-label fw-semibold">Nama Produk <span class="text-danger">*</span></label>
                    <input type="text" name="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name', $product->name) }}" required>
                    @error('name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>

                <div class="col-md-4">
                    <label class="form-label fw-semibold">Merek / Brand <span class="text-danger">*</span></label>
                    <input type="text" name="brand" class="form-control" value="{{ old('brand', $product->brand) }}" required>
                </div>

                <div class="col-md-4">
                    <label class="form-label fw-semibold">Jenis Kopi <span class="text-danger">*</span></label>
                    <select name="coffee_type" class="form-select" required>
                        <option value="Arabika" {{ old('coffee_type', $product->coffee_type) == 'Arabika' ? 'selected' : '' }}>Arabika</option>
                        <option value="Robusta" {{ old('coffee_type', $product->coffee_type) == 'Robusta' ? 'selected' : '' }}>Robusta</option>
                        <option value="Blend" {{ old('coffee_type', $product->coffee_type) == 'Blend' ? 'selected' : '' }}>House Blend / Campuran</option>
                    </select>
                </div>

                <div class="col-md-4">
                    <label class="form-label fw-semibold">Bentuk Fisik <span class="text-danger">*</span></label>
                    <select name="form" class="form-select" required>
                        <option value="both" {{ old('form', $product->form) == 'both' ? 'selected' : '' }}>Tersedia Biji & Bubuk</option>
                        <option value="bean" {{ old('form', $product->form) == 'bean' ? 'selected' : '' }}>Khusus Biji Kopi</option>
                        <option value="ground" {{ old('form', $product->form) == 'ground' ? 'selected' : '' }}>Khusus Kopi Bubuk</option>
                    </select>
                </div>

                <div class="col-12">
                    <label class="form-label fw-semibold">Deskripsi & Catatan Rasa</label>
                    <textarea name="description" class="form-control" rows="4">{{ old('description', $product->description) }}</textarea>
                </div>

                <div class="col-md-4">
                    <label class="form-label fw-semibold">Status Produk <span class="text-danger">*</span></label>
                    <select name="is_active" class="form-select">
                        <option value="1" {{ old('is_active', $product->is_active) ? 'selected' : '' }}>Aktif</option>
                        <option value="0" {{ !old('is_active', $product->is_active) ? 'selected' : '' }}>Nonaktif</option>
                    </select>
                </div>
            </div>

            <div class="d-flex justify-content-end gap-2 mt-4 pt-3 border-top">
                <a href="{{ route('products.show', $product) }}" class="btn btn-light px-4">Batal</a>
                <button type="submit" class="btn btn-coffee px-5">Simpan Perubahan</button>
            </div>
        </form>
    </div>
</div>
@endsection
