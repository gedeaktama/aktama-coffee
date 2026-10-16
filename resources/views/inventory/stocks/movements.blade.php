@extends('layouts.app')

@section('title', 'Kartu Mutasi Stok Persediaan - Kopi Distro')

@section('content')
<div class="container py-3">
    <!-- Header Page -->
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-4">
        <div>
            <div class="d-flex align-items-center gap-2">
                <span class="badge bg-secondary bg-opacity-10 text-secondary px-3 py-2 rounded-pill fw-semibold">
                    <i class="bi bi-journal-text me-1"></i> Audit Trail & Kartu Stok
                </span>
                <span class="text-muted">&bull;</span>
                <span class="small text-muted">Tahap 3: Persediaan & Pembelian</span>
            </div>
            <h2 class="fw-bold text-dark mt-2 mb-1">Kartu Stok & Riwayat Mutasi</h2>
            <p class="text-muted mb-0">Catatan kronologis setiap pergerakan stok masuk, keluar, dan penyesuaian opname untuk transparansi audit.</p>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('inventory.index') }}" class="btn btn-outline-coffee d-inline-flex align-items-center gap-2">
                <i class="bi bi-boxes"></i>
                <span>Monitoring Stok</span>
            </a>
            @if(auth()->user()->hasRole(['owner', 'admin', 'warehouse']))
                <a href="{{ route('purchases.create') }}" class="btn btn-coffee d-inline-flex align-items-center gap-2 shadow-sm">
                    <i class="bi bi-cart-plus"></i>
                    <span>Buat PO Baru</span>
                </a>
            @endif
        </div>
    </div>

    <!-- Filter Form -->
    <div class="card card-custom p-3 mb-4">
        <form method="GET" action="{{ route('inventory.movements') }}" class="row g-2 align-items-center">
            <div class="col-md-5">
                <select name="variant_id" class="form-select form-select-searchable" data-placeholder="-- Cari Varian Kopi / SKU --">
                    <option value="">-- Semua Varian Kopi / SKU --</option>
                    @foreach($variants as $v)
                        <option value="{{ $v->id }}" data-sku="{{ $v->sku }}" {{ request('variant_id') == $v->id ? 'selected' : '' }}>
                            {{ $v->sku }} &mdash; {{ $v->product->name }} ({{ $v->variant_name }}, {{ $v->package_weight_grams }}g)
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-4">
                <select name="type" class="form-select">
                    <option value="">-- Semua Jenis Mutasi --</option>
                    <option value="in" {{ request('type') === 'in' ? 'selected' : '' }}>Masuk (+) Penerimaan Supplier</option>
                    <option value="out" {{ request('type') === 'out' ? 'selected' : '' }}>Keluar (-) Penjualan Pesanan</option>
                    <option value="adjustment" {{ request('type') === 'adjustment' ? 'selected' : '' }}>Penyesuaian Fisik (Opname)</option>
                </select>
            </div>
            <div class="col-md-3 d-flex gap-2">
                <button type="submit" class="btn btn-outline-coffee flex-grow-1">
                    <i class="bi bi-funnel me-1"></i> Filter
                </button>
                @if(request()->hasAny(['variant_id', 'type']))
                    <a href="{{ route('inventory.movements') }}" class="btn btn-outline-secondary" title="Reset Filter">
                        <i class="bi bi-x-circle"></i>
                    </a>
                @endif
            </div>
        </form>
    </div>

    <!-- Movements Table -->
    <div class="card card-custom overflow-hidden">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th class="ps-3 py-3 text-secondary text-uppercase small fw-bold">Waktu Mutasi</th>
                        <th class="py-3 text-secondary text-uppercase small fw-bold">SKU & Produk</th>
                        <th class="py-3 text-secondary text-uppercase small fw-bold text-center">Tipe</th>
                        <th class="py-3 text-secondary text-uppercase small fw-bold text-center">Jumlah</th>
                        <th class="py-3 text-secondary text-uppercase small fw-bold text-center">Saldo Awal</th>
                        <th class="py-3 text-secondary text-uppercase small fw-bold text-center">Saldo Akhir</th>
                        <th class="py-3 text-secondary text-uppercase small fw-bold">Petugas</th>
                        <th class="pe-3 py-3 text-secondary text-uppercase small fw-bold">Keterangan / Audit Note</th>
                    </tr>
                </thead>
                <tbody class="border-top-0">
                    @forelse($movements as $m)
                        <tr>
                            <td class="ps-3 text-nowrap small text-muted">
                                <i class="bi bi-clock me-1"></i>{{ $m->created_at->translatedFormat('d/m/Y H:i') }}
                            </td>
                            <td>
                                <div class="font-monospace fw-bold text-dark small">{{ $m->variant->sku }}</div>
                                <div class="small fw-semibold text-coffee">{{ $m->variant->product->name }} &mdash; {{ $m->variant->variant_name }}</div>
                            </td>
                            <td class="text-center">
                                <span class="badge {{ $m->type_badge_class }} px-2 py-1 rounded-pill small">
                                    {{ $m->type_label }}
                                </span>
                            </td>
                            <td class="text-center fw-bold">
                                @if($m->quantity > 0)
                                    <span class="text-success">+{{ $m->quantity }}</span>
                                @elseif($m->quantity < 0)
                                    <span class="text-danger">{{ $m->quantity }}</span>
                                @else
                                    <span class="text-secondary">0</span>
                                @endif
                                <small class="text-muted fw-normal">pack</small>
                            </td>
                            <td class="text-center text-muted small">
                                {{ $m->stock_before }} pack
                            </td>
                            <td class="text-center fw-bold text-dark">
                                {{ $m->stock_after }} pack
                            </td>
                            <td>
                                <div class="small fw-semibold text-dark">{{ $m->user->name ?? 'Sistem' }}</div>
                                <span class="badge badge-role badge-role-{{ $m->user->role ?? 'admin' }} scale-75">
                                    {{ $m->user->role ?? '-' }}
                                </span>
                            </td>
                            <td class="pe-3 small text-secondary">
                                {{ $m->notes ?? '-' }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="text-center py-5 text-muted">
                                <i class="bi bi-journal-x fs-1 text-secondary opacity-50 mb-3 d-block"></i>
                                <h6 class="fw-semibold text-dark">Belum ada catatan mutasi stok</h6>
                                <p class="small text-muted mb-0">Mutasi akan tercatat otomatis saat barang PO diterima atau terjadi transaksi penjualan.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($movements->hasPages())
            <div class="card-footer bg-white border-top py-3 d-flex justify-content-between align-items-center">
                <small class="text-muted">Menampilkan {{ $movements->firstItem() }} - {{ $movements->lastItem() }} dari {{ $movements->total() }} catatan mutasi</small>
                <div>{{ $movements->links() }}</div>
            </div>
        @endif
    </div>
</div>
@endsection
