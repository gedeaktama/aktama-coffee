@extends('layouts.app')

@section('title', 'Master Kategori Kopi - Kopi Distro')

@section('content')
<div class="container py-3">
    <!-- Breadcrumb & Header -->
    <div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-4">
        <div>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-1">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}" class="text-decoration-none">Dashboard</a></li>
                    <li class="breadcrumb-item active">Master Kategori</li>
                </ol>
            </nav>
            <h3 class="fw-bold mb-0 text-dark"><i class="bi bi-tags me-2 text-warning"></i> Kategori Produk Kopi</h3>
        </div>
        <button type="button" class="btn btn-coffee" data-bs-toggle="modal" data-bs-target="#modalAddCategory">
            <i class="bi bi-plus-lg me-1"></i> Tambah Kategori
        </button>
    </div>

    <!-- Table Card -->
    <div class="card card-custom p-0 overflow-hidden">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th class="ps-4">Nama Kategori</th>
                        <th>Slug</th>
                        <th>Deskripsi</th>
                        <th class="text-center">Jumlah Produk</th>
                        <th class="text-center">Status</th>
                        <th class="text-end pe-4">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($categories as $category)
                        <tr>
                            <td class="ps-4 fw-semibold text-dark">{{ $category->name }}</td>
                            <td><code>{{ $category->slug }}</code></td>
                            <td class="text-muted small">{{ $category->description ?? '-' }}</td>
                            <td class="text-center">
                                <span class="badge bg-secondary rounded-pill px-2 py-1">{{ $category->products_count }} produk</span>
                            </td>
                            <td class="text-center">
                                @if($category->is_active)
                                    <span class="badge bg-success bg-opacity-10 text-success border border-success border-opacity-25">Aktif</span>
                                @else
                                    <span class="badge bg-danger bg-opacity-10 text-danger border border-danger border-opacity-25">Nonaktif</span>
                                @endif
                            </td>
                            <td class="text-end pe-4">
                                <button type="button" class="btn btn-sm btn-outline-primary me-1" 
                                    data-bs-toggle="modal" 
                                    data-bs-target="#modalEditCategory{{ $category->id }}">
                                    <i class="bi bi-pencil"></i>
                                </button>
                                <form action="{{ route('categories.toggle', $category) }}" method="POST" class="d-inline">
                                    @csrf
                                    @method('PATCH')
                                    <button type="submit" class="btn btn-sm {{ $category->is_active ? 'btn-outline-danger' : 'btn-outline-success' }}" 
                                        title="{{ $category->is_active ? 'Nonaktifkan' : 'Aktifkan' }}"
                                        onclick="return confirm('Ubah status kategori ini?')">
                                        <i class="bi {{ $category->is_active ? 'bi-slash-circle' : 'bi-check-circle' }}"></i>
                                    </button>
                                </form>
                            </td>
                        </tr>

                        <!-- Modal Edit Category -->
                        <div class="modal fade" id="modalEditCategory{{ $category->id }}" tabindex="-1" aria-hidden="true">
                            <div class="modal-dialog">
                                <div class="modal-content card-custom">
                                    <form action="{{ route('categories.update', $category) }}" method="POST">
                                        @csrf
                                        @method('PUT')
                                        <div class="modal-header border-0 pb-0">
                                            <h5 class="modal-title fw-bold">Edit Kategori</h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                        </div>
                                        <div class="modal-body">
                                            <div class="mb-3">
                                                <label class="form-label fw-semibold">Nama Kategori</label>
                                                <input type="text" name="name" class="form-control" value="{{ $category->name }}" required>
                                            </div>
                                            <div class="mb-3">
                                                <label class="form-label fw-semibold">Deskripsi</label>
                                                <textarea name="description" class="form-control" rows="3">{{ $category->description }}</textarea>
                                            </div>
                                            <div class="mb-3">
                                                <label class="form-label fw-semibold">Status</label>
                                                <select name="is_active" class="form-select">
                                                    <option value="1" {{ $category->is_active ? 'selected' : '' }}>Aktif</option>
                                                    <option value="0" {{ !$category->is_active ? 'selected' : '' }}>Nonaktif</option>
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
                            <td colspan="6" class="text-center py-4 text-muted">Belum ada data kategori kopi.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($categories->hasPages())
            <div class="p-3 border-top">
                {{ $categories->links() }}
            </div>
        @endif
    </div>
</div>

<!-- Modal Add Category -->
<div class="modal fade" id="modalAddCategory" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content card-custom">
            <form action="{{ route('categories.store') }}" method="POST">
                @csrf
                <div class="modal-header border-0 pb-0">
                    <h5 class="modal-title fw-bold">Tambah Kategori Kopi</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Nama Kategori</label>
                        <input type="text" name="name" class="form-control" placeholder="Contoh: Kopi Luwak Asli" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Deskripsi</label>
                        <textarea name="description" class="form-control" rows="3" placeholder="Keterangan kategori produk..."></textarea>
                    </div>
                </div>
                <div class="modal-footer border-0 pt-0">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-coffee">Simpan Kategori</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
