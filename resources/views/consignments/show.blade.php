@extends('layouts.app')

@section('title', 'Detail Titipan ' . $consignment->consignment_number . ' — Kopi Distro')

@section('content')
<div class="container-fluid py-3">
    <!-- Breadcrumb & Header -->
    <div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-4">
        <div>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-1">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}" class="text-decoration-none">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('consignments.index') }}" class="text-decoration-none">Konsinyasi</a></li>
                    <li class="breadcrumb-item active">{{ $consignment->consignment_number }}</li>
                </ol>
            </nav>
            <div class="d-flex align-items-center gap-2">
                <h3 class="fw-bold mb-0 text-dark">{{ $consignment->customer->business_name }}</h3>
                <span class="badge {{ $consignment->status_badge_class }}">{{ $consignment->status_label }}</span>
            </div>
            <p class="text-muted small mb-0">No. Surat Titip: <code class="fw-bold">{{ $consignment->consignment_number }}</code> &bull; Tanggal Mulai Titip: {{ $consignment->delivery_date->format('d M Y') }}</p>
        </div>
        <div class="d-flex gap-2">
            @if($consignment->status === 'active')
                <a href="{{ route('consignments.audit.create', $consignment) }}" class="btn btn-warning btn-lg fw-bold text-dark shadow-sm px-4">
                    <i class="bi bi-clipboard2-check me-1"></i> Lakukan Survei &amp; Tagih Laku
                </a>
            @endif
            <a href="{{ route('consignments.print', $consignment) }}" target="_blank" class="btn btn-outline-dark">
                <i class="bi bi-printer me-1"></i> Cetak Surat Titip
            </a>
            <a href="{{ route('consignments.index') }}" class="btn btn-outline-secondary">
                <i class="bi bi-arrow-left me-1"></i> Kembali
            </a>
        </div>
    </div>

    <!-- Informasi Toko & Nilai Titipan -->
    <div class="row g-4 mb-4">
        <div class="col-lg-8">
            <div class="card card-custom p-4 shadow-sm h-100">
                <div class="row g-3">
                    <div class="col-sm-6">
                        <small class="text-muted d-block">Nama Toko / Mitra Warung</small>
                        <h5 class="fw-bold text-dark mb-1">{{ $consignment->customer->business_name }}</h5>
                        <p class="small text-secondary mb-0"><i class="bi bi-geo-alt me-1"></i>{{ $consignment->customer->shipping_address ?: $consignment->customer->address }}</p>
                    </div>
                    <div class="col-sm-6">
                        <small class="text-muted d-block">Kontak / Penanggung Jawab Toko</small>
                        <strong class="text-dark d-block">{{ $consignment->customer->pic_name }}</strong>
                        <span class="small text-muted"><i class="bi bi-telephone me-1"></i>{{ $consignment->customer->phone }}</span>
                    </div>
                    <div class="col-sm-6 border-top pt-2">
                        <small class="text-muted d-block">Sales Penanggung Jawab</small>
                        <strong class="text-dark">{{ $consignment->salesUser->name ?? '-' }}</strong>
                    </div>
                    <div class="col-sm-6 border-top pt-2">
                        <small class="text-muted d-block">Catatan Posisi Rak</small>
                        <span class="text-dark">{{ $consignment->notes ?: '-' }}</span>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="card card-custom p-4 shadow-sm h-100 bg-light border">
                <small class="text-muted fw-bold text-uppercase d-block mb-1">Total Sisa Stok di Rak Toko</small>
                <h2 class="fw-bold text-primary mb-2">{{ $consignment->total_active_stock }} pack</h2>
                
                <div class="pt-2 border-top">
                    <div class="d-flex justify-content-between small text-muted mb-1">
                        <span>Nilai Barang di Rak:</span>
                        <strong class="text-dark">{{ $consignment->formatted_total_stock_value }}</strong>
                    </div>
                    <div class="d-flex justify-content-between small text-muted">
                        <span>Total Akumulasi Laku:</span>
                        <strong class="text-success">{{ $consignment->total_sold_quantity }} pack terjual</strong>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Tabel Posisi Barang di Toko Saat Ini -->
    <div class="card card-custom p-0 overflow-hidden shadow-sm mb-4">
        <div class="p-3 border-bottom bg-light d-flex justify-content-between align-items-center">
            <h5 class="fw-bold text-dark mb-0">
                <i class="bi bi-boxes text-warning me-2"></i> Posisi Kopi di Rak Toko Saat Ini
            </h5>
            <span class="small text-muted"><i class="bi bi-info-circle me-1"></i> Data diperbarui otomatis setiap kali selesai survei mingguan</span>
        </div>

        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th class="ps-4">Produk Kopi &amp; Varian SKU</th>
                        <th class="text-center">Awal Titip</th>
                        <th class="text-center">Sisa Aktif di Rak</th>
                        <th class="text-center">Total Laku</th>
                        <th class="text-center">Retur</th>
                        <th class="text-end">Harga Satuan</th>
                        <th class="text-end pe-4">Subtotal Fisik di Rak</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($consignment->items as $item)
                        <tr>
                            <td class="ps-4">
                                <div class="d-flex align-items-center gap-3">
                                    <img src="{{ $item->variant->product->image_url }}" alt="{{ $item->variant->product->name }}" class="rounded-2 border bg-light p-1" style="width: 44px; height: 44px; object-fit: contain;">
                                    <div>
                                        <div class="fw-bold text-dark">{{ $item->variant->product->name }}</div>
                                        <small class="text-muted"><code class="fw-bold">{{ $item->variant->sku }}</code> &bull; {{ $item->variant->variant_name }} ({{ $item->variant->package_weight_grams }}g)</small>
                                    </div>
                                </div>
                            </td>
                            <td class="text-center">
                                <span class="badge bg-light text-dark border">{{ $item->initial_quantity }} pack</span>
                            </td>
                            <td class="text-center">
                                <span class="badge bg-primary fs-6 px-3 py-2">
                                    {{ $item->current_quantity }} pack
                                </span>
                            </td>
                            <td class="text-center">
                                <span class="badge bg-success bg-opacity-15 text-success fw-bold px-2 py-1">
                                    {{ $item->sold_quantity }} pack
                                </span>
                            </td>
                            <td class="text-center">
                                <span class="badge bg-secondary bg-opacity-10 text-secondary">
                                    {{ $item->returned_quantity }} pack
                                </span>
                            </td>
                            <td class="text-end text-dark">
                                {{ $item->formatted_price }}
                            </td>
                            <td class="text-end pe-4 fw-bold text-dark">
                                {{ $item->formatted_current_value }}
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    <!-- Riwayat Kunjungan Survei & Penyetoran Uang -->
    <div class="card card-custom p-0 overflow-hidden shadow-sm">
        <div class="p-3 border-bottom bg-light d-flex justify-content-between align-items-center">
            <h5 class="fw-bold text-dark mb-0">
                <i class="bi bi-clock-history text-warning me-2"></i> Riwayat Survei Mingguan &amp; Uang yang Masuk
            </h5>
            @if($consignment->status === 'active')
                <a href="{{ route('consignments.audit.create', $consignment) }}" class="btn btn-sm btn-warning fw-bold text-dark">
                    <i class="bi bi-plus-lg me-1"></i> Input Survei Baru
                </a>
            @endif
        </div>

        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th class="ps-4">No. Bukti Survei</th>
                        <th>Tanggal Survei</th>
                        <th>Petugas Sales</th>
                        <th class="text-center">Kopi yang Laku</th>
                        <th class="text-center">Restock Tambahan</th>
                        <th class="text-end">Total Uang Laku</th>
                        <th class="text-center">Status Bayar</th>
                        <th class="text-end pe-4">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($consignment->audits as $audit)
                        <tr>
                            <td class="ps-4">
                                <a href="{{ route('consignments.audits.show', $audit) }}" class="fw-bold font-monospace text-decoration-none text-dark">
                                    {{ $audit->audit_number }}
                                </a>
                            </td>
                            <td>
                                {{ $audit->audit_date->format('d M Y') }}
                            </td>
                            <td>
                                {{ $audit->user->name ?? '-' }}
                            </td>
                            <td class="text-center">
                                <span class="badge bg-success bg-opacity-15 text-success fs-6 px-3 py-1 fw-bold">
                                    {{ $audit->total_sold_qty }} pack
                                </span>
                            </td>
                            <td class="text-center">
                                @if($audit->total_restock_qty > 0)
                                    <span class="badge bg-info bg-opacity-10 text-info border border-info border-opacity-25">
                                        +{{ $audit->total_restock_qty }} pack baru
                                    </span>
                                @else
                                    <span class="text-muted small">-</span>
                                @endif
                            </td>
                            <td class="text-end fw-bold text-dark">
                                {{ $audit->formatted_total_sold_amount }}
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
                                    <a href="{{ route('consignments.audits.print', $audit) }}" target="_blank" class="btn btn-outline-secondary" title="Cetak Nota">
                                        <i class="bi bi-printer"></i>
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="py-4 text-center text-muted">
                                <i class="bi bi-calendar-x fs-2 text-secondary opacity-50 mb-2 d-block"></i>
                                Belum ada kunjungan survei mingguan untuk toko ini.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
