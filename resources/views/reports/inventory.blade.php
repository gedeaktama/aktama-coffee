@extends('layouts.app')

@section('title', 'Laporan Valuasi Nilai Persediaan Stok - Kopi Distro')

@section('content')
<div class="container py-3">
    <!-- Header Page -->
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-4">
        <div>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-1">
                    <li class="breadcrumb-item"><a href="{{ route('reports.index') }}" class="text-decoration-none">Laporan</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Persediaan & Aset</li>
                </ol>
            </nav>
            <div class="d-flex align-items-center gap-2">
                <h2 class="fw-bold text-dark mb-0">Valuasi Persediaan & Nilai Aset Stok Gudang</h2>
                <span class="badge bg-warning bg-opacity-10 text-dark border border-warning border-opacity-25 px-2 py-1">
                    Inventory Assets
                </span>
            </div>
            <p class="text-muted small mb-0">Posisi saldo stok fisik real-time, nilai modal perolehan (HPP), dan estimasi valuasi ritel.</p>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('reports.inventory', ['export' => 'csv']) }}" class="btn btn-outline-success d-inline-flex align-items-center gap-2">
                <i class="bi bi-file-earmark-spreadsheet"></i>
                <span>Download Valuasi (CSV)</span>
            </a>
            <a href="{{ route('inventory.index') }}" class="btn btn-coffee">
                <i class="bi bi-box-seam me-1"></i> Monitoring Gudang
            </a>
        </div>
    </div>

    <!-- Inventory Valuation Metrics -->
    <div class="row g-3 mb-4">
        <div class="col-sm-6 col-lg-3">
            <div class="card card-custom p-3 border-start border-primary border-4">
                <span class="text-muted small fw-semibold text-uppercase">Total Saldo Stok Fisik</span>
                <h4 class="fw-bold mb-0 text-dark">{{ number_format($data['total_units'], 0, ',', '.') }} Pack</h4>
                <small class="text-muted mt-2 d-block border-top pt-2">Tersebar di {{ $data['total_skus'] }} varian SKU kopi</small>
            </div>
        </div>

        <div class="col-sm-6 col-lg-3">
            <div class="card card-custom p-3 border-start border-warning border-4">
                <span class="text-muted small fw-semibold text-uppercase">Nilai Aset Modal (HPP)</span>
                <h3 class="fw-bold mb-0 text-dark">Rp {{ number_format($data['total_asset_value'], 0, ',', '.') }}</h3>
                <small class="text-muted mt-2 d-block border-top pt-2">Modal tertahan dalam persediaan</small>
            </div>
        </div>

        <div class="col-sm-6 col-lg-3">
            <div class="card card-custom p-3 border-start border-success border-4">
                <span class="text-muted small fw-semibold text-uppercase">Estimasi Nilai Jual Ritel</span>
                <h3 class="fw-bold mb-0 text-success">Rp {{ number_format($data['total_retail_value'], 0, ',', '.') }}</h3>
                <small class="text-muted mt-2 d-block border-top pt-2">Proyeksi saat seluruh stok terjual</small>
            </div>
        </div>

        <div class="col-sm-6 col-lg-3">
            <div class="card card-custom p-3 border-start {{ $data['low_stock_count'] > 0 ? 'border-danger' : 'border-success' }} border-4">
                <span class="text-muted small fw-semibold text-uppercase">Status Restok Minimum</span>
                <h4 class="fw-bold mb-0 {{ $data['low_stock_count'] > 0 ? 'text-danger' : 'text-success' }}">
                    {{ $data['low_stock_count'] }} SKU Menipis
                </h4>
                <small class="text-muted mt-2 d-block border-top pt-2">
                    {{ $data['low_stock_count'] > 0 ? 'Perlu dibuatkan Purchase Order (PO)' : 'Persediaan stok mencukupi aman' }}
                </small>
            </div>
        </div>
    </div>

    <!-- Table of Inventory Assets -->
    <div class="card card-custom overflow-hidden border-0 shadow-sm">
        <div class="card-header bg-white py-3 border-bottom d-flex justify-content-between align-items-center">
            <h5 class="fw-bold text-dark mb-0">
                <i class="bi bi-table text-primary me-2"></i> Rincian Nilai Persediaan per SKU Kopi
            </h5>
            <span class="badge bg-light text-secondary border">{{ $data['variants']->count() }} SKU</span>
        </div>
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th class="ps-3 py-3 text-secondary text-uppercase small fw-bold">Varian Produk Kopi</th>
                        <th class="py-3 text-secondary text-uppercase small fw-bold">SKU</th>
                        <th class="py-3 text-secondary text-uppercase small fw-bold text-center">Sisa Stok</th>
                        <th class="py-3 text-secondary text-uppercase small fw-bold text-end">HPP / Beli</th>
                        <th class="py-3 text-secondary text-uppercase small fw-bold text-end">Harga Jual</th>
                        <th class="py-3 text-secondary text-uppercase small fw-bold text-end">Nilai Modal Aset</th>
                        <th class="pe-3 py-3 text-secondary text-uppercase small fw-bold text-end">Nilai Jual Ritel</th>
                    </tr>
                </thead>
                <tbody class="border-top-0">
                    @forelse($data['variants'] as $item)
                        @php
                            $v = $item['variant'];
                        @endphp
                        <tr>
                            <td class="ps-3">
                                <strong class="text-dark">{{ $v->product->name ?? 'Produk' }}</strong>
                                <small class="text-muted d-block">{{ $v->variant_name }} ({{ $v->package_weight_grams }}g &middot; {{ ucfirst($v->grind_form) }})</small>
                            </td>
                            <td class="font-monospace small text-secondary">
                                {{ $v->sku }}
                            </td>
                            <td class="text-center font-monospace">
                                <span class="fw-bold {{ $item['is_low_stock'] ? 'text-danger' : 'text-dark' }}">
                                    {{ $item['stock'] }} pack
                                </span>
                                @if($item['is_low_stock'])
                                    <span class="badge bg-danger d-block mt-1 small">Low Stock</span>
                                @endif
                            </td>
                            <td class="text-end font-monospace text-secondary">
                                Rp {{ number_format($item['cost_price'], 0, ',', '.') }}
                            </td>
                            <td class="text-end font-monospace text-dark">
                                Rp {{ number_format($item['selling_price'], 0, ',', '.') }}
                            </td>
                            <td class="text-end font-monospace fw-bold text-dark">
                                Rp {{ number_format($item['asset_value'], 0, ',', '.') }}
                            </td>
                            <td class="pe-3 text-end font-monospace fw-bold text-success">
                                Rp {{ number_format($item['retail_value'], 0, ',', '.') }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center py-5 text-muted">
                                Tidak ada data varian produk di database.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
                <tfoot class="table-light">
                    <tr>
                        <th colspan="2" class="ps-3 fw-bold text-end">TOTAL KESELURUHAN GUDANG:</th>
                        <th class="text-center font-monospace fw-bold">{{ number_format($data['total_units'], 0, ',', '.') }} pack</th>
                        <th colspan="2"></th>
                        <th class="text-end font-monospace fw-bold text-dark fs-6">Rp {{ number_format($data['total_asset_value'], 0, ',', '.') }}</th>
                        <th class="pe-3 text-end font-monospace fw-bold text-success fs-6">Rp {{ number_format($data['total_retail_value'], 0, ',', '.') }}</th>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>
</div>
@endsection
