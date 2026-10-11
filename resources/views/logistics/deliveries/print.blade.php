@extends('layouts.app')

@section('title', 'Cetak Surat Jalan ' . $deliveryOrder->delivery_number . ' - Kopi Distro')

@push('styles')
<style>
    @media print {
        header, nav, footer, .no-print, .btn {
            display: none !important;
        }
        body {
            background-color: #ffffff !important;
            padding: 0 !important;
            margin: 0 !important;
        }
        .container {
            max-width: 100% !important;
            width: 100% !important;
            padding: 0 !important;
        }
        .sj-paper {
            box-shadow: none !important;
            border: 1px solid #000 !important;
        }
    }

    .sj-paper {
        background: #fff;
        border-radius: 8px;
        border: 1px solid #dee2e6;
    }
</style>
@endpush

@section('content')
<div class="container py-4">
    <!-- Top Action Toolbar -->
    <div class="d-flex justify-content-between align-items-center mb-4 no-print">
        <a href="{{ route('delivery.show', $deliveryOrder) }}" class="btn btn-outline-secondary">
            <i class="bi bi-arrow-left me-1"></i> Kembali ke Rincian
        </a>
        <div class="d-flex gap-2">
            <button type="button" class="btn btn-coffee px-4 shadow-sm" onclick="window.print()">
                <i class="bi bi-printer me-1"></i> Cetak Surat Jalan Resmi (Print)
            </button>
        </div>
    </div>

    <!-- Official Delivery Note Document Paper -->
    <div class="card sj-paper p-4 p-md-5 mx-auto shadow-sm" style="max-width: 900px;">
        <!-- Header & Kop Perusahaan -->
        <div class="row align-items-center pb-3 border-bottom border-2 border-dark mb-4">
            <div class="col-7">
                <div class="d-flex align-items-center gap-2 mb-1">
                    <i class="bi bi-cup-hot-fill text-warning fs-2"></i>
                    <h3 class="fw-bold text-dark mb-0 tracking-tight">KOPI DISTRO</h3>
                </div>
                <small class="text-muted d-block">Distribusi Biji & Bubuk Kopi Kemasan Segar Nusantara</small>
                <small class="text-muted d-block">Gudang Logistik: Pergudangan Kopi Center Blok C-8, Jakarta Selatan</small>
                <small class="text-muted d-block"><i class="bi bi-telephone me-1"></i> Telp: (021) 7890-1234 &middot; Hotline Armada: 0812-3456-7890</small>
            </div>
            <div class="col-5 text-end">
                <h4 class="fw-bold text-dark text-uppercase tracking-wider mb-1">SURAT JALAN</h4>
                <small class="text-muted font-monospace d-block">DELIVERY ORDER</small>
                <div class="mt-2">
                    <span class="font-monospace fw-bold fs-5 text-dark">{{ $deliveryOrder->delivery_number }}</span>
                </div>
            </div>
        </div>

        <!-- Meta Delivery Details -->
        <div class="row g-4 mb-4">
            <div class="col-6">
                <table class="table table-sm table-borderless mb-0">
                    <tr>
                        <td class="text-muted text-nowrap" style="width: 140px;">Kepada Yth.</td>
                        <td class="fw-bold">:</td>
                        <td>
                            <strong class="text-dark fs-6">{{ $deliveryOrder->recipient_name }}</strong>
                            @if($deliveryOrder->customer)
                                <div class="text-muted small">({{ $deliveryOrder->customer->business_name }})</div>
                            @endif
                        </td>
                    </tr>
                    <tr>
                        <td class="text-muted text-nowrap">Telepon / HP</td>
                        <td class="fw-bold">:</td>
                        <td class="fw-semibold text-dark">{{ $deliveryOrder->recipient_phone }}</td>
                    </tr>
                    <tr>
                        <td class="text-muted text-nowrap">Alamat Kirim</td>
                        <td class="fw-bold">:</td>
                        <td class="small text-dark">{{ $deliveryOrder->shipping_address }}</td>
                    </tr>
                </table>
            </div>

            <div class="col-6">
                <table class="table table-sm table-borderless mb-0">
                    <tr>
                        <td class="text-muted text-nowrap" style="width: 150px;">Tanggal Kirim</td>
                        <td class="fw-bold">:</td>
                        <td class="fw-semibold text-dark">{{ $deliveryOrder->delivery_date->translatedFormat('d F Y') }}</td>
                    </tr>
                    <tr>
                        <td class="text-muted text-nowrap">No. Pesanan (Order)</td>
                        <td class="fw-bold">:</td>
                        <td><strong class="font-monospace text-dark">{{ $deliveryOrder->order->order_number }}</strong></td>
                    </tr>
                    <tr>
                        <td class="text-muted text-nowrap">Armada / Kurir</td>
                        <td class="fw-bold">:</td>
                        <td>
                            <span class="text-dark">{{ $deliveryOrder->driver_name ?? ($deliveryOrder->courier_name ?? 'Internal Store Driver') }}</span>
                            @if($deliveryOrder->vehicle_plate_number)
                                <span class="badge bg-light text-dark border ms-1 font-monospace">{{ $deliveryOrder->vehicle_plate_number }}</span>
                            @endif
                        </td>
                    </tr>
                    @if($deliveryOrder->tracking_number)
                        <tr>
                            <td class="text-muted text-nowrap">No. Resi Ekspedisi</td>
                            <td class="fw-bold">:</td>
                            <td><strong class="font-monospace text-primary">{{ $deliveryOrder->tracking_number }}</strong></td>
                        </tr>
                    @endif
                </table>
            </div>
        </div>

        <!-- Items Table -->
        <div class="table-responsive my-3">
            <table class="table table-bordered align-middle mb-0">
                <thead class="table-light border-dark">
                    <tr class="text-center small fw-bold text-uppercase">
                        <th style="width: 40px;">No</th>
                        <th>Varian Produk Kopi</th>
                        <th style="width: 130px;">Kode SKU</th>
                        <th style="width: 100px;">Bentuk</th>
                        <th style="width: 110px;">Qty Dikirim</th>
                        <th style="width: 160px;">Kemasan / Koli</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($deliveryOrder->items as $idx => $item)
                        <tr>
                            <td class="text-center">{{ $idx + 1 }}</td>
                            <td>
                                <strong class="text-dark">{{ $item->variant->product->name ?? 'Produk Kopi' }}</strong>
                                <small class="text-muted d-block">{{ $item->variant->variant_name ?? '' }} ({{ $item->variant->package_weight_grams ?? 0 }}g)</small>
                            </td>
                            <td class="font-monospace small text-center text-secondary">
                                {{ $item->variant->sku ?? '-' }}
                            </td>
                            <td class="text-center small text-capitalize">
                                {{ $item->variant->grind_form ?? 'Kemasan' }}
                            </td>
                            <td class="text-center font-monospace fw-bold text-dark fs-6">
                                {{ $item->quantity }} pack
                            </td>
                            <td class="small text-muted text-center">
                                {{ $item->package_info ?? ($item->quantity . ' Pack') }}
                            </td>
                        </tr>
                    @endforeach
                </tbody>
                <tfoot class="table-light border-dark">
                    <tr>
                        <th colspan="4" class="text-end fw-bold">TOTAL MUATAN FISIK:</th>
                        <th class="text-center font-monospace fw-bold fs-6 text-dark">{{ $deliveryOrder->total_units }} pack</th>
                        <th class="text-center small text-muted">Lengkap & Tersegel</th>
                    </tr>
                </tfoot>
            </table>
        </div>

        <!-- Notes / Terms -->
        <div class="p-3 bg-light rounded border my-3">
            <small class="text-muted d-block fw-bold mb-1">Catatan & Ketentuan Pengiriman:</small>
            <p class="small text-secondary mb-0">
                1. Periksa kondisi segel kemasan dan kuantitas barang fisik saat serah terima di tempat.<br>
                2. Komplain kekurangan atau kerusakan barang wajib dicantumkan dalam surat jalan ini sebelum ditandatangani.<br>
                3. {{ $deliveryOrder->notes ?? 'Barang telah diperiksa dalam kondisi baik dan siap diterima oleh pihak pemesan.' }}
            </p>
        </div>

        <!-- Signatures (3 Pihak: Gudang, Pengemudi, Penerima) -->
        <div class="row pt-4 mt-2 text-center">
            <div class="col-4">
                <small class="text-muted d-block mb-5">Petugas Gudang / Pengirim,</small>
                <div class="border-top border-dark mx-auto" style="width: 150px;"></div>
                <small class="fw-semibold text-dark mt-1 d-block">
                    ( {{ $deliveryOrder->user->name ?? 'Staf Gudang' }} )
                </small>
            </div>
            <div class="col-4">
                <small class="text-muted d-block mb-5">Pengemudi / Kurir Armada,</small>
                <div class="border-top border-dark mx-auto" style="width: 150px;"></div>
                <small class="fw-semibold text-dark mt-1 d-block">
                    ( {{ $deliveryOrder->driver_name ?? ($deliveryOrder->courier_name ?? 'Sopir / Kurir') }} )
                </small>
            </div>
            <div class="col-4">
                <small class="text-muted d-block mb-5">Penerima Barang di Lokasi,</small>
                <div class="border-top border-dark mx-auto" style="width: 150px;"></div>
                <small class="fw-semibold text-dark mt-1 d-block">
                    ( {{ $deliveryOrder->received_by ?? $deliveryOrder->recipient_name }} )
                </small>
                <small class="text-muted d-block" style="font-size: 0.7rem;">Nama Terang & Cap Toko</small>
            </div>
        </div>
    </div>
</div>
@endsection
