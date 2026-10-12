@extends('layouts.app')

@section('title', 'Riwayat Survei & Penyetoran Konsinyasi — Kopi Distro')

@section('content')
<div class="container-fluid py-3">
    <!-- Breadcrumb & Header -->
    <div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-4">
        <div>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-1">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}" class="text-decoration-none">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('consignments.index') }}" class="text-decoration-none">Konsinyasi</a></li>
                    <li class="breadcrumb-item active">Riwayat Survei</li>
                </ol>
            </nav>
            <h3 class="fw-bold mb-0 text-dark">
                <i class="bi bi-journal-check text-warning me-2"></i> Riwayat Survei &amp; Penyetoran Konsinyasi
            </h3>
            <p class="text-muted small mb-0">Catatan kunjungan mingguan sales canvasser, opname sisa rak toko mitra, dan rekapitulasi uang yang berhasil disetor dari produk laku.</p>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('consignments.index') }}" class="btn btn-outline-dark">
                <i class="bi bi-shop-window me-1"></i> Kembali ke Stok Toko
            </a>
        </div>
    </div>

    <!-- Filter & Pencarian -->
    <div class="card card-custom p-3 mb-4 shadow-sm">
        <form method="GET" action="{{ route('consignments.audits.index') }}" class="row g-2 align-items-center">
            <div class="col-md-6">
                <div class="input-group">
                    <span class="input-group-text bg-white border-end-0 text-muted"><i class="bi bi-search"></i></span>
                    <input type="text" name="search" class="form-control border-start-0" placeholder="Cari No. Bukti Survei (SRV-...) atau nama toko..." value="{{ request('search') }}">
                </div>
            </div>
            <div class="col-md-4">
                <select name="customer_id" class="form-select">
                    <option value="">-- Semua Toko Mitra --</option>
                    @foreach($customers as $c)
                        <option value="{{ $c->id }}" {{ request('customer_id') == $c->id ? 'selected' : '' }}>{{ $c->business_name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2 d-flex gap-2">
                <button type="submit" class="btn btn-warning fw-bold text-dark flex-grow-1"><i class="bi bi-filter"></i> Filter</button>
                @if(request()->hasAny(['search', 'customer_id']))
                    <a href="{{ route('consignments.audits.index') }}" class="btn btn-outline-secondary" title="Reset"><i class="bi bi-x-circle"></i></a>
                @endif
            </div>
        </form>
    </div>

    <!-- Tabel Daftar Survei -->
    <div class="card card-custom p-0 overflow-hidden shadow-sm">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th class="ps-4">No. Bukti Survei</th>
                        <th>Toko Mitra</th>
                        <th>Tanggal Kunjungan</th>
                        <th>Petugas Sales</th>
                        <th class="text-center">Kopi yang Laku</th>
                        <th class="text-center">Restock Baru</th>
                        <th class="text-end">Total Uang Laku</th>
                        <th class="text-center">Metode Bayar</th>
                        <th class="text-center">Status</th>
                        <th class="text-end pe-4">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($audits as $audit)
                        <tr>
                            <td class="ps-4">
                                <a href="{{ route('consignments.audits.show', $audit) }}" class="fw-bold font-monospace text-decoration-none text-dark">
                                    {{ $audit->audit_number }}
                                </a>
                                <small class="text-muted d-block">Titip: {{ $audit->consignment->consignment_number ?? '-' }}</small>
                            </td>
                            <td>
                                <div class="fw-bold text-dark">{{ $audit->customer->business_name }}</div>
                                <small class="text-muted">{{ $audit->customer->pic_name }} &bull; {{ $audit->customer->phone }}</small>
                            </td>
                            <td>
                                <div>{{ $audit->audit_date->format('d M Y') }}</div>
                                <small class="text-muted">{{ $audit->audit_date->diffForHumans() }}</small>
                            </td>
                            <td>
                                <span class="badge bg-light text-dark border">{{ $audit->user->name ?? '-' }}</span>
                            </td>
                            <td class="text-center">
                                <span class="badge bg-success bg-opacity-15 text-success fs-6 px-3 py-1 fw-bold">
                                    {{ $audit->total_sold_qty }} pack
                                </span>
                            </td>
                            <td class="text-center">
                                @if($audit->total_restock_qty > 0)
                                    <span class="badge bg-info bg-opacity-10 text-info border border-info border-opacity-25">
                                        +{{ $audit->total_restock_qty }} pack
                                    </span>
                                @else
                                    <span class="text-muted small">-</span>
                                @endif
                            </td>
                            <td class="text-end fw-bold text-dark fs-6">
                                {{ $audit->formatted_total_sold_amount }}
                            </td>
                            <td class="text-center">
                                <span class="badge bg-light text-dark border text-uppercase">
                                    {{ $audit->payment_method }}
                                </span>
                            </td>
                            <td class="text-center">
                                <span class="badge {{ $audit->payment_status_badge_class }}">
                                    {{ $audit->payment_status_label }}
                                </span>
                            </td>
                            <td class="text-end pe-4">
                                <div class="btn-group btn-group-sm">
                                    <a href="{{ route('consignments.audits.show', $audit) }}" class="btn btn-outline-secondary" title="Lihat Nota">
                                        <i class="bi bi-eye"></i>
                                    </a>
                                    <a href="{{ route('consignments.audits.print', $audit) }}" target="_blank" class="btn btn-outline-secondary" title="Cetak Kuitansi">
                                        <i class="bi bi-printer"></i>
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="10" class="py-5 text-center text-muted">
                                <i class="bi bi-clipboard-x fs-1 text-secondary opacity-50 mb-3 d-block"></i>
                                <h6 class="fw-bold text-dark">Belum ada riwayat survei mingguan</h6>
                                <p class="small text-muted mb-0">Lakukan survei mingguan pada toko mitra yang aktif dititipkan kopi.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    @if($audits->hasPages())
        <div class="mt-4 d-flex justify-content-center">
            {{ $audits->links() }}
        </div>
    @endif
</div>
@endsection
