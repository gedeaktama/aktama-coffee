@extends('layouts.app')

@section('title', 'Laporan Penjualan & Omset - Kopi Distro')

@section('content')
<div class="container py-3">
    <!-- Header Page -->
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-4">
        <div>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-1">
                    <li class="breadcrumb-item"><a href="{{ route('reports.index') }}" class="text-decoration-none">Laporan</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Penjualan</li>
                </ol>
            </nav>
            <h2 class="fw-bold text-dark mb-1">Laporan Penjualan & Omset</h2>
            <p class="text-muted mb-0">Periode: <strong>{{ \Carbon\Carbon::parse($startDate)->translatedFormat('d F Y') }}</strong> s/d <strong>{{ \Carbon\Carbon::parse($endDate)->translatedFormat('d F Y') }}</strong></p>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('reports.sales', array_merge(request()->all(), ['export' => 'csv'])) }}" class="btn btn-outline-success d-inline-flex align-items-center gap-2">
                <i class="bi bi-file-earmark-spreadsheet"></i>
                <span>Download Spreadsheet (CSV)</span>
            </a>
            <a href="{{ route('reports.index') }}" class="btn btn-outline-coffee">
                <i class="bi bi-arrow-left me-1"></i> Kembali
            </a>
        </div>
    </div>

    <!-- Filter Toolbar -->
    <div class="card card-custom p-3 mb-4">
        <form method="GET" action="{{ route('reports.sales') }}" class="row g-2 align-items-end">
            <div class="col-md-3">
                <label for="start_date" class="form-label small fw-semibold text-muted mb-1">Dari Tanggal</label>
                <input type="date" name="start_date" id="start_date" class="form-control form-control-sm" value="{{ $startDate }}">
            </div>
            <div class="col-md-3">
                <label for="end_date" class="form-label small fw-semibold text-muted mb-1">Sampai Tanggal</label>
                <input type="date" name="end_date" id="end_date" class="form-control form-control-sm" value="{{ $endDate }}">
            </div>
            <div class="col-md-3">
                <label for="channel" class="form-label small fw-semibold text-muted mb-1">Kanal Penjualan</label>
                <select name="channel" id="channel" class="form-select form-select-sm">
                    <option value="">Semua Saluran (Grosir & Retail)</option>
                    <option value="b2b" {{ $channel === 'b2b' ? 'selected' : '' }}>Grosir B2B (Kafe, Toko, Reseller)</option>
                    <option value="retail" {{ $channel === 'retail' ? 'selected' : '' }}>Retail Online (B2C)</option>
                </select>
            </div>
            <div class="col-md-3 d-flex gap-2">
                <button type="submit" class="btn btn-coffee btn-sm flex-grow-1">
                    <i class="bi bi-filter me-1"></i> Filter
                </button>
                <a href="{{ route('reports.sales') }}" class="btn btn-outline-secondary btn-sm">Reset</a>
            </div>
        </form>
    </div>

    <!-- Summary KPIs -->
    <div class="row g-3 mb-4">
        <div class="col-sm-6 col-lg-3">
            <div class="card card-custom p-3 border-start border-primary border-4">
                <span class="text-muted small fw-semibold text-uppercase">Total Transaksi</span>
                <h4 class="fw-bold mb-0 text-dark">{{ $data['total_orders'] }} Order</h4>
                <small class="text-muted mt-2 d-block border-top pt-2">Kotor: Rp {{ number_format($data['total_gross'], 0, ',', '.') }}</small>
            </div>
        </div>

        <div class="col-sm-6 col-lg-3">
            <div class="card card-custom p-3 border-start border-success border-4">
                <span class="text-muted small fw-semibold text-uppercase">Omset Bersih (Net Sales)</span>
                <h4 class="fw-bold mb-0 text-success">Rp {{ number_format($data['total_net'], 0, ',', '.') }}</h4>
                <small class="text-muted mt-2 d-block border-top pt-2">Setelah potongan diskon</small>
            </div>
        </div>

        <div class="col-sm-6 col-lg-3">
            <div class="card card-custom p-3 border-start border-info border-4">
                <span class="text-muted small fw-semibold text-uppercase">Kas Diterima (Paid)</span>
                <h4 class="fw-bold mb-0 text-info">Rp {{ number_format($data['total_paid'], 0, ',', '.') }}</h4>
                <small class="text-muted mt-2 d-block border-top pt-2">Sudah terbayar lunas/cicil</small>
            </div>
        </div>

        <div class="col-sm-6 col-lg-3">
            <div class="card card-custom p-3 border-start border-warning border-4">
                <span class="text-muted small fw-semibold text-uppercase">Piutang Berjalan (Unpaid)</span>
                <h4 class="fw-bold mb-0 text-warning">Rp {{ number_format($data['total_unpaid'], 0, ',', '.') }}</h4>
                <small class="text-muted mt-2 d-block border-top pt-2">Tempo yang belum lunas</small>
            </div>
        </div>
    </div>

    <!-- Section 1: Breakdown Volume Penjualan per Varian SKU -->
    <div class="card card-custom p-4 mb-4">
        <h5 class="fw-bold text-dark mb-3 pb-2 border-bottom">
            <i class="bi bi-box-seam text-primary me-2"></i> Rekapitulasi Penjualan per Varian SKU Kopi
        </h5>
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th class="ps-3 py-2 small fw-bold text-secondary text-uppercase">Produk Kopi & Varian</th>
                        <th class="py-2 small fw-bold text-secondary text-uppercase">SKU</th>
                        <th class="py-2 small fw-bold text-secondary text-uppercase text-center">Volume Terjual</th>
                        <th class="pe-3 py-2 small fw-bold text-secondary text-uppercase text-end">Total Omset (Rp)</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($data['sku_summary'] as $sku)
                        <tr>
                            <td class="ps-3">
                                <strong class="text-dark">{{ $sku->variant->product->name ?? 'Produk' }}</strong>
                                <small class="text-muted d-block">{{ $sku->variant->variant_name ?? '' }} ({{ $sku->variant->package_weight_grams ?? 0 }}g)</small>
                            </td>
                            <td class="font-monospace small text-secondary">
                                {{ $sku->variant->sku ?? '-' }}
                            </td>
                            <td class="text-center font-monospace fw-bold text-dark">
                                {{ $sku->total_qty }} pack
                            </td>
                            <td class="pe-3 text-end font-monospace fw-bold text-success">
                                Rp {{ number_format($sku->total_revenue, 0, ',', '.') }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="text-center py-3 text-muted">Tidak ada data transaksi pada periode ini.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Section 2: Daftar Rincian Order Transaksi -->
    <div class="card card-custom overflow-hidden border-0 shadow-sm">
        <div class="card-header bg-white py-3 border-bottom d-flex justify-content-between align-items-center">
            <h5 class="fw-bold text-dark mb-0">
                <i class="bi bi-receipt-cutoff text-secondary me-2"></i> Rincian Transaksi Pesanan
            </h5>
            <span class="badge bg-light text-secondary border">{{ $data['total_orders'] }} Transaksi</span>
        </div>
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th class="ps-3 py-3 text-secondary text-uppercase small fw-bold">No. Order</th>
                        <th class="py-3 text-secondary text-uppercase small fw-bold">Tanggal</th>
                        <th class="py-3 text-secondary text-uppercase small fw-bold">Kanal</th>
                        <th class="py-3 text-secondary text-uppercase small fw-bold">Pelanggan / Mitra</th>
                        <th class="py-3 text-secondary text-uppercase small fw-bold text-end">Subtotal</th>
                        <th class="py-3 text-secondary text-uppercase small fw-bold text-end">Diskon</th>
                        <th class="py-3 text-secondary text-uppercase small fw-bold text-end">Total Bersih</th>
                        <th class="py-3 text-secondary text-uppercase small fw-bold text-center">Status Bayar</th>
                        <th class="pe-3 py-3 text-secondary text-uppercase small fw-bold text-end">Aksi</th>
                    </tr>
                </thead>
                <tbody class="border-top-0">
                    @forelse($data['orders'] as $ord)
                        <tr>
                            <td class="ps-3 font-monospace fw-bold">
                                <a href="{{ route('orders.show', $ord) }}" class="text-decoration-none text-dark">
                                    {{ $ord->order_number }}
                                </a>
                            </td>
                            <td class="text-muted small">
                                {{ $ord->order_date->format('d/m/Y') }}
                            </td>
                            <td>
                                @if($ord->isB2B())
                                    <span class="badge bg-primary bg-opacity-10 text-primary border small">B2B Grosir</span>
                                @else
                                    <span class="badge bg-info bg-opacity-10 text-info border small">Retail</span>
                                @endif
                            </td>
                            <td>
                                <div class="fw-bold text-dark">{{ $ord->customer->business_name ?? $ord->recipient_name }}</div>
                            </td>
                            <td class="text-end fw-semibold text-secondary">
                                Rp {{ number_format($ord->subtotal, 0, ',', '.') }}
                            </td>
                            <td class="text-end text-muted">
                                {{ $ord->discount > 0 ? 'Rp ' . number_format($ord->discount, 0, ',', '.') : '-' }}
                            </td>
                            <td class="text-end fw-bold text-dark font-monospace">
                                {{ $ord->formatted_total }}
                            </td>
                            <td class="text-center">
                                <span class="badge {{ $ord->payment_status_badge_class }} px-2 py-1 rounded-pill small">
                                    {{ $ord->payment_status_label }}
                                </span>
                            </td>
                            <td class="pe-3 text-end">
                                <a href="{{ route('orders.show', $ord) }}" class="btn btn-sm btn-outline-coffee px-2">
                                    <i class="bi bi-eye"></i>
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="text-center py-5 text-muted">
                                Tidak ada transaksi pada filter periode yang dipilih.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
