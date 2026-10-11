@extends('layouts.app')

@section('title', 'Daftar Pembelian Supplier (PO) - Kopi Distro')

@section('content')
<div class="container py-3">
    <!-- Header Page -->
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-4">
        <div>
            <div class="d-flex align-items-center gap-2">
                <span class="badge bg-primary bg-opacity-10 text-primary px-3 py-2 rounded-pill fw-semibold">
                    <i class="bi bi-cart-check me-1"></i> Modul Pembelian (Procurement)
                </span>
                <span class="text-muted">&bull;</span>
                <span class="small text-muted">Tahap 3: Persediaan & Pembelian</span>
            </div>
            <h2 class="fw-bold text-dark mt-2 mb-1">Pesanan Pembelian ke Pemasok (PO)</h2>
            <p class="text-muted mb-0">Kelola pemesanan biji & bubuk kopi ke supplier dan konfirmasi penerimaan fisik ke gudang.</p>
        </div>
        <div>
            @if(auth()->user()->hasRole(['owner', 'admin', 'warehouse']))
                <a href="{{ route('purchases.create') }}" class="btn btn-coffee d-inline-flex align-items-center gap-2 shadow-sm">
                    <i class="bi bi-plus-circle"></i>
                    <span>Buat PO Baru</span>
                </a>
            @endif
        </div>
    </div>

    <!-- Quick Filter & Search -->
    <div class="card card-custom p-3 mb-4">
        <form method="GET" action="{{ route('purchases.index') }}" class="row g-2 align-items-center">
            <div class="col-md-4">
                <div class="input-group">
                    <span class="input-group-text bg-white border-end-0 text-muted"><i class="bi bi-search"></i></span>
                    <input type="text" name="search" class="form-control border-start-0" placeholder="Cari No. PO atau nama supplier..." value="{{ request('search') }}">
                </div>
            </div>
            <div class="col-md-3">
                <select name="status" class="form-select">
                    <option value="">-- Semua Status PO --</option>
                    <option value="ordered" {{ request('status') == 'ordered' ? 'selected' : '' }}>Menunggu Penerimaan Fisik</option>
                    <option value="received" {{ request('status') == 'received' ? 'selected' : '' }}>Sudah Diterima (Stok Masuk)</option>
                    <option value="cancelled" {{ request('status') == 'cancelled' ? 'selected' : '' }}>Dibatalkan</option>
                </select>
            </div>
            <div class="col-md-3">
                <select name="supplier_id" class="form-select">
                    <option value="">-- Semua Supplier --</option>
                    @foreach($suppliers as $supplier)
                        <option value="{{ $supplier->id }}" {{ request('supplier_id') == $supplier->id ? 'selected' : '' }}>
                            {{ $supplier->name }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2 d-flex gap-2">
                <button type="submit" class="btn btn-outline-coffee flex-grow-1">
                    <i class="bi bi-funnel me-1"></i> Filter
                </button>
                @if(request()->hasAny(['search', 'status', 'supplier_id']))
                    <a href="{{ route('purchases.index') }}" class="btn btn-outline-secondary" title="Reset Filter">
                        <i class="bi bi-x-circle"></i>
                    </a>
                @endif
            </div>
        </form>
    </div>

    <!-- Purchases Table -->
    <div class="card card-custom overflow-hidden">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th class="ps-3 py-3 text-secondary text-uppercase small fw-bold">No. PO</th>
                        <th class="py-3 text-secondary text-uppercase small fw-bold">Tanggal</th>
                        <th class="py-3 text-secondary text-uppercase small fw-bold">Pemasok / Supplier</th>
                        <th class="py-3 text-secondary text-uppercase small fw-bold">Diterbitkan Oleh</th>
                        <th class="py-3 text-secondary text-uppercase small fw-bold text-end">Total Biaya</th>
                        <th class="py-3 text-secondary text-uppercase small fw-bold text-center">Status Barang</th>
                        <th class="pe-3 py-3 text-secondary text-uppercase small fw-bold text-end">Aksi</th>
                    </tr>
                </thead>
                <tbody class="border-top-0">
                    @forelse($purchases as $po)
                        <tr>
                            <td class="ps-3 fw-bold text-dark font-monospace">
                                <a href="{{ route('purchases.show', $po) }}" class="text-decoration-none text-dark">
                                    {{ $po->purchase_number }}
                                </a>
                            </td>
                            <td class="text-muted small">
                                <i class="bi bi-calendar3 me-1"></i>{{ $po->purchase_date->format('d/m/Y') }}
                            </td>
                            <td>
                                <div class="fw-semibold text-dark">{{ $po->supplier->name }}</div>
                                <small class="text-muted"><i class="bi bi-telephone me-1"></i>{{ $po->supplier->phone ?? '-' }}</small>
                            </td>
                            <td>
                                <div class="small fw-semibold text-dark">{{ $po->user->name ?? 'Sistem' }}</div>
                                <span class="badge badge-role badge-role-{{ $po->user->role ?? 'admin' }} scale-75">
                                    {{ $po->user->role ?? '-' }}
                                </span>
                            </td>
                            <td class="text-end fw-bold text-dark">
                                {{ $po->formatted_total }}
                            </td>
                            <td class="text-center">
                                @if($po->status === \App\Models\Purchase::STATUS_ORDERED)
                                    <span class="badge bg-warning text-dark px-3 py-2 rounded-pill">
                                        <i class="bi bi-clock-history me-1"></i> Menunggu Fisik
                                    </span>
                                @elseif($po->status === \App\Models\Purchase::STATUS_RECEIVED)
                                    <span class="badge bg-success px-3 py-2 rounded-pill">
                                        <i class="bi bi-check-circle me-1"></i> Diterima di Gudang
                                    </span>
                                @elseif($po->status === \App\Models\Purchase::STATUS_CANCELLED)
                                    <span class="badge bg-secondary px-3 py-2 rounded-pill">
                                        <i class="bi bi-x-circle me-1"></i> Dibatalkan
                                    </span>
                                @else
                                    <span class="badge bg-light text-dark border px-3 py-2 rounded-pill">
                                        {{ ucfirst($po->status) }}
                                    </span>
                                @endif
                            </td>
                            <td class="pe-3 text-end">
                                <a href="{{ route('purchases.show', $po) }}" class="btn btn-sm btn-outline-primary px-3">
                                    <i class="bi bi-eye me-1"></i> Detail
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center py-5 text-muted">
                                <div class="py-4">
                                    <i class="bi bi-inbox fs-1 text-secondary opacity-50 mb-3 d-block"></i>
                                    <h6 class="fw-semibold text-dark">Belum ada pesanan pembelian (PO)</h6>
                                    <p class="small text-muted mb-3">Klik tombol "Buat PO Baru" untuk melakukan pemesanan stok kopi ke supplier.</p>
                                    @if(auth()->user()->hasRole(['owner', 'admin', 'warehouse']))
                                        <a href="{{ route('purchases.create') }}" class="btn btn-sm btn-coffee">
                                            <i class="bi bi-plus-circle me-1"></i> Buat PO Sekarang
                                        </a>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($purchases->hasPages())
            <div class="card-footer bg-white border-top py-3 d-flex justify-content-between align-items-center">
                <small class="text-muted">Menampilkan {{ $purchases->firstItem() }} - {{ $purchases->lastItem() }} dari {{ $purchases->total() }} pesanan pembelian</small>
                <div>{{ $purchases->links() }}</div>
            </div>
        @endif
    </div>
</div>
@endsection
