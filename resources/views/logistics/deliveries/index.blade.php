@extends('layouts.app')

@section('title', 'Daftar Surat Jalan & Pengiriman - Kopi Distro')

@section('content')
<div class="container py-3">
    <!-- Header Page -->
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-4">
        <div>
            <div class="d-flex align-items-center gap-2">
                <span class="badge bg-secondary bg-opacity-10 text-secondary px-3 py-2 rounded-pill fw-semibold">
                    <i class="bi bi-truck me-1"></i> Modul Pengiriman & Logistik
                </span>
                <span class="text-muted">&bull;</span>
                <span class="small text-muted">Tahap 6: Surat Jalan & Pelacakan</span>
            </div>
            <h2 class="fw-bold text-dark mt-2 mb-1">Surat Jalan & Pengiriman Barang</h2>
            <p class="text-muted mb-0">Kelola penerbitan surat jalan fisik, pantau status armada pengantaran, dan rekam serah terima barang di lokasi pelanggan.</p>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('orders.index') }}" class="btn btn-outline-coffee d-inline-flex align-items-center gap-2">
                <i class="bi bi-receipt"></i>
                <span>Pesanan Penjualan</span>
            </a>
            <a href="{{ route('inventory.index') }}" class="btn btn-coffee d-inline-flex align-items-center gap-2 shadow-sm">
                <i class="bi bi-box-seam"></i>
                <span>Stok Gudang</span>
            </a>
        </div>
    </div>

    <!-- KPI Metric Cards -->
    <div class="row g-3 mb-4">
        <div class="col-sm-6 col-lg-3">
            <div class="card card-custom p-3 border-start border-primary border-4">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <span class="text-muted small fw-semibold text-uppercase">Total Surat Jalan</span>
                        <h4 class="fw-bold mb-0 text-dark">{{ $metrics['total_deliveries'] }} Dokumen</h4>
                    </div>
                    <div class="p-3 rounded-3 bg-primary bg-opacity-10 text-primary">
                        <i class="bi bi-file-earmark-ruled fs-4"></i>
                    </div>
                </div>
                <small class="text-muted mt-2 d-block border-top pt-2">Akumulasi seluruh pengiriman</small>
            </div>
        </div>

        <div class="col-sm-6 col-lg-3">
            <div class="card card-custom p-3 border-start border-warning border-4">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <span class="text-muted small fw-semibold text-uppercase">Packing di Gudang</span>
                        <h4 class="fw-bold mb-0 text-warning">{{ $metrics['processing_count'] }} Paket</h4>
                    </div>
                    <div class="p-3 rounded-3 bg-warning bg-opacity-10 text-warning">
                        <i class="bi bi-box-seam fs-4"></i>
                    </div>
                </div>
                <small class="text-muted mt-2 d-block border-top pt-2">Sedang disiapkan / antre jemput</small>
            </div>
        </div>

        <div class="col-sm-6 col-lg-3">
            <div class="card card-custom p-3 border-start border-info border-4">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <span class="text-muted small fw-semibold text-uppercase">Dalam Perjalanan</span>
                        <h4 class="fw-bold mb-0 text-info">{{ $metrics['shipped_count'] }} Armada</h4>
                    </div>
                    <div class="p-3 rounded-3 bg-info bg-opacity-10 text-info">
                        <i class="bi bi-truck fs-4"></i>
                    </div>
                </div>
                <small class="text-muted mt-2 d-block border-top pt-2">Sedang dibawa kurir / ekspedisi</small>
            </div>
        </div>

        <div class="col-sm-6 col-lg-3">
            <div class="card card-custom p-3 border-start border-success border-4">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <span class="text-muted small fw-semibold text-uppercase">Selesai Diterima</span>
                        <h4 class="fw-bold mb-0 text-success">{{ $metrics['delivered_count'] }} Selesai</h4>
                    </div>
                    <div class="p-3 rounded-3 bg-success bg-opacity-10 text-success">
                        <i class="bi bi-check2-circle fs-4"></i>
                    </div>
                </div>
                <small class="text-muted mt-2 d-block border-top pt-2">Diterima fisik di lokasi pelanggan</small>
            </div>
        </div>
    </div>

    <!-- Filter & Search Toolbar -->
    <div class="card card-custom p-3 mb-4">
        <form method="GET" action="{{ route('delivery.index') }}" class="row g-2 align-items-center">
            <!-- Filter Status Tabs -->
            <div class="col-md-7 d-flex gap-1 flex-wrap">
                <a href="{{ route('delivery.index') }}" 
                   class="btn btn-sm {{ !request('status') ? 'btn-secondary text-white' : 'btn-outline-secondary' }}">
                    Semua
                </a>
                <a href="{{ route('delivery.index', array_merge(request()->except('page'), ['status' => 'processing'])) }}" 
                   class="btn btn-sm {{ request('status') === 'processing' ? 'btn-warning text-dark' : 'btn-outline-warning' }}">
                    <i class="bi bi-box-seam me-1"></i> Disiapkan Gudang
                </a>
                <a href="{{ route('delivery.index', array_merge(request()->except('page'), ['status' => 'shipped'])) }}" 
                   class="btn btn-sm {{ request('status') === 'shipped' ? 'btn-info text-dark' : 'btn-outline-info' }}">
                    <i class="bi bi-truck me-1"></i> Dalam Pengiriman
                </a>
                <a href="{{ route('delivery.index', array_merge(request()->except('page'), ['status' => 'delivered'])) }}" 
                   class="btn btn-sm {{ request('status') === 'delivered' ? 'btn-success' : 'btn-outline-success' }}">
                    <i class="bi bi-check2-circle me-1"></i> Selesai
                </a>
            </div>

            <!-- Search Input -->
            <div class="col-md-5">
                <div class="input-group">
                    <span class="input-group-text bg-white"><i class="bi bi-search text-muted"></i></span>
                    <input type="text" name="search" class="form-control" placeholder="Cari No SJ, No Order, sopir, penerima..." value="{{ request('search') }}">
                    <button class="btn btn-coffee" type="submit">Cari</button>
                    @if(request()->anyFilled(['search', 'status', 'courier_type']))
                        <a href="{{ route('delivery.index') }}" class="btn btn-outline-secondary">Reset</a>
                    @endif
                </div>
            </div>
        </form>
    </div>

    <!-- Delivery List Table -->
    <div class="card card-custom overflow-hidden border-0 shadow-sm">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th class="ps-3 py-3 text-secondary text-uppercase small fw-bold">No. Surat Jalan</th>
                        <th class="py-3 text-secondary text-uppercase small fw-bold">Tanggal</th>
                        <th class="py-3 text-secondary text-uppercase small fw-bold">Pesanan (Order)</th>
                        <th class="py-3 text-secondary text-uppercase small fw-bold">Penerima & Alamat</th>
                        <th class="py-3 text-secondary text-uppercase small fw-bold">Kurir / Armada</th>
                        <th class="py-3 text-secondary text-uppercase small fw-bold text-center">Muatan</th>
                        <th class="py-3 text-secondary text-uppercase small fw-bold text-center">Status</th>
                        <th class="pe-3 py-3 text-secondary text-uppercase small fw-bold text-end">Aksi</th>
                    </tr>
                </thead>
                <tbody class="border-top-0">
                    @forelse($deliveries as $del)
                        <tr>
                            <td class="ps-3 font-monospace fw-bold text-dark">
                                <a href="{{ route('delivery.show', $del) }}" class="text-decoration-none text-dark">
                                    {{ $del->delivery_number }}
                                </a>
                            </td>
                            <td class="text-muted small">
                                <i class="bi bi-calendar3 me-1"></i>{{ $del->delivery_date->format('d/m/Y') }}
                            </td>
                            <td>
                                <a href="{{ route('orders.show', $del->order) }}" class="font-monospace small text-primary text-decoration-none">
                                    {{ $del->order->order_number }}
                                </a>
                            </td>
                            <td>
                                <div class="fw-bold text-dark">{{ $del->recipient_name }}</div>
                                <small class="text-muted d-block text-truncate" style="max-width: 200px;" title="{{ $del->shipping_address }}">
                                    <i class="bi bi-geo-alt me-1"></i>{{ $del->shipping_address }}
                                </small>
                            </td>
                            <td>
                                @if($del->courier_type === \App\Models\DeliveryOrder::COURIER_INTERNAL)
                                    <span class="badge bg-light text-dark border small"><i class="bi bi-truck me-1"></i> Internal</span>
                                    <div class="small fw-semibold mt-1">{{ $del->driver_name ?? '-' }}</div>
                                    @if($del->vehicle_plate_number)
                                        <small class="text-muted font-monospace">{{ $del->vehicle_plate_number }}</small>
                                    @endif
                                @else
                                    <span class="badge bg-light text-primary border small"><i class="bi bi-box me-1"></i> {{ $del->courier_name ?? 'Ekspedisi' }}</span>
                                    @if($del->tracking_number)
                                        <div class="small font-monospace text-secondary mt-1">Resi: {{ $del->tracking_number }}</div>
                                    @endif
                                @endif
                            </td>
                            <td class="text-center">
                                <span class="badge bg-light text-secondary border px-2 py-1">
                                    {{ $del->total_units }} Pack
                                </span>
                            </td>
                            <td class="text-center">
                                <span class="badge {{ $del->status_badge_class }} px-3 py-2 rounded-pill">
                                    {{ $del->status_label }}
                                </span>
                            </td>
                            <td class="pe-3 text-end">
                                <div class="btn-group">
                                    <a href="{{ route('delivery.show', $del) }}" class="btn btn-sm btn-outline-primary px-2" title="Detail Pengiriman">
                                        <i class="bi bi-eye"></i>
                                    </a>
                                    <a href="{{ route('delivery.print', $del) }}" target="_blank" class="btn btn-sm btn-outline-secondary px-2" title="Cetak Surat Jalan">
                                        <i class="bi bi-printer"></i>
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="text-center py-5 text-muted">
                                <i class="bi bi-truck fs-1 text-secondary opacity-50 mb-3 d-block"></i>
                                <h6 class="fw-semibold text-dark">Belum ada dokumen surat jalan pengiriman</h6>
                                <p class="small text-muted mb-0">Surat jalan diterbitkan dari halaman pesanan yang telah berstatus dikonfirmasi (confirmed).</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($deliveries->hasPages())
            <div class="card-footer bg-white border-top py-3 d-flex justify-content-between align-items-center">
                <small class="text-muted">Menampilkan {{ $deliveries->firstItem() }} - {{ $deliveries->lastItem() }} dari {{ $deliveries->total() }} surat jalan</small>
                <div>{{ $deliveries->links() }}</div>
            </div>
        @endif
    </div>
</div>
@endsection
