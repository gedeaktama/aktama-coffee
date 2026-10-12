@extends('layouts.app')

@section('title', 'Nota Survei ' . $audit->audit_number . ' — Kopi Distro')

@section('content')
<div class="container py-3">
    <!-- Breadcrumb & Header -->
    <div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-4">
        <div>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-1">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}" class="text-decoration-none">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('consignments.index') }}" class="text-decoration-none">Konsinyasi</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('consignments.audits.index') }}" class="text-decoration-none">Riwayat Survei</a></li>
                    <li class="breadcrumb-item active">{{ $audit->audit_number }}</li>
                </ol>
            </nav>
            <div class="d-flex align-items-center gap-2">
                <h3 class="fw-bold mb-0 text-dark">{{ $audit->audit_number }}</h3>
                <span class="badge {{ $audit->payment_status_badge_class }}">{{ $audit->payment_status_label }}</span>
            </div>
            <p class="text-muted small mb-0">Survei Toko: <strong>{{ $audit->customer->business_name }}</strong> &bull; Tanggal Kunjungan: {{ $audit->audit_date->format('d M Y') }}</p>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('consignments.audits.print', $audit) }}" target="_blank" class="btn btn-warning fw-bold text-dark shadow-sm">
                <i class="bi bi-printer me-1"></i> Cetak Kuitansi Setoran
            </a>
            <a href="{{ route('consignments.show', $audit->consignment) }}" class="btn btn-outline-secondary">
                <i class="bi bi-arrow-left me-1"></i> Kembali ke Toko
            </a>
        </div>
    </div>

    <!-- Ringkasan Hasil Survei -->
    <div class="row g-4 mb-4">
        <div class="col-lg-8">
            <div class="card card-custom p-4 shadow-sm h-100">
                <h5 class="fw-bold text-dark mb-3 pb-2 border-bottom">
                    <i class="bi bi-shop me-2 text-warning"></i> Informasi Toko Mitra &amp; Petugas
                </h5>
                <div class="row g-3">
                    <div class="col-sm-6">
                        <small class="text-muted d-block">Toko Mitra</small>
                        <h6 class="fw-bold text-dark mb-1">{{ $audit->customer->business_name }}</h6>
                        <span class="small text-muted">{{ $audit->customer->shipping_address ?: $audit->customer->address }}</span>
                    </div>
                    <div class="col-sm-6">
                        <small class="text-muted d-block">Penanggung Jawab Toko</small>
                        <strong class="text-dark d-block">{{ $audit->customer->pic_name }}</strong>
                        <span class="small text-muted">{{ $audit->customer->phone }}</span>
                    </div>
                    <div class="col-sm-6 border-top pt-2">
                        <small class="text-muted d-block">Sales Canvasser yang Bertugas</small>
                        <strong class="text-dark">{{ $audit->user->name ?? '-' }}</strong>
                    </div>
                    <div class="col-sm-6 border-top pt-2">
                        <small class="text-muted d-block">Metode Pembayaran</small>
                        <strong class="text-dark text-uppercase"><i class="bi bi-cash-stack text-success me-1"></i>{{ $audit->payment_method }}</strong>
                    </div>
                    @if($audit->order)
                        <div class="col-12 border-top pt-2">
                            <small class="text-muted d-block">Faktur Penjualan Resmi Otomatis:</small>
                            <a href="{{ route('orders.show', $audit->order) }}" class="fw-bold text-primary text-decoration-none">
                                <i class="bi bi-receipt me-1"></i> {{ $audit->order->order_number }} &rarr;
                            </a>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="card card-custom p-4 shadow-sm h-100" style="background: linear-gradient(135deg, #fdfbf7 0%, #fef3c7 100%); border: 2px solid #fde68a;">
                <small class="text-muted fw-bold text-uppercase d-block mb-1">Total Uang yang Disetor Toko</small>
                <h2 class="fw-bold text-dark mb-2">{{ $audit->formatted_total_sold_amount }}</h2>
                
                <div class="pt-2 border-top border-warning">
                    <div class="d-flex justify-content-between small text-muted mb-1">
                        <span>Total Kopi yang Laku:</span>
                        <strong class="text-success fs-6">{{ $audit->total_sold_qty }} pack</strong>
                    </div>
                    <div class="d-flex justify-content-between small text-muted">
                        <span>Restock Tambahan:</span>
                        <strong class="text-info fs-6">+{{ $audit->total_restock_qty }} pack baru</strong>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Tabel Rincian Opname & Perhitungan Produk Laku -->
    <div class="card card-custom p-0 overflow-hidden shadow-sm">
        <div class="p-3 bg-light border-bottom">
            <h5 class="fw-bold text-dark mb-0">
                <i class="bi bi-list-check me-2 text-warning"></i> Rincian Perhitungan Produk Laku &amp; Opname Rak
            </h5>
        </div>

        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th class="ps-4">Produk Kopi &amp; SKU</th>
                        <th class="text-center">Stok Awal Titip</th>
                        <th class="text-center bg-warning bg-opacity-10">Sisa Fisik Rak</th>
                        <th class="text-center">Retur</th>
                        <th class="text-center bg-success bg-opacity-10">Laku Terjual</th>
                        <th class="text-end">Harga Satuan</th>
                        <th class="text-end bg-success bg-opacity-10">Subtotal Bayar</th>
                        <th class="text-center">Restock Baru</th>
                        <th class="text-center pe-4">Stok Akhir di Rak</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($audit->items as $item)
                        <tr>
                            <td class="ps-4">
                                <div class="d-flex align-items-center gap-2">
                                    <img src="{{ $item->variant->product->image_url }}" alt="{{ $item->variant->product->name }}" class="rounded-2 border bg-light p-1" style="width: 40px; height: 40px; object-fit: contain;">
                                    <div>
                                        <div class="fw-bold text-dark">{{ $item->variant->product->name }}</div>
                                        <small class="text-muted"><code class="fw-bold">{{ $item->variant->sku }}</code> ({{ $item->variant->package_weight_grams }}g)</small>
                                    </div>
                                </div>
                            </td>
                            <td class="text-center">
                                <span class="badge bg-light text-dark border">{{ $item->stock_before }} pack</span>
                            </td>
                            <td class="text-center bg-warning bg-opacity-10 fw-bold">
                                {{ $item->physical_remaining }} pack
                            </td>
                            <td class="text-center">
                                <span class="badge bg-secondary bg-opacity-10 text-secondary">{{ $item->quantity_returned }}</span>
                            </td>
                            <td class="text-center bg-success bg-opacity-10">
                                <span class="badge bg-success bg-opacity-15 text-success fs-6 fw-bold px-3 py-1">
                                    {{ $item->quantity_sold }} pack
                                </span>
                            </td>
                            <td class="text-end font-monospace">
                                {{ $item->formatted_price }}
                            </td>
                            <td class="text-end bg-success bg-opacity-10 fw-bold text-dark">
                                {{ $item->formatted_subtotal }}
                            </td>
                            <td class="text-center">
                                @if($item->restock_quantity > 0)
                                    <span class="badge bg-info bg-opacity-10 text-info border border-info border-opacity-25">
                                        +{{ $item->restock_quantity }}
                                    </span>
                                @else
                                    <span class="text-muted">-</span>
                                @endif
                            </td>
                            <td class="text-center pe-4 fw-bold text-primary">
                                {{ $item->stock_after }} pack
                            </td>
                        </tr>
                    @endforeach
                </tbody>
                <tfoot>
                    <tr class="table-light">
                        <th colspan="4" class="text-end">TOTAL LAKU &amp; TAGIHAN:</th>
                        <th class="text-center fw-bold fs-6 text-success">{{ $audit->total_sold_qty }} pack</th>
                        <th></th>
                        <th class="text-end fw-bold fs-6 text-dark">{{ $audit->formatted_total_sold_amount }}</th>
                        <th class="text-center text-info fw-bold">+{{ $audit->total_restock_qty }}</th>
                        <th></th>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>
</div>
@endsection
