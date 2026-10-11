@extends('layouts.app')

@section('title', 'Kartu Piutang ' . $customer->business_name . ' - Kopi Distro')

@section('content')
<div class="container py-3">
    <!-- Breadcrumb & Top Actions -->
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-4">
        <div>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-1">
                    <li class="breadcrumb-item"><a href="{{ route('finance.receivables.index') }}" class="text-decoration-none">Monitoring Piutang</a></li>
                    <li class="breadcrumb-item active" aria-current="page">{{ $customer->business_name }}</li>
                </ol>
            </nav>
            <div class="d-flex align-items-center gap-2 flex-wrap">
                <h2 class="fw-bold text-dark mb-0">{{ $customer->business_name }}</h2>
                <span class="badge bg-secondary font-monospace">{{ $customer->code }}</span>
                <span class="badge bg-light text-dark border">{{ $customer->type_label }}</span>
                @if($customer->payment_terms_days > 0)
                    <span class="badge bg-info bg-opacity-10 text-info border border-info border-opacity-25">
                        <i class="bi bi-calendar3 me-1"></i> TOP {{ $customer->payment_terms_days }} Hari
                    </span>
                @endif
            </div>
        </div>

        <div class="d-flex gap-2">
            <button type="button" class="btn btn-outline-secondary" onclick="window.print()">
                <i class="bi bi-printer me-1"></i> Cetak Kartu Piutang
            </button>
            <a href="{{ route('finance.receivables.index') }}" class="btn btn-outline-coffee">
                <i class="bi bi-arrow-left me-1"></i> Kembali
            </a>
        </div>
    </div>

    <!-- Financial Status Cards -->
    <div class="row g-3 mb-4">
        <div class="col-sm-6 col-lg-3">
            <div class="card card-custom p-3 border-start border-primary border-4">
                <span class="text-muted small fw-semibold text-uppercase">Plafon Kredit Disetujui</span>
                <h4 class="fw-bold mb-0 text-dark">Rp {{ number_format($customer->credit_limit, 0, ',', '.') }}</h4>
                <small class="text-muted mt-2 d-block border-top pt-2">Batas maksimal hutang tempo</small>
            </div>
        </div>

        <div class="col-sm-6 col-lg-3">
            <div class="card card-custom p-3 border-start {{ $receivables > 0 ? 'border-warning' : 'border-success' }} border-4">
                <span class="text-muted small fw-semibold text-uppercase">Saldo Piutang Aktif</span>
                <h4 class="fw-bold mb-0 {{ $receivables > 0 ? 'text-warning' : 'text-success' }}">
                    Rp {{ number_format($receivables, 0, ',', '.') }}
                </h4>
                <small class="text-muted mt-2 d-block border-top pt-2">Tagihan faktur belum lunas</small>
            </div>
        </div>

        <div class="col-sm-6 col-lg-3">
            <div class="card card-custom p-3 border-start {{ $availableCredit > 0 ? 'border-success' : 'border-danger' }} border-4">
                <span class="text-muted small fw-semibold text-uppercase">Sisa Plafon Tersedia</span>
                <h4 class="fw-bold mb-0 {{ $availableCredit > 0 ? 'text-success' : 'text-danger' }}">
                    Rp {{ number_format($availableCredit, 0, ',', '.') }}
                </h4>
                <small class="text-muted mt-2 d-block border-top pt-2">Kapasitas order grosir tempo</small>
            </div>
        </div>

        <div class="col-sm-6 col-lg-3">
            <div class="card card-custom p-3 border-start border-info border-4">
                <span class="text-muted small fw-semibold text-uppercase">Kontak Penanggung Jawab</span>
                <h6 class="fw-bold mb-0 text-dark mt-1">{{ $customer->pic_name }}</h6>
                <small class="text-muted mt-2 d-block border-top pt-2">
                    <i class="bi bi-whatsapp text-success me-1"></i> {{ $customer->phone }}
                </small>
            </div>
        </div>
    </div>

    <!-- Section: Daftar Faktur Tagihan (Debet Piutang) -->
    <div class="card card-custom mb-4 overflow-hidden border-0 shadow-sm">
        <div class="card-header bg-white py-3 border-bottom d-flex justify-content-between align-items-center">
            <h5 class="fw-bold text-dark mb-0">
                <i class="bi bi-file-earmark-text text-primary me-2"></i> Riwayat Faktur Penjualan (Debet)
            </h5>
            <span class="badge bg-light text-secondary border">{{ $invoices->count() }} Faktur</span>
        </div>
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th class="ps-3 py-3 text-secondary text-uppercase small fw-bold">No. Faktur</th>
                        <th class="py-3 text-secondary text-uppercase small fw-bold">Tgl. Faktur</th>
                        <th class="py-3 text-secondary text-uppercase small fw-bold">Jatuh Tempo</th>
                        <th class="py-3 text-secondary text-uppercase small fw-bold">Pesanan (Order)</th>
                        <th class="py-3 text-secondary text-uppercase small fw-bold text-end">Total Tagihan</th>
                        <th class="py-3 text-secondary text-uppercase small fw-bold text-end">Terbayar</th>
                        <th class="py-3 text-secondary text-uppercase small fw-bold text-end">Sisa Piutang</th>
                        <th class="py-3 text-secondary text-uppercase small fw-bold text-center">Status</th>
                        <th class="pe-3 py-3 text-secondary text-uppercase small fw-bold text-end">Aksi</th>
                    </tr>
                </thead>
                <tbody class="border-top-0">
                    @forelse($invoices as $inv)
                        <tr>
                            <td class="ps-3 font-monospace fw-bold">
                                <a href="{{ route('finance.invoices.show', $inv) }}" class="text-decoration-none text-dark">
                                    {{ $inv->invoice_number }}
                                </a>
                            </td>
                            <td class="text-muted small">
                                {{ $inv->invoice_date->format('d/m/Y') }}
                            </td>
                            <td>
                                @if($inv->due_date)
                                    <span class="{{ $inv->isOverdue() ? 'text-danger fw-bold' : 'text-secondary small' }}">
                                        {{ $inv->due_date->format('d/m/Y') }}
                                    </span>
                                    @if($inv->isOverdue())
                                        <small class="badge bg-danger d-block mt-1">Telat {{ $inv->days_overdue }} Hari</small>
                                    @endif
                                @else
                                    <span class="text-muted">-</span>
                                @endif
                            </td>
                            <td>
                                <a href="{{ route('orders.show', $inv->order) }}" class="font-monospace small text-primary text-decoration-none">
                                    {{ $inv->order->order_number }}
                                </a>
                            </td>
                            <td class="text-end fw-semibold text-dark">
                                {{ $inv->formatted_total }}
                            </td>
                            <td class="text-end fw-semibold text-success">
                                {{ $inv->formatted_paid }}
                            </td>
                            <td class="text-end fw-bold {{ $inv->remaining_amount > 0 ? 'text-danger' : 'text-success' }}">
                                {{ $inv->formatted_remaining }}
                            </td>
                            <td class="text-center">
                                <span class="badge {{ $inv->status_badge_class }} px-3 py-1 rounded-pill">
                                    {{ $inv->status_label }}
                                </span>
                            </td>
                            <td class="pe-3 text-end">
                                <a href="{{ route('finance.invoices.show', $inv) }}" class="btn btn-sm btn-outline-primary">
                                    <i class="bi bi-eye me-1"></i> Buka Faktur
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="text-center py-4 text-muted">
                                Mitra ini belum memiliki riwayat faktur tagihan.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Section: Riwayat Pembayaran Masuk (Kredit Piutang) -->
    <div class="card card-custom overflow-hidden border-0 shadow-sm">
        <div class="card-header bg-white py-3 border-bottom d-flex justify-content-between align-items-center">
            <h5 class="fw-bold text-dark mb-0">
                <i class="bi bi-cash-stack text-success me-2"></i> Riwayat Pelunasan & Pembayaran Masuk (Kredit)
            </h5>
            <span class="badge bg-light text-secondary border">{{ $payments->count() }} Transaksi</span>
        </div>
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th class="ps-3 py-3 text-secondary text-uppercase small fw-bold">No. Kuitansi</th>
                        <th class="py-3 text-secondary text-uppercase small fw-bold">Tanggal Bayar</th>
                        <th class="py-3 text-secondary text-uppercase small fw-bold">Faktur Terkait</th>
                        <th class="py-3 text-secondary text-uppercase small fw-bold">Metode Pembayaran</th>
                        <th class="py-3 text-secondary text-uppercase small fw-bold">No. Bukti / Referensi</th>
                        <th class="py-3 text-secondary text-uppercase small fw-bold text-end">Jumlah Dibayar</th>
                        <th class="pe-3 py-3 text-secondary text-uppercase small fw-bold text-end">Aksi</th>
                    </tr>
                </thead>
                <tbody class="border-top-0">
                    @forelse($payments as $pay)
                        <tr>
                            <td class="ps-3 font-monospace fw-bold text-success">
                                {{ $pay->payment_number }}
                            </td>
                            <td class="text-muted small">
                                {{ $pay->payment_date->translatedFormat('d F Y') }}
                            </td>
                            <td>
                                @if($pay->invoice)
                                    <a href="{{ route('finance.invoices.show', $pay->invoice) }}" class="font-monospace small text-primary text-decoration-none">
                                        {{ $pay->invoice->invoice_number }}
                                    </a>
                                @else
                                    <span class="text-muted">-</span>
                                @endif
                            </td>
                            <td>
                                <span class="badge bg-light text-dark border">{{ $pay->method_label }}</span>
                            </td>
                            <td class="small font-monospace text-secondary">
                                {{ $pay->reference_number ?? '-' }}
                            </td>
                            <td class="text-end fw-bold text-success fs-6">
                                {{ $pay->formatted_amount }}
                            </td>
                            <td class="pe-3 text-end">
                                <a href="{{ route('finance.payments.receipt', $pay) }}" target="_blank" class="btn btn-sm btn-outline-secondary">
                                    <i class="bi bi-printer me-1"></i> Cetak Kuitansi
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center py-4 text-muted">
                                Belum ada riwayat pelunasan pembayaran yang tercatat untuk mitra ini.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
