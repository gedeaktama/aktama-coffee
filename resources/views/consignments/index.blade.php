@extends('layouts.app')

@section('title', 'Stok Titipan Toko (Konsinyasi) — Kopi Distro')

@section('content')
<div class="container-fluid py-3">
    <!-- Breadcrumb & Header -->
    <div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-4">
        <div>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-1">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}" class="text-decoration-none">Dashboard</a></li>
                    <li class="breadcrumb-item active">Konsinyasi (Titip Jual)</li>
                </ol>
            </nav>
            <h3 class="fw-bold mb-0 text-dark">
                <i class="bi bi-shop-window text-warning me-2"></i> Stok Titipan Toko (Konsinyasi)
            </h3>
            <p class="text-muted small mb-0">Manajemen kopi yang dititipkan di mitra toko/warung, monitoring sisa rak, dan survei penagihan produk laku.</p>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('consignments.audits.index') }}" class="btn btn-outline-dark">
                <i class="bi bi-journal-check me-1"></i> Riwayat Survei &amp; Setoran
            </a>
            @if(auth()->user()?->hasRole(['owner', 'admin', 'sales', 'warehouse']))
                <a href="{{ route('consignments.create') }}" class="btn btn-warning fw-bold text-dark shadow-sm">
                    <i class="bi bi-plus-lg me-1"></i> Kirim Titipan Baru
                </a>
            @endif
        </div>
    </div>

    <!-- 4 Kartu Statistik Konsinyasi -->
    <div class="row g-3 mb-4">
        <div class="col-sm-6 col-xl-3">
            <div class="card card-custom p-3 border-0 shadow-sm h-100" style="border-left: 4px solid #f59e0b !important;">
                <div class="d-flex justify-content-between align-items-center mb-1">
                    <small class="text-muted fw-semibold">Toko Mitra Aktif Titip</small>
                    <span class="badge bg-warning bg-opacity-15 text-warning rounded-pill p-2"><i class="bi bi-shop fs-6"></i></span>
                </div>
                <h3 class="fw-bold text-dark mb-0">{{ $totalActiveStores }} Toko</h3>
                <small class="text-muted">Memajang produk kopi kita</small>
            </div>
        </div>
        <div class="col-sm-6 col-xl-3">
            <div class="card card-custom p-3 border-0 shadow-sm h-100" style="border-left: 4px solid #3b82f6 !important;">
                <div class="d-flex justify-content-between align-items-center mb-1">
                    <small class="text-muted fw-semibold">Total Pack di Toko</small>
                    <span class="badge bg-primary bg-opacity-10 text-primary rounded-pill p-2"><i class="bi bi-boxes fs-6"></i></span>
                </div>
                <h3 class="fw-bold text-dark mb-0">{{ number_format($totalItemsInStores, 0, ',', '.') }} pack</h3>
                <small class="text-muted">Stok fisik tersebar di luar gudang</small>
            </div>
        </div>
        <div class="col-sm-6 col-xl-3">
            <div class="card card-custom p-3 border-0 shadow-sm h-100" style="border-left: 4px solid #10b981 !important;">
                <div class="d-flex justify-content-between align-items-center mb-1">
                    <small class="text-muted fw-semibold">Nilai Fisik Barang di Toko</small>
                    <span class="badge bg-success bg-opacity-10 text-success rounded-pill p-2"><i class="bi bi-tag fs-6"></i></span>
                </div>
                <h3 class="fw-bold text-dark mb-0">Rp {{ number_format($totalStockValue, 0, ',', '.') }}</h3>
                <small class="text-muted">Potensi omzet di rak mitra</small>
            </div>
        </div>
        <div class="col-sm-6 col-xl-3">
            <div class="card card-custom p-3 border-0 shadow-sm h-100" style="border-left: 4px solid #8b5cf6 !important;">
                <div class="d-flex justify-content-between align-items-center mb-1">
                    <small class="text-muted fw-semibold">Realisasi Laku Terbayar</small>
                    <span class="badge bg-purple bg-opacity-10 text-dark rounded-pill p-2"><i class="bi bi-cash-stack fs-6"></i></span>
                </div>
                <h3 class="fw-bold text-dark mb-0">Rp {{ number_format($totalRevenueFromSold, 0, ',', '.') }}</h3>
                <small class="text-success fw-semibold"><i class="bi bi-check-circle me-1"></i>Hasil survei lunas</small>
            </div>
        </div>
    </div>

    <!-- Filter & Pencarian -->
    <div class="card card-custom p-3 mb-4 shadow-sm">
        <form method="GET" action="{{ route('consignments.index') }}" class="row g-2 align-items-center">
            <div class="col-md-5">
                <div class="input-group">
                    <span class="input-group-text bg-white border-end-0 text-muted"><i class="bi bi-search"></i></span>
                    <input type="text" name="search" class="form-control border-start-0" placeholder="Cari No. Surat Titip (KS-...) atau nama toko..." value="{{ request('search') }}">
                </div>
            </div>
            <div class="col-md-3">
                <select name="customer_id" class="form-select">
                    <option value="">-- Semua Toko Mitra --</option>
                    @foreach($customers as $c)
                        <option value="{{ $c->id }}" {{ request('customer_id') == $c->id ? 'selected' : '' }}>{{ $c->business_name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2">
                <select name="status" class="form-select">
                    <option value="">-- Semua Status --</option>
                    <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>Titipan Aktif</option>
                    <option value="settled" {{ request('status') === 'settled' ? 'selected' : '' }}>Selesai / Lunas</option>
                </select>
            </div>
            <div class="col-md-2 d-flex gap-2">
                <button type="submit" class="btn btn-warning fw-bold text-dark flex-grow-1"><i class="bi bi-filter"></i> Filter</button>
                @if(request()->hasAny(['search', 'customer_id', 'status']))
                    <a href="{{ route('consignments.index') }}" class="btn btn-outline-secondary" title="Reset Filter"><i class="bi bi-x-circle"></i></a>
                @endif
            </div>
        </form>
    </div>

    <!-- Tabel Daftar Penitipan Toko -->
    <div class="card card-custom p-0 overflow-hidden shadow-sm">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th class="ps-4">No. Surat Titip</th>
                        <th>Toko / Mitra Warung</th>
                        <th>Tanggal Titip</th>
                        <th>Sales PIC</th>
                        <th>Rincian Produk Titipan</th>
                        <th class="text-center">Sisa di Rak Toko</th>
                        <th class="text-end">Nilai Titipan</th>
                        <th class="text-center">Status</th>
                        <th class="text-end pe-4">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($consignments as $ks)
                        <tr>
                            <td class="ps-4">
                                <a href="{{ route('consignments.show', $ks) }}" class="fw-bold font-monospace text-decoration-none text-dark">
                                    {{ $ks->consignment_number }}
                                </a>
                            </td>
                            <td>
                                <div class="fw-bold text-dark">{{ $ks->customer->business_name }}</div>
                                <small class="text-muted"><i class="bi bi-person me-1"></i>{{ $ks->customer->pic_name }} &bull; {{ $ks->customer->phone }}</small>
                            </td>
                            <td>
                                <div>{{ $ks->delivery_date->format('d M Y') }}</div>
                                <small class="text-muted">{{ $ks->delivery_date->diffForHumans() }}</small>
                            </td>
                            <td>
                                <span class="badge bg-light text-dark border">{{ $ks->salesUser->name ?? '-' }}</span>
                            </td>
                            <td>
                                <div class="d-flex flex-column gap-1">
                                    @foreach($ks->items as $item)
                                        <div class="small">
                                            <strong class="text-dark">{{ $item->variant->sku }}</strong>: 
                                            {{ $item->current_quantity }} pack 
                                            @if($item->sold_quantity > 0)
                                                <span class="text-success fw-bold">({{ $item->sold_quantity }} laku)</span>
                                            @endif
                                        </div>
                                    @endforeach
                                </div>
                            </td>
                            <td class="text-center">
                                <span class="badge bg-primary fs-6 px-3 py-2">
                                    {{ $ks->total_active_stock }} pack
                                </span>
                            </td>
                            <td class="text-end fw-bold text-dark">
                                {{ $ks->formatted_total_stock_value }}
                            </td>
                            <td class="text-center">
                                <span class="badge {{ $ks->status_badge_class }}">
                                    {{ $ks->status_label }}
                                </span>
                            </td>
                            <td class="text-end pe-4">
                                <div class="btn-group btn-group-sm">
                                    @if($ks->status === 'active')
                                        <a href="{{ route('consignments.audit.create', $ks) }}" class="btn btn-warning fw-bold text-dark" title="Survei & Tagih Laku">
                                            <i class="bi bi-clipboard-check me-1"></i> Survei
                                        </a>
                                    @endif
                                    <a href="{{ route('consignments.show', $ks) }}" class="btn btn-outline-secondary" title="Detail">
                                        <i class="bi bi-eye"></i>
                                    </a>
                                    <a href="{{ route('consignments.print', $ks) }}" target="_blank" class="btn btn-outline-secondary" title="Cetak Surat Titip">
                                        <i class="bi bi-printer"></i>
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="py-5 text-center text-muted">
                                <i class="bi bi-shop fs-1 text-secondary opacity-50 mb-3 d-block"></i>
                                <h6 class="fw-bold text-dark">Belum ada data penitipan konsinyasi</h6>
                                <p class="small text-muted mb-3">Mulai buat surat titip barang baru ke warung, warkop, atau toko kelontong mitra Anda.</p>
                                @if(auth()->user()?->hasRole(['owner', 'admin', 'sales', 'warehouse']))
                                    <a href="{{ route('consignments.create') }}" class="btn btn-warning btn-sm fw-bold text-dark">
                                        <i class="bi bi-plus-lg me-1"></i> Buat Penitipan Pertama
                                    </a>
                                @endif
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    @if($consignments->hasPages())
        <div class="mt-4 d-flex justify-content-center">
            {{ $consignments->links() }}
        </div>
    @endif
</div>
@endsection
