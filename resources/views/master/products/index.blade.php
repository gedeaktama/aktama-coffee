@extends('layouts.app')

@section('title', 'Master Produk Kopi - Kopi Distro')

@section('content')
<div class="container py-3">
    <!-- Breadcrumb & Header -->
    <div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-4">
        <div>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-1">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}" class="text-decoration-none">Dashboard</a></li>
                    <li class="breadcrumb-item active">Master Produk</li>
                </ol>
            </nav>
            <h3 class="fw-bold mb-0 text-dark"><i class="bi bi-box-seam me-2 text-warning"></i> Produk Kopi & Varian SKU</h3>
        </div>
        <a href="{{ route('products.create') }}" class="btn btn-coffee">
            <i class="bi bi-plus-lg me-1"></i> Tambah Produk Baru
        </a>
    </div>

    <!-- Filter Bar -->
    <div class="card card-custom p-3 mb-4">
        <form method="GET" action="{{ route('products.index') }}" class="row g-2 align-items-center">
            <div class="col-md-4">
                <div class="input-group">
                    <span class="input-group-text bg-light"><i class="bi bi-search"></i></span>
                    <input type="text" name="search" class="form-control" placeholder="Cari nama, merek, atau SKU..." value="{{ request('search') }}">
                </div>
            </div>
            <div class="col-md-3">
                <select name="category_id" class="form-select">
                    <option value="">Semua Kategori</option>
                    @foreach($categories as $cat)
                        <option value="{{ $cat->id }}" {{ request('category_id') == $cat->id ? 'selected' : '' }}>
                            {{ $cat->name }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-3">
                <select name="coffee_type" class="form-select">
                    <option value="">Semua Jenis Kopi</option>
                    <option value="Arabika" {{ request('coffee_type') == 'Arabika' ? 'selected' : '' }}>Arabika</option>
                    <option value="Robusta" {{ request('coffee_type') == 'Robusta' ? 'selected' : '' }}>Robusta</option>
                    <option value="Blend" {{ request('coffee_type') == 'Blend' ? 'selected' : '' }}>House Blend</option>
                </select>
            </div>
            <div class="col-md-2 d-flex gap-2">
                <button type="submit" class="btn btn-dark w-100"><i class="bi bi-filter"></i> Filter</button>
                @if(request()->hasAny(['search', 'category_id', 'coffee_type']))
                    <a href="{{ route('products.index') }}" class="btn btn-outline-secondary" title="Reset Filter"><i class="bi bi-x-lg"></i></a>
                @endif
            </div>
        </form>
    </div>

    <!-- Products Table -->
    <div class="card card-custom p-0 overflow-hidden">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th class="ps-4">Nama Produk & Merek</th>
                        <th>Kategori</th>
                        <th>Jenis Kopi</th>
                        <th>Daftar Varian & Satu Harga Standar (SKU)</th>
                        <th class="text-center">Total Stok</th>
                        <th class="text-center">Status</th>
                        <th class="text-end pe-4">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($products as $product)
                        @php
                            $totalStock = $product->variants->sum('current_stock');
                        @endphp
                        <tr>
                            <td class="ps-4">
                                <div class="fw-bold text-dark fs-6">{{ $product->name }}</div>
                                <small class="text-muted"><i class="bi bi-tag-fill text-warning me-1"></i>{{ $product->brand }}</small>
                            </td>
                            <td>
                                <span class="badge bg-light text-dark border">{{ $product->category?->name ?? 'Uncategorized' }}</span>
                            </td>
                            <td>
                                <span class="badge bg-info bg-opacity-10 text-info border border-info border-opacity-25">{{ $product->coffee_type }}</span>
                            </td>
                            <td>
                                <div class="d-flex flex-column gap-1">
                                    @foreach($product->variants as $variant)
                                        <div class="small d-flex align-items-center gap-2">
                                            <code class="fw-bold">{{ $variant->sku }}</code>
                                            <span class="text-muted">({{ $variant->package_weight_grams }}g):</span>
                                            <strong class="text-success">{{ $variant->formatted_price }}</strong>
                                            <span class="badge {{ $variant->isLowStock() ? 'bg-danger' : 'bg-light text-muted border' }}" style="font-size: 0.7rem;">
                                                stok: {{ $variant->current_stock }}
                                            </span>
                                        </div>
                                    @endforeach
                                </div>
                            </td>
                            <td class="text-center">
                                <span class="fw-bold fs-6 {{ $totalStock <= 10 ? 'text-danger' : 'text-dark' }}">
                                    {{ $totalStock }} unit
                                </span>
                            </td>
                            <td class="text-center">
                                @if($product->is_active)
                                    <span class="badge bg-success bg-opacity-10 text-success border border-success border-opacity-25">Aktif</span>
                                @else
                                    <span class="badge bg-danger bg-opacity-10 text-danger border border-danger border-opacity-25">Nonaktif</span>
                                @endif
                            </td>
                            <td class="text-end pe-4">
                                <a href="{{ route('products.show', $product) }}" class="btn btn-sm btn-outline-dark me-1" title="Kelola Varian & Detail">
                                    <i class="bi bi-gear-fill me-1"></i> Varian
                                </a>
                                <a href="{{ route('products.edit', $product) }}" class="btn btn-sm btn-outline-primary me-1" title="Edit Produk">
                                    <i class="bi bi-pencil"></i>
                                </a>
                                <form action="{{ route('products.toggle', $product) }}" method="POST" class="d-inline">
                                    @csrf
                                    @method('PATCH')
                                    <button type="submit" class="btn btn-sm {{ $product->is_active ? 'btn-outline-danger' : 'btn-outline-success' }}" 
                                        title="{{ $product->is_active ? 'Nonaktifkan' : 'Aktifkan' }}"
                                        onclick="return confirm('Ubah status produk ini?')">
                                        <i class="bi {{ $product->is_active ? 'bi-slash-circle' : 'bi-check-circle' }}"></i>
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center py-5 text-muted">
                                <i class="bi bi-box2 text-muted fs-1 d-block mb-2"></i>
                                Tidak ada produk kopi yang sesuai kriteria.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($products->hasPages())
            <div class="p-3 border-top">
                {{ $products->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
