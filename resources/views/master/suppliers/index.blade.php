@extends('layouts.app')

@section('title', 'Master Supplier Kopi - Kopi Distro')

@section('content')
<div class="container py-3">
    <!-- Breadcrumb & Header -->
    <div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-4">
        <div>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-1">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}" class="text-decoration-none">Dashboard</a></li>
                    <li class="breadcrumb-item active">Master Pemasok</li>
                </ol>
            </nav>
            <h3 class="fw-bold mb-0 text-dark"><i class="bi bi-truck me-2 text-warning"></i> Pemasok / Supplier Kopi</h3>
        </div>
        <button type="button" class="btn btn-coffee" data-bs-toggle="modal" data-bs-target="#modalAddSupplier">
            <i class="bi bi-plus-lg me-1"></i> Tambah Supplier Baru
        </button>
    </div>

    <!-- Table Card -->
    <div class="card card-custom p-0 overflow-hidden">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th class="ps-4">Kode Supplier</th>
                        <th>Nama Perusahaan</th>
                        <th>Penanggung Jawab (PIC)</th>
                        <th>Kontak Telepon</th>
                        <th>Alamat</th>
                        <th class="text-center">Status</th>
                        <th class="text-end pe-4">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($suppliers as $supplier)
                        <tr>
                            <td class="ps-4"><code>{{ $supplier->code }}</code></td>
                            <td class="fw-bold text-dark">{{ $supplier->name }}</td>
                            <td>{{ $supplier->pic_name }}</td>
                            <td>
                                <div><i class="bi bi-telephone text-muted me-1"></i>{{ $supplier->phone }}</div>
                                @if($supplier->email)
                                    <small class="text-muted"><i class="bi bi-envelope text-muted me-1"></i>{{ $supplier->email }}</small>
                                @endif
                            </td>
                            <td class="text-muted small">{{ Str::limit($supplier->address, 50) ?: '-' }}</td>
                            <td class="text-center">
                                @if($supplier->is_active)
                                    <span class="badge bg-success bg-opacity-10 text-success border border-success border-opacity-25">Aktif</span>
                                @else
                                    <span class="badge bg-danger bg-opacity-10 text-danger border border-danger border-opacity-25">Nonaktif</span>
                                @endif
                            </td>
                            <td class="text-end pe-4">
                                <button type="button" class="btn btn-sm btn-outline-primary me-1" data-bs-toggle="modal" data-bs-target="#modalEditSupplier{{ $supplier->id }}">
                                    <i class="bi bi-pencil"></i>
                                </button>
                                <form action="{{ route('suppliers.toggle', $supplier) }}" method="POST" class="d-inline">
                                    @csrf
                                    @method('PATCH')
                                    <button type="submit" class="btn btn-sm {{ $supplier->is_active ? 'btn-outline-danger' : 'btn-outline-success' }}" 
                                        title="{{ $supplier->is_active ? 'Nonaktifkan' : 'Aktifkan' }}"
                                        onclick="return confirm('Ubah status supplier ini?')">
                                        <i class="bi {{ $supplier->is_active ? 'bi-slash-circle' : 'bi-check-circle' }}"></i>
                                    </button>
                                </form>
                            </td>
                        </tr>

                        <!-- Modal Edit Supplier -->
                        <div class="modal fade" id="modalEditSupplier{{ $supplier->id }}" tabindex="-1" aria-hidden="true">
                            <div class="modal-dialog">
                                <div class="modal-content card-custom">
                                    <form action="{{ route('suppliers.update', $supplier) }}" method="POST">
                                        @csrf
                                        @method('PUT')
                                        <div class="modal-header border-0 pb-0">
                                            <h5 class="modal-title fw-bold">Edit Supplier: {{ $supplier->code }}</h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                        </div>
                                        <div class="modal-body">
                                            <div class="mb-3">
                                                <label class="form-label fw-semibold">Nama Perusahaan</label>
                                                <input type="text" name="name" class="form-control" value="{{ $supplier->name }}" required>
                                            </div>
                                            <div class="mb-3">
                                                <label class="form-label fw-semibold">Penanggung Jawab (PIC)</label>
                                                <input type="text" name="pic_name" class="form-control" value="{{ $supplier->pic_name }}" required>
                                            </div>
                                            <div class="row">
                                                <div class="col-md-6 mb-3">
                                                    <label class="form-label fw-semibold">Nomor Telepon</label>
                                                    <input type="text" name="phone" class="form-control" value="{{ $supplier->phone }}" required>
                                                </div>
                                                <div class="col-md-6 mb-3">
                                                    <label class="form-label fw-semibold">Email</label>
                                                    <input type="email" name="email" class="form-control" value="{{ $supplier->email }}">
                                                </div>
                                            </div>
                                            <div class="mb-3">
                                                <label class="form-label fw-semibold">Alamat</label>
                                                <textarea name="address" class="form-control" rows="3">{{ $supplier->address }}</textarea>
                                            </div>
                                            <div class="mb-3">
                                                <label class="form-label fw-semibold">Status</label>
                                                <select name="is_active" class="form-select">
                                                    <option value="1" {{ $supplier->is_active ? 'selected' : '' }}>Aktif</option>
                                                    <option value="0" {{ !$supplier->is_active ? 'selected' : '' }}>Nonaktif</option>
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
                            <td colspan="7" class="text-center py-4 text-muted">Belum ada data supplier pemasok kopi.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($suppliers->hasPages())
            <div class="p-3 border-top">
                {{ $suppliers->links() }}
            </div>
        @endif
    </div>
</div>

<!-- Modal Add Supplier -->
<div class="modal fade" id="modalAddSupplier" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content card-custom">
            <form action="{{ route('suppliers.store') }}" method="POST">
                @csrf
                <div class="modal-header border-0 pb-0">
                    <h5 class="modal-title fw-bold">Tambah Supplier Pemasok Kopi</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-5 mb-3">
                            <label class="form-label fw-semibold">Kode Supplier <span class="text-danger">*</span></label>
                            <input type="text" name="code" class="form-control" placeholder="SUP-003" required>
                        </div>
                        <div class="col-md-7 mb-3">
                            <label class="form-label fw-semibold">Nama Perusahaan <span class="text-danger">*</span></label>
                            <input type="text" name="name" class="form-control" placeholder="PT Mitra Kopi Alam" required>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Penanggung Jawab (PIC) <span class="text-danger">*</span></label>
                        <input type="text" name="pic_name" class="form-control" placeholder="Nama kontak supplier" required>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-semibold">Nomor Telepon <span class="text-danger">*</span></label>
                            <input type="text" name="phone" class="form-control" placeholder="08123456789" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-semibold">Email</label>
                            <input type="email" name="email" class="form-control" placeholder="info@supplier.com">
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Alamat Lengkap</label>
                        <textarea name="address" class="form-control" rows="3" placeholder="Alamat gudang / kantor supplier..."></textarea>
                    </div>
                </div>
                <div class="modal-footer border-0 pt-0">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-coffee">Simpan Supplier</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
