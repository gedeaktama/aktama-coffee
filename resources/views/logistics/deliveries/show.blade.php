@extends('layouts.app')

@section('title', 'Surat Jalan ' . $deliveryOrder->delivery_number . ' - Kopi Distro')

@section('content')
<div class="container py-3">
    <!-- Breadcrumb & Top Actions -->
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-4">
        <div>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-1">
                    <li class="breadcrumb-item"><a href="{{ route('delivery.index') }}" class="text-decoration-none">Pengiriman & Logistik</a></li>
                    <li class="breadcrumb-item active" aria-current="page">{{ $deliveryOrder->delivery_number }}</li>
                </ol>
            </nav>
            <div class="d-flex align-items-center gap-2 flex-wrap">
                <h2 class="fw-bold text-dark font-monospace mb-0">{{ $deliveryOrder->delivery_number }}</h2>
                <span class="badge {{ $deliveryOrder->status_badge_class }} px-3 py-1 fs-6 rounded-pill">
                    {{ $deliveryOrder->status_label }}
                </span>
                <span class="badge bg-light text-dark border font-monospace">
                    Order: {{ $deliveryOrder->order->order_number }}
                </span>
            </div>
        </div>

        <div class="d-flex gap-2">
            <a href="{{ route('delivery.print', $deliveryOrder) }}" target="_blank" class="btn btn-outline-secondary">
                <i class="bi bi-printer me-1"></i> Cetak Dokumen Fisik (SJ)
            </a>
            <a href="{{ route('delivery.index') }}" class="btn btn-outline-coffee">
                <i class="bi bi-arrow-left me-1"></i> Daftar Surat Jalan
            </a>
        </div>
    </div>

    <!-- Status Action Banners -->
    @if($deliveryOrder->isProcessing())
        <div class="card bg-warning bg-opacity-10 border-warning border-opacity-50 p-3 mb-4">
            <div class="d-flex align-items-center justify-content-between flex-wrap gap-2">
                <div class="d-flex align-items-center gap-3">
                    <i class="bi bi-box-seam text-warning fs-3"></i>
                    <div>
                        <h6 class="fw-bold text-dark mb-1">Paket Sedang Disiapkan di Gudang</h6>
                        <small class="text-muted">Barang telah dipacking dan menunggu armada sopir / penjemputan ekspedisi.</small>
                    </div>
                </div>
                @if(auth()->user()->hasRole(['owner', 'admin', 'warehouse']))
                    <button type="button" class="btn btn-info btn-sm px-3 shadow-sm text-dark fw-semibold" data-bs-toggle="modal" data-bs-target="#markShippedModal">
                        <i class="bi bi-truck me-1"></i> Mulai Pengiriman (Keluarkan Barang)
                    </button>
                @endif
            </div>
        </div>
    @elseif($deliveryOrder->isShipped())
        <div class="card bg-info bg-opacity-10 border-info border-opacity-50 p-3 mb-4">
            <div class="d-flex align-items-center justify-content-between flex-wrap gap-2">
                <div class="d-flex align-items-center gap-3">
                    <i class="bi bi-truck text-info fs-3"></i>
                    <div>
                        <h6 class="fw-bold text-dark mb-1">Barang Dalam Perjalanan Menuju Lokasi Penerima</h6>
                        <small class="text-muted">
                            Dibawa oleh: <strong>{{ $deliveryOrder->driver_name ?? ($deliveryOrder->courier_name ?? 'Kurir') }}</strong> 
                            @if($deliveryOrder->vehicle_plate_number) (Plat: {{ $deliveryOrder->vehicle_plate_number }}) @endif
                            @if($deliveryOrder->tracking_number) &bull; No. Resi: <strong>{{ $deliveryOrder->tracking_number }}</strong> @endif
                        </small>
                    </div>
                </div>
                @if(auth()->user()->hasRole(['owner', 'admin', 'warehouse', 'sales']))
                    <button type="button" class="btn btn-success btn-sm px-3 shadow-sm" data-bs-toggle="modal" data-bs-target="#markDeliveredModal">
                        <i class="bi bi-check2-circle me-1"></i> Konfirmasi Barang Selesai Diterima
                    </button>
                @endif
            </div>
        </div>
    @elseif($deliveryOrder->isDelivered())
        <div class="card bg-success bg-opacity-10 border-success border-opacity-50 p-3 mb-4">
            <div class="d-flex align-items-center gap-3">
                <i class="bi bi-check-circle-fill text-success fs-3"></i>
                <div>
                    <h6 class="fw-bold text-success mb-1">Pengiriman Telah Selesai Diterima di Lokasi</h6>
                    <small class="text-muted">
                        Diterima oleh <strong>{{ $deliveryOrder->received_by }}</strong> 
                        pada <strong>{{ $deliveryOrder->received_at ? $deliveryOrder->received_at->translatedFormat('d F Y, H:i') : '-' }} WIB</strong>.
                    </small>
                </div>
            </div>
        </div>
    @endif

    <div class="row g-4">
        <!-- Left: Destination & Courier Meta -->
        <div class="col-lg-4">
            <!-- Delivery Info -->
            <div class="card card-custom p-4 mb-4">
                <h6 class="fw-bold text-dark mb-3 pb-2 border-bottom">
                    <i class="bi bi-truck text-primary me-2"></i> Detail Armada & Pengantaran
                </h6>
                <div class="mb-2">
                    <small class="text-muted d-block">Tanggal Surat Jalan</small>
                    <span class="fw-semibold">{{ $deliveryOrder->delivery_date->translatedFormat('d F Y') }}</span>
                </div>
                <div class="mb-2">
                    <small class="text-muted d-block">Jenis Ekspedisi</small>
                    <span class="badge bg-light text-dark border">{{ \App\Models\DeliveryOrder::COURIER_TYPES[$deliveryOrder->courier_type] ?? $deliveryOrder->courier_type }}</span>
                </div>
                @if($deliveryOrder->driver_name)
                    <div class="mb-2">
                        <small class="text-muted d-block">Nama Sopir / Pengemudi</small>
                        <span class="fw-semibold text-dark">{{ $deliveryOrder->driver_name }}</span>
                    </div>
                @endif
                @if($deliveryOrder->driver_phone)
                    <div class="mb-2">
                        <small class="text-muted d-block">Kontak Sopir</small>
                        <span class="fw-semibold">{{ $deliveryOrder->driver_phone }}</span>
                    </div>
                @endif
                @if($deliveryOrder->vehicle_plate_number)
                    <div class="mb-2">
                        <small class="text-muted d-block">Plat Nomor Kendaraan</small>
                        <span class="font-monospace fw-bold">{{ $deliveryOrder->vehicle_plate_number }}</span>
                    </div>
                @endif
                @if($deliveryOrder->courier_name)
                    <div class="mb-2">
                        <small class="text-muted d-block">Nama Ekspedisi</small>
                        <span class="fw-semibold text-primary">{{ $deliveryOrder->courier_name }}</span>
                    </div>
                @endif
                @if($deliveryOrder->tracking_number)
                    <div class="mb-2">
                        <small class="text-muted d-block">Nomor Resi Pelacakan</small>
                        <span class="font-monospace fw-bold text-dark">{{ $deliveryOrder->tracking_number }}</span>
                    </div>
                @endif
                <div class="mb-2 border-top pt-2">
                    <small class="text-muted d-block">Petugas Pembuat Surat Jalan</small>
                    <span class="fw-semibold">{{ $deliveryOrder->user->name ?? 'Gudang' }}</span>
                </div>
            </div>

            <!-- Recipient Info -->
            <div class="card card-custom p-4 mb-4">
                <h6 class="fw-bold text-dark mb-3 pb-2 border-bottom">
                    <i class="bi bi-geo-alt text-danger me-2"></i> Penerima & Alamat Tujuan
                </h6>
                <div class="mb-2">
                    <small class="text-muted d-block">Nama Penerima</small>
                    <span class="fw-bold text-dark fs-6">{{ $deliveryOrder->recipient_name }}</span>
                    @if($deliveryOrder->customer)
                        <span class="badge bg-light text-secondary border ms-1">{{ $deliveryOrder->customer->business_name }}</span>
                    @endif
                </div>
                <div class="mb-2">
                    <small class="text-muted d-block">Telepon / WhatsApp</small>
                    <span class="fw-semibold">{{ $deliveryOrder->recipient_phone }}</span>
                </div>
                <div class="mb-2">
                    <small class="text-muted d-block">Alamat Pengiriman</small>
                    <p class="small text-dark mb-0">{{ $deliveryOrder->shipping_address }}</p>
                </div>
                @if($deliveryOrder->notes)
                    <div class="p-2 rounded bg-light border small mt-2">
                        <strong class="text-muted d-block">Catatan:</strong>
                        <span class="fst-italic text-secondary">"{{ $deliveryOrder->notes }}"</span>
                    </div>
                @endif
            </div>
        </div>

        <!-- Right: Items Loaded & Logistics Progression -->
        <div class="col-lg-8">
            <div class="card card-custom overflow-hidden border-0 shadow-sm mb-4">
                <div class="card-header bg-white py-3 border-bottom d-flex justify-content-between align-items-center">
                    <h5 class="fw-bold text-dark mb-0">
                        <i class="bi bi-boxes text-coffee me-2"></i> Muatan Barang yang Dikirim
                    </h5>
                    <span class="badge bg-primary bg-opacity-10 text-primary border border-primary border-opacity-25 px-2 py-1">
                        Total: {{ $deliveryOrder->total_units }} Pack
                    </span>
                </div>
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th class="ps-3 py-3 text-secondary text-uppercase small fw-bold">#</th>
                                <th class="py-3 text-secondary text-uppercase small fw-bold">Varian Produk Kopi</th>
                                <th class="py-3 text-secondary text-uppercase small fw-bold">SKU</th>
                                <th class="py-3 text-secondary text-uppercase small fw-bold text-center">Jumlah Dikirim</th>
                                <th class="pe-3 py-3 text-secondary text-uppercase small fw-bold">Keterangan Kemasan</th>
                            </tr>
                        </thead>
                        <tbody class="border-top-0">
                            @foreach($deliveryOrder->items as $idx => $item)
                                <tr>
                                    <td class="ps-3 text-muted">{{ $idx + 1 }}</td>
                                    <td>
                                        <div class="fw-bold text-dark">{{ $item->variant->product->name ?? 'Produk Kopi' }}</div>
                                        <small class="text-muted">{{ $item->variant->variant_name ?? '' }} ({{ $item->variant->package_weight_grams ?? 0 }}g)</small>
                                    </td>
                                    <td class="font-monospace small text-secondary">
                                        {{ $item->variant->sku ?? '-' }}
                                    </td>
                                    <td class="text-center font-monospace fw-bold text-primary fs-6">
                                        {{ $item->quantity }} pack
                                    </td>
                                    <td class="pe-3 small text-muted">
                                        {{ $item->package_info ?? '-' }}
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                        <tfoot class="table-light">
                            <tr>
                                <th colspan="3" class="ps-3 fw-bold text-end">Total Fisik Koli / Pack:</th>
                                <th class="text-center font-monospace fw-bold text-primary fs-6">{{ $deliveryOrder->total_units }} pack</th>
                                <th></th>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>

            <!-- Linked Order & Invoice Links -->
            <div class="card card-custom p-4">
                <h6 class="fw-bold text-dark mb-3 pb-2 border-bottom">
                    <i class="bi bi-link-45deg text-secondary me-2"></i> Dokumen Transaksi Terkait
                </h6>
                <div class="row g-3">
                    <div class="col-md-6">
                        <div class="p-3 rounded-3 bg-light border d-flex justify-content-between align-items-center">
                            <div>
                                <small class="text-muted d-block">Pesanan Penjualan:</small>
                                <strong class="font-monospace text-dark">{{ $deliveryOrder->order->order_number }}</strong>
                            </div>
                            <a href="{{ route('orders.show', $deliveryOrder->order) }}" class="btn btn-sm btn-outline-coffee">
                                Buka Order &rarr;
                            </a>
                        </div>
                    </div>
                    @if($deliveryOrder->order->invoice)
                        <div class="col-md-6">
                            <div class="p-3 rounded-3 bg-light border d-flex justify-content-between align-items-center">
                                <div>
                                    <small class="text-muted d-block">Faktur Tagihan:</small>
                                    <strong class="font-monospace text-primary">{{ $deliveryOrder->order->invoice->invoice_number }}</strong>
                                </div>
                                <a href="{{ route('finance.invoices.show', $deliveryOrder->order->invoice) }}" class="btn btn-sm btn-outline-primary">
                                    Buka Faktur &rarr;
                                </a>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal Mark Shipped -->
<div class="modal fade" id="markShippedModal" tabindex="-1" aria-labelledby="markShippedModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form method="POST" action="{{ route('delivery.mark_shipped', $deliveryOrder) }}">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title fw-bold" id="markShippedModalLabel">
                        <i class="bi bi-truck text-info me-2"></i> Mulai Pengiriman Barang
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4">
                    <p class="small text-muted mb-3">
                        Konfirmasi bahwa barang pesanan telah selesai dikemas di gudang dan telah dibawa oleh sopir / diserahkan kepada kurir ekspedisi.
                    </p>
                    <div class="mb-3">
                        <label for="modal_driver_name" class="form-label fw-semibold">Nama Pengemudi / Kurir</label>
                        <input type="text" name="driver_name" id="modal_driver_name" class="form-control" value="{{ $deliveryOrder->driver_name }}" placeholder="Nama pengemudi yang membawa barang">
                    </div>
                    <div class="mb-3">
                        <label for="modal_tracking_number" class="form-label fw-semibold">Nomor Resi / Waybill (Jika ada)</label>
                        <input type="text" name="tracking_number" id="modal_tracking_number" class="form-control" value="{{ $deliveryOrder->tracking_number }}" placeholder="Nomor resi pengiriman">
                    </div>
                    <div class="mb-3">
                        <label for="modal_notes" class="form-label fw-semibold">Catatan Keberangkatan</label>
                        <input type="text" name="notes" id="modal_notes" class="form-control" value="{{ $deliveryOrder->notes }}" placeholder="Contoh: Berangkat dari gudang pukul 10.30 WIB">
                    </div>
                </div>
                <div class="modal-footer bg-light">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-info text-dark fw-semibold px-4">
                        <i class="bi bi-truck me-1"></i> Konfirmasi Barang Berangkat
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Mark Delivered -->
<div class="modal fade" id="markDeliveredModal" tabindex="-1" aria-labelledby="markDeliveredModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form method="POST" action="{{ route('delivery.mark_delivered', $deliveryOrder) }}">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title fw-bold" id="markDeliveredModalLabel">
                        <i class="bi bi-check-circle-fill text-success me-2"></i> Konfirmasi Barang Sampai & Diterima
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4">
                    <p class="small text-muted mb-3">
                        Rekam nama orang yang menerima fisik barang di lokasi tujuan dan waktu serah terima.
                    </p>
                    <div class="mb-3">
                        <label for="received_by" class="form-label fw-semibold">Nama Penerima Fisik <span class="text-danger">*</span></label>
                        <input type="text" name="received_by" id="received_by" class="form-control" value="{{ old('received_by', $deliveryOrder->recipient_name) }}" required placeholder="Contoh: Barista Andi / Mas Joko (Kafe)">
                    </div>
                    <div class="mb-3">
                        <label for="received_at" class="form-label fw-semibold">Waktu Diterima</label>
                        <input type="datetime-local" name="received_at" id="received_at" class="form-control" value="{{ date('Y-m-d\TH:i') }}">
                    </div>
                    <div class="mb-3">
                        <label for="delivered_notes" class="form-label fw-semibold">Catatan Serah Terima</label>
                        <input type="text" name="notes" id="delivered_notes" class="form-control" placeholder="Contoh: Barang diterima lengkap dalam kondisi segel baik">
                    </div>
                </div>
                <div class="modal-footer bg-light">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-success px-4">
                        <i class="bi bi-check2-circle me-1"></i> Konfirmasi Selesai Diterima
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
