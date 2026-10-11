@extends('layouts.app')

@section('title', $product->name . ' - Detail Produk - Kopi Distro')

@section('content')
<div class="container py-3">
    <!-- Breadcrumb & Header -->
    <div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-4">
        <div>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-1">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}" class="text-decoration-none">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('products.index') }}" class="text-decoration-none">Master Produk</a></li>
                    <li class="breadcrumb-item active">{{ $product->name }}</li>
                </ol>
            </nav>
            <h3 class="fw-bold mb-0 text-dark">{{ $product->name }}</h3>
        </div>
        <div class="d-flex gap-2">
            <button type="button" class="btn btn-coffee" data-bs-toggle="modal" data-bs-target="#modalAddVariant">
                <i class="bi bi-plus-lg me-1"></i> Tambah Varian SKU
            </button>
            <a href="{{ route('products.edit', $product) }}" class="btn btn-outline-primary">
                <i class="bi bi-pencil me-1"></i> Edit Info Produk
            </a>
        </div>
    </div>

    <!-- Product Summary Card -->
    <div class="card card-custom p-4 mb-4">
        <div class="row g-4 align-items-center">
            <div class="col-md-8">
                <div class="d-flex flex-wrap gap-2 mb-2">
                    <span class="badge bg-light text-dark border">{{ $product->category?->name ?? 'Uncategorized' }}</span>
                    <span class="badge bg-info bg-opacity-10 text-info border border-info border-opacity-25">{{ $product->coffee_type }}</span>
                    <span class="badge bg-secondary bg-opacity-10 text-secondary border">{{ $product->form }}</span>
                    @if($product->is_active)
                        <span class="badge bg-success bg-opacity-10 text-success border border-success border-opacity-25">Aktif</span>
                    @else
                        <span class="badge bg-danger bg-opacity-10 text-danger border border-danger border-opacity-25">Nonaktif</span>
                    @endif
                </div>
                <h4 class="fw-bold text-dark mb-1">{{ $product->name }}</h4>
                <p class="text-muted small mb-2"><i class="bi bi-tag me-1"></i> Merek: <strong>{{ $product->brand }}</strong></p>
                <p class="text-secondary small mb-0">{{ $product->description ?: 'Belum ada deskripsi profil rasa.' }}</p>
            </div>
            <div class="col-md-4 text-md-end">
                <div class="p-3 rounded-3 bg-light d-inline-block text-start border">
                    <small class="text-muted d-block">Total Varian Terdaftar</small>
                    <span class="fw-bold fs-4 text-dark">{{ $product->variants->count() }} SKU</span>
                    <small class="text-muted d-block mt-1">Total Stok Tersedia: <strong>{{ $product->variants->sum('current_stock') }} pack</strong></small>
                </div>
            </div>
        </div>
    </div>

    <!-- Variants Table Card -->
    <div class="card card-custom p-0 overflow-hidden">
        <div class="p-3 border-bottom bg-light d-flex justify-content-between align-items-center">
            <h5 class="fw-bold mb-0 text-dark">
                <i class="bi bi-upc-scan me-2 text-warning"></i> Daftar Varian Kemasan & SKU
            </h5>
            <small class="text-success fw-semibold">Satu Harga Jual Standar per SKU Berlaku untuk Semua Pelanggan</small>
        </div>

        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th class="ps-4">Kode SKU</th>
                        <th>Nama Varian</th>
                        <th>Ukuran Kemasan</th>
                        <th>Bentuk Gilingan</th>
                        <th class="text-end">Harga Beli</th>
                        <th class="text-end">Harga Jual Standar</th>
                        <th class="text-center">Stok Fisik</th>
                        <th class="text-center">Status</th>
                        <th class="text-end pe-4">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($product->variants as $variant)
                        <tr>
                            <td class="ps-4">
                                <code class="fw-bold fs-6">{{ $variant->sku }}</code>
                            </td>
                            <td class="fw-semibold text-dark">{{ $variant->variant_name }}</td>
                            <td><span class="badge bg-light text-dark border">{{ $variant->package_weight_grams }} Gram</span></td>
                            <td class="small text-muted">{{ $variant->grind_form }}</td>
                            <td class="text-end text-muted small">{{ $variant->formatted_cost }}</td>
                            <td class="text-end fw-bold text-success">{{ $variant->formatted_price }}</td>
                            <td class="text-center">
                                <span class="badge {{ $variant->isLowStock() ? 'bg-danger' : 'bg-success' }} px-2 py-1">
                                    {{ $variant->current_stock }} unit
                                </span>
                                @if($variant->isLowStock())
                                    <small class="text-danger d-block" style="font-size: 0.68rem;">Menipis (Min: {{ $variant->minimum_stock }})</small>
                                @endif
                            </td>
                            <td class="text-center">
                                @if($variant->is_active)
                                    <span class="badge bg-success bg-opacity-10 text-success border border-success border-opacity-25">Aktif</span>
                                @else
                                    <span class="badge bg-danger bg-opacity-10 text-danger border border-danger border-opacity-25">Nonaktif</span>
                                @endif
                            </td>
                            <td class="text-end pe-4">
                                <button type="button" class="btn btn-sm btn-outline-primary" data-bs-toggle="modal" data-bs-target="#modalEditVariant{{ $variant->id }}">
                                    <i class="bi bi-pencil me-1"></i> Edit Varian
                                </button>
                            </td>
                        </tr>

                        <!-- Modal Edit Variant -->
                        <div class="modal fade" id="modalEditVariant{{ $variant->id }}" tabindex="-1" aria-hidden="true">
                            <div class="modal-dialog">
                                <div class="modal-content card-custom">
                                    <form action="{{ route('variants.update', $variant) }}" method="POST">
                                        @csrf
                                        @method('PUT')
                                        <div class="modal-header border-0 pb-0">
                                            <h5 class="modal-title fw-bold">Edit Varian: {{ $variant->sku }}</h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                        </div>
                                        <div class="modal-body">
                                            <div class="mb-3">
                                                <label class="form-label fw-semibold">Nama Varian</label>
                                                <input type="text" name="variant_name" class="form-control" value="{{ $variant->variant_name }}" required>
                                            </div>
                                            <div class="row">
                                                <div class="col-md-6 mb-3">
                                                    <label class="form-label fw-semibold">Ukuran Kemasan (Gram)</label>
                                                    <input type="number" name="package_weight_grams" class="form-control" value="{{ $variant->package_weight_grams }}" required>
                                                </div>
                                                <div class="col-md-6 mb-3">
                                                    <label class="form-label fw-semibold">Bentuk Gilingan</label>
                                                    <select name="grind_form" class="form-select">
                                                        <option value="Biji Kopi" {{ $variant->grind_form == 'Biji Kopi' ? 'selected' : '' }}>Biji Kopi</option>
                                                        <option value="Bubuk Halus" {{ $variant->grind_form == 'Bubuk Halus' ? 'selected' : '' }}>Bubuk Halus</option>
                                                        <option value="Bubuk Sedang" {{ $variant->grind_form == 'Bubuk Sedang' ? 'selected' : '' }}>Bubuk Sedang</option>
                                                        <option value="Bubuk Kasar" {{ $variant->grind_form == 'Bubuk Kasar' ? 'selected' : '' }}>Bubuk Kasar</option>
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="col-md-6 mb-3">
                                                    <label class="form-label fw-semibold">Harga Beli Supplier (Rp)</label>
                                                    <input type="number" step="100" name="cost_price" class="form-control" value="{{ (int)$variant->cost_price }}" required>
                                                </div>
                                                <div class="col-md-6 mb-3">
                                                    <label class="form-label fw-semibold">Harga Jual Standar (Rp)</label>
                                                    <input type="number" step="100" name="selling_price" class="form-control" value="{{ (int)$variant->selling_price }}" required>
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="col-md-6 mb-3">
                                                    <label class="form-label fw-semibold">Stok Saat Ini</label>
                                                    <input type="number" name="current_stock" class="form-control" value="{{ $variant->current_stock }}" required>
                                                </div>
                                                <div class="col-md-6 mb-3">
                                                    <label class="form-label fw-semibold">Stok Minimum</label>
                                                    <input type="number" name="minimum_stock" class="form-control" value="{{ $variant->minimum_stock }}" required>
                                                </div>
                                            </div>
                                            <div class="mb-3">
                                                <label class="form-label fw-semibold">Status Varian</label>
                                                <select name="is_active" class="form-select">
                                                    <option value="1" {{ $variant->is_active ? 'selected' : '' }}>Aktif</option>
                                                    <option value="0" {{ !$variant->is_active ? 'selected' : '' }}>Nonaktif</option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="modal-footer border-0 pt-0">
                                            <button type="button" class="btn btn-light" data-bs-dismiss="modal">Batal</button>
                                            <button type="submit" class="btn btn-coffee">Simpan Perubahan</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    @empty
                        <tr>
                            <td colspan="9" class="text-center py-4 text-muted">Belum ada varian SKU pada produk ini.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Modal Add Variant -->
<div class="modal fade" id="modalAddVariant" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content card-custom">
            <form action="{{ route('products.variants.store', $product) }}" method="POST">
                @csrf
                <div class="modal-header border-0 pb-0">
                    <h5 class="modal-title fw-bold">Tambah Varian SKU Baru</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Kode SKU Unik <span class="text-danger">*</span></label>
                        <input type="text" name="sku" class="form-control" placeholder="Contoh: SKU-AG-1KG" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Nama Varian <span class="text-danger">*</span></label>
                        <input type="text" name="variant_name" class="form-control" placeholder="Contoh: Kemasan 1kg (Biji/Bubuk)" required>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-semibold">Ukuran Kemasan (Gram)</label>
                            <input type="number" name="package_weight_grams" class="form-control" value="250" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-semibold">Bentuk Gilingan</label>
                            <select name="grind_form" class="form-select">
                                <option value="Biji Kopi">Biji Kopi</option>
                                <option value="Bubuk Halus">Bubuk Halus</option>
                                <option value="Bubuk Sedang">Bubuk Sedang</option>
                                <option value="Bubuk Kasar">Bubuk Kasar</option>
                            </select>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-semibold">Harga Beli Supplier (Rp)</label>
                            <input type="number" step="100" name="cost_price" class="form-control" value="50000" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-semibold">Harga Jual Standar (Rp)</label>
                            <input type="number" step="100" name="selling_price" class="form-control" value="75000" required>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-semibold">Stok Awal</label>
                            <input type="number" name="current_stock" class="form-control" value="0" min="0" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-semibold">Stok Minimum</label>
                            <input type="number" name="minimum_stock" class="form-control" value="10" min="1" required>
                        </div>
                    </div>
                </div>
                <div class="modal-footer border-0 pt-0">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-coffee">Simpan Varian Baru</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
