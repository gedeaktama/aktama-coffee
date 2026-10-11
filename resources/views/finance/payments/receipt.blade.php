@extends('layouts.app')

@section('title', 'Kuitansi Pembayaran ' . $payment->payment_number . ' - Kopi Distro')

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
        .receipt-card {
            box-shadow: none !important;
            border: 1px solid #ddd !important;
        }
    }

    .receipt-card {
        background: #fff;
        border-radius: 12px;
        border: 2px dashed #d1c7bd;
    }
</style>
@endpush

@section('content')
<div class="container py-4">
    <!-- Top Action Toolbar -->
    <div class="d-flex justify-content-between align-items-center mb-4 no-print">
        <a href="{{ route('finance.invoices.show', $payment->invoice) }}" class="btn btn-outline-secondary">
            <i class="bi bi-arrow-left me-1"></i> Kembali ke Faktur
        </a>
        <div class="d-flex gap-2">
            <button type="button" class="btn btn-coffee px-4 shadow-sm" onclick="window.print()">
                <i class="bi bi-printer me-1"></i> Cetak Kuitansi Resmi
            </button>
        </div>
    </div>

    <!-- Official Receipt Paper Container -->
    <div class="card receipt-card p-4 p-md-5 mx-auto shadow-sm" style="max-width: 800px;">
        <!-- Header & Kop -->
        <div class="row align-items-center pb-4 border-bottom mb-4">
            <div class="col-sm-7">
                <div class="d-flex align-items-center gap-2 mb-1">
                    <i class="bi bi-cup-hot-fill text-warning fs-3"></i>
                    <h4 class="fw-bold text-dark mb-0 tracking-tight">KOPI DISTRO</h4>
                </div>
                <small class="text-muted d-block">Distribusi Biji & Bubuk Kopi Kemasan Premium</small>
                <small class="text-muted d-block">Jl. Kopi Nusantara No. 10, Jakarta Selatan</small>
                <small class="text-muted d-block"><i class="bi bi-telephone me-1"></i> (021) 7890-1234 &middot; WhatsApp: 0812-3456-7890</small>
            </div>
            <div class="col-sm-5 text-sm-end mt-3 mt-sm-0">
                <span class="badge bg-success bg-opacity-10 text-success border border-success border-opacity-25 px-3 py-2 fs-6 rounded-pill">
                    <i class="bi bi-check2-circle me-1"></i> LUNAS / SAH
                </span>
                <div class="mt-2">
                    <small class="text-muted d-block">Nomor Kuitansi Resmi:</small>
                    <span class="font-monospace fw-bold text-dark fs-5">{{ $payment->payment_number }}</span>
                </div>
            </div>
        </div>

        <div class="text-center my-2">
            <h5 class="fw-bold text-dark text-uppercase letter-spacing-1 mb-1">BUKTI PEMBAYARAN RESMI</h5>
            <small class="text-muted font-monospace">OFFICIAL PAYMENT RECEIPT</small>
        </div>

        <!-- Receipt Body Details -->
        <div class="my-4">
            <table class="table table-borderless align-middle mb-0">
                <tbody>
                    <tr>
                        <td class="text-muted text-nowrap" style="width: 220px;">Telah Diterima Dari</td>
                        <td class="fw-bold" style="width: 20px;">:</td>
                        <td>
                            <span class="fw-bold text-dark fs-6">
                                {{ $payment->customer->business_name ?? ($payment->invoice->order->recipient_name ?? 'Pelanggan Umum') }}
                            </span>
                            @if($payment->customer)
                                <span class="badge bg-light text-secondary border ms-1">{{ $payment->customer->type_label }}</span>
                                <small class="text-muted d-block">PIC: {{ $payment->customer->pic_name }} ({{ $payment->customer->phone }})</small>
                            @endif
                        </td>
                    </tr>
                    <tr>
                        <td class="text-muted text-nowrap">Tanggal Pembayaran</td>
                        <td class="fw-bold">:</td>
                        <td class="fw-semibold text-dark">
                            {{ $payment->payment_date->translatedFormat('d F Y') }}
                        </td>
                    </tr>
                    <tr>
                        <td class="text-muted text-nowrap">Untuk Pembayaran Faktur</td>
                        <td class="fw-bold">:</td>
                        <td>
                            <strong class="font-monospace text-primary">{{ $payment->invoice->invoice_number }}</strong>
                            <span class="text-muted small ms-2">(Order Penjualan: {{ $payment->invoice->order->order_number }})</span>
                        </td>
                    </tr>
                    <tr>
                        <td class="text-muted text-nowrap">Metode Pembayaran</td>
                        <td class="fw-bold">:</td>
                        <td>
                            <span class="badge bg-light text-dark border">{{ $payment->method_label }}</span>
                            @if($payment->reference_number)
                                <span class="ms-2 small text-secondary">
                                    No. Ref / Bukti: <strong class="font-monospace">{{ $payment->reference_number }}</strong>
                                </span>
                            @endif
                        </td>
                    </tr>
                    @if($payment->notes)
                        <tr>
                            <td class="text-muted text-nowrap">Keterangan / Catatan</td>
                            <td class="fw-bold">:</td>
                            <td class="fst-italic text-secondary">
                                "{{ $payment->notes }}"
                            </td>
                        </tr>
                    @endif
                </tbody>
            </table>
        </div>

        <!-- Highlight Amount Box -->
        <div class="p-3 my-3 bg-light rounded-3 border d-flex justify-content-between align-items-center flex-wrap gap-2">
            <div>
                <small class="text-muted text-uppercase fw-semibold d-block">Jumlah Pembayaran Diterima</small>
                <h3 class="fw-bold text-success mb-0 font-monospace">{{ $payment->formatted_amount }}</h3>
            </div>
            <div class="text-end">
                <small class="text-muted d-block">Sisa Tagihan Faktur Sekarang:</small>
                <strong class="font-monospace fs-6 {{ $payment->invoice->remaining_amount > 0 ? 'text-danger' : 'text-success' }}">
                    {{ $payment->invoice->formatted_remaining }}
                </strong>
            </div>
        </div>

        <!-- Footer Signatures -->
        <div class="row pt-5 mt-4 text-center">
            <div class="col-6">
                <small class="text-muted d-block mb-5">Penyetor / Mitra,</small>
                <div class="border-top mx-auto" style="width: 180px;"></div>
                <small class="fw-semibold text-dark mt-1 d-block">
                    {{ $payment->customer->pic_name ?? ($payment->invoice->order->recipient_name ?? 'Pelanggan') }}
                </small>
            </div>
            <div class="col-6">
                <small class="text-muted d-block mb-5">Diterima Oleh (Finance / Kasir),</small>
                <div class="border-top mx-auto" style="width: 180px;"></div>
                <small class="fw-semibold text-dark mt-1 d-block">
                    {{ $payment->user->name ?? 'Bagian Keuangan' }}
                </small>
                <small class="text-muted d-block" style="font-size: 0.72rem;">Kopi Distro Management</small>
            </div>
        </div>
    </div>
</div>
@endsection
