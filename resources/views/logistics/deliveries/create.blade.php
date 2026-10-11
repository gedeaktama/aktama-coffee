@extends('layouts.app')

@section('title', 'Terbitkan Surat Jalan - Kopi Distro')

@section('content')
<div class="container py-3">
    <!-- Breadcrumb & Top Actions -->
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-4">
        <div>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-1">
                    <li class="breadcrumb-item"><a href="{{ route('delivery.index') }}" class="text-decoration-none">Pengiriman & Logistik</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Terbitkan Surat Jalan</li>
                </ol>
            </nav>
            <div class="d-flex align-items-center gap-2">
                <h2 class="fw-bold text-dark mb-0">Penerbitan Surat Jalan (Delivery Order)</h2>
                <span class="badge bg-primary bg-opacity-10 text-primary border border-primary border-opacity-25 px-2 py-1">
                    Order: {{ $order->order_number }}
                </span>
            </div>
            <p class="text-muted small mb-0">Terbitkan dokumen surat jalan resmi untuk barang pesanan yang siap dikeluarkan dari gudang penyimpanan.</p>
        </div>

        <a href="{{ route('orders.show', $order) }}" class="btn btn-outline-coffee">
            <i class="bi bi-arrow-left me-1"></i> Kembali ke Pesanan
        </a>
    </div>

    <form method="POST" action="{{ route('delivery.orders.store', $order) }}">
        @csrf

        <div class="row g-4">
            <!-- Left Column: Delivery Form Meta -->
            <div class="col-lg-7">
                <div class="card card-custom p-4 mb-4">
                    <h5 class="fw-bold text-dark mb-3 pb-2 border-bottom">
                        <i class="bi bi-truck text-primary me-2"></i> Informasi Ekspedisi & Pengantaran
                    </h5>

                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <label for="delivery_date" class="form-label fw-semibold">Tanggal Pengiriman <span class="text-danger">*</span></label>
                            <input type="date" name="delivery_date" id="delivery_date" class="form-control" value="{{ old('delivery_date', date('Y-m-d')) }}" required>
                        </div>
                        <div class="col-md-6">
                            <label for="courier_type" class="form-label fw-semibold">Metode / Jenis Pengiriman <span class="text-danger">*</span></label>
                            <select name="courier_type" id="courier_type" class="form-select" required onchange="toggleCourierFields()">
                                <option value="internal" {{ old('courier_type') === 'internal' ? 'selected' : '' }}>Armada Sendiri (Internal Driver / Kurir Toko)</option>
                                <option value="expedition" {{ old('courier_type') === 'expedition' ? 'selected' : '' }}>Ekspedisi Logistik Pihak Ke-3 (JNE, SiCepat, Lalamove, dll)</option>
                            </select>
                        </div>
                    </div>

                    <!-- Internal Armada Fields -->
                    <div id="internal_fields" class="p-3 bg-light rounded-3 border mb-3">
                        <h6 class="fw-bold text-dark mb-2"><i class="bi bi-person-badge me-1 text-primary"></i> Data Armada & Pengemudi Internal</h6>
                        <div class="row g-2">
                            <div class="col-md-6">
                                <label for="driver_name" class="form-label small fw-semibold">Nama Pengemudi / Sopir</label>
                                <input type="text" name="driver_name" id="driver_name" class="form-control" value="{{ old('driver_name', 'Bambang Supriyadi (Driver Toko)') }}" placeholder="Contoh: Pak Joko">
                            </div>
                            <div class="col-md-6">
                                <label for="driver_phone" class="form-label small fw-semibold">No. Telepon / HP Sopir</label>
                                <input type="text" name="driver_phone" id="driver_phone" class="form-control" value="{{ old('driver_phone', '0813-8899-7711') }}" placeholder="Contoh: 0812-xxxx-xxxx">
                            </div>
                            <div class="col-md-6 mt-2">
                                <label for="vehicle_plate_number" class="form-label small fw-semibold">Nomor Polisi / Plat Kendaraan</label>
                                <input type="text" name="vehicle_plate_number" id="vehicle_plate_number" class="form-control font-monospace" value="{{ old('vehicle_plate_number', 'B 9128 KD') }}" placeholder="Contoh: B 1234 ABC">
                            </div>
                        </div>
                    </div>

                    <!-- Ekspedisi Fields -->
                    <div id="expedition_fields" class="p-3 bg-light rounded-3 border mb-3 d-none">
                        <h6 class="fw-bold text-dark mb-2"><i class="bi bi-box-seam me-1 text-primary"></i> Data Ekspedisi & Nomor Resi</h6>
                        <div class="row g-2">
                            <div class="col-md-6">
                                <label for="courier_name" class="form-label small fw-semibold">Nama Perusahaan Ekspedisi</label>
                                <input type="text" name="courier_name" id="courier_name" class="form-control" value="{{ old('courier_name') }}" placeholder="Contoh: Lalamove / JNE Cargo / Deliveree">
                            </div>
                            <div class="col-md-6">
                                <label for="tracking_number" class="form-label small fw-semibold">Nomor Resi / Waybill (Tracking No.)</label>
                                <input type="text" name="tracking_number" id="tracking_number" class="form-control font-monospace" value="{{ old('tracking_number') }}" placeholder="Contoh: JNE8899201992">
                            </div>
                        </div>
                    </div>

                    <!-- Destination Information -->
                    <h5 class="fw-bold text-dark mb-3 mt-4 pb-2 border-bottom">
                        <i class="bi bi-geo-alt text-danger me-2"></i> Tujuan & Penerima Barang
                    </h5>

                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <label for="recipient_name" class="form-label fw-semibold">Nama Penerima <span class="text-danger">*</span></label>
                            <input type="text" name="recipient_name" id="recipient_name" class="form-control" value="{{ old('recipient_name', $order->recipient_name ?? ($order->customer->pic_name ?? '')) }}" required>
                        </div>
                        <div class="col-md-6">
                            <label for="recipient_phone" class="form-label fw-semibold">No. Telepon Penerima <span class="text-danger">*</span></label>
                            <input type="text" name="recipient_phone" id="recipient_phone" class="form-control" value="{{ old('recipient_phone', $order->recipient_phone ?? ($order->customer->phone ?? '')) }}" required>
                        </div>
                        <div class="col-12">
                            <label for="shipping_address" class="form-label fw-semibold">Alamat Lengkap Pengiriman <span class="text-danger">*</span></label>
                            <textarea name="shipping_address" id="shipping_address" class="form-control" rows="3" required>{{ old('shipping_address', $order->shipping_address ?? ($order->customer->address ?? '')) }}</textarea>
                        </div>
                    </div>

                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <label for="status" class="form-label fw-semibold">Status Awal Pengiriman <span class="text-danger">*</span></label>
                            <select name="status" id="status" class="form-select" required>
                                <option value="processing" {{ old('status') === 'processing' ? 'selected' : '' }}>Disiapkan di Gudang (Packing)</option>
                                <option value="shipped" {{ old('status') === 'shipped' ? 'selected' : '' }}>Langsung Dibawa Sopir (Dalam Pengiriman)</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label for="notes" class="form-label fw-semibold">Instruksi Khusus / Catatan Sopir</label>
                            <input type="text" name="notes" id="notes" class="form-control" value="{{ old('notes', 'Pastikan kemasan kopi tidak terkena air hujan & basah.') }}" placeholder="Contoh: Hubungi penerima sebelum tiba">
                        </div>
                    </div>
                </div>
            </div>

            <!-- Right Column: Order Summary & Item List -->
            <div class="col-lg-5">
                <div class="card card-custom p-4 mb-4">
                    <h5 class="fw-bold text-dark mb-3 pb-2 border-bottom">
                        <i class="bi bi-box2 text-success me-2"></i> Rincian Muatan Kopi
                    </h5>

                    <div class="p-3 bg-light rounded-3 border mb-3">
                        <div class="d-flex justify-content-between small mb-1">
                            <span class="text-muted">Nomor Pesanan:</span>
                            <strong class="font-monospace text-dark">{{ $order->order_number }}</strong>
                        </div>
                        <div class="d-flex justify-content-between small mb-1">
                            <span class="text-muted">Mitra / Pemesan:</span>
                            <span class="fw-semibold text-dark">{{ $order->customer->business_name ?? $order->recipient_name }}</span>
                        </div>
                        <div class="d-flex justify-content-between small">
                            <span class="text-muted">Nomor Surat Jalan:</span>
                            <strong class="font-monospace text-primary">{{ $autoDeliveryNumber }} (Otomatis)</strong>
                        </div>
                    </div>

                    <div class="table-responsive mb-3">
                        <table class="table table-sm table-bordered align-middle mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th class="py-2 small">Item Varian Kopi</th>
                                    <th class="py-2 small text-center" style="width: 80px;">Jumlah</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($order->items as $item)
                                    <tr>
                                        <td>
                                            <div class="fw-bold text-dark small">{{ $item->variant->product->name ?? 'Produk Kopi' }}</div>
                                            <small class="text-muted">{{ $item->variant->variant_name ?? '' }} ({{ $item->variant->package_weight_grams ?? 0 }}g)</small>
                                        </td>
                                        <td class="text-center fw-bold font-monospace">
                                            {{ $item->quantity }} pack
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                            <tfoot class="table-light">
                                <tr>
                                    <th class="small fw-bold">Total Muatan Fisik:</th>
                                    <th class="text-center font-monospace fw-bold text-primary">{{ $order->items->sum('quantity') }} Pack</th>
                                </tr>
                            </tfoot>
                        </table>
                    </div>

                    <div class="alert alert-info py-2 small mb-3">
                        <i class="bi bi-info-circle me-1"></i> Seluruh item pesanan di atas akan otomatis dimuat ke dalam dokumen fisik Surat Jalan ini.
                    </div>

                    <div class="d-grid gap-2">
                        <button type="submit" class="btn btn-coffee py-2 shadow-sm">
                            <i class="bi bi-check2-circle me-1"></i> Terbitkan & Cetak Surat Jalan
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>

@push('scripts')
<script>
    function toggleCourierFields() {
        const type = document.getElementById('courier_type').value;
        const internalBox = document.getElementById('internal_fields');
        const expeditionBox = document.getElementById('expedition_fields');

        if (type === 'internal') {
            internalBox.classList.remove('d-none');
            expeditionBox.classList.add('d-none');
        } else {
            internalBox.classList.add('d-none');
            expeditionBox.classList.remove('d-none');
        }
    }
    document.addEventListener('DOMContentLoaded', toggleCourierFields);
</script>
@endpush
@endsection
