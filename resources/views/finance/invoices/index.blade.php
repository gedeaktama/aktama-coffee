@extends('layouts.app')

@section('title', 'Daftar Faktur Penjualan & Piutang - Kopi Distro')

@section('content')
<div class="container py-3">
    <!-- Header Page -->
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-4">
        <div>
            <div class="d-flex align-items-center gap-2">
                <span class="badge bg-info bg-opacity-10 text-info px-3 py-2 rounded-pill fw-semibold">
                    <i class="bi bi-receipt-cutoff me-1"></i> Modul Keuangan & Piutang
                </span>
                <span class="text-muted">&bull;</span>
                <span class="small text-muted">Tahap 5: Pembayaran & Piutang Tempo</span>
            </div>
            <h2 class="fw-bold text-dark mt-2 mb-1">Faktur Penjualan & Tagihan (Invoices)</h2>
            <p class="text-muted mb-0">Kelola penerbitan faktur tagihan penjualan, verifikasi pembayaran, dan pantau jatuh tempo piutang.</p>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('finance.receivables.index') }}" class="btn btn-outline-coffee d-inline-flex align-items-center gap-2">
                <i class="bi bi-credit-card-2-front"></i>
                <span>Monitoring Piutang B2B</span>
            </a>
            <a href="{{ route('orders.index') }}" class="btn btn-coffee d-inline-flex align-items-center gap-2 shadow-sm">
                <i class="bi bi-cart-check"></i>
                <span>Lihat Pesanan Penjualan</span>
            </a>
        </div>
    </div>

    <!-- KPI Metric Cards -->
    <div class="row g-3 mb-4">
        <div class="col-sm-6 col-lg-3">
            <div class="card card-custom p-3 border-start border-primary border-4">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <span class="text-muted small fw-semibold text-uppercase">Total Faktur Diterbitkan</span>
                        <h4 class="fw-bold mb-0 text-dark">Rp {{ number_format($metrics['total_invoiced'], 0, ',', '.') }}</h4>
                    </div>
                    <div class="p-3 rounded-3 bg-primary bg-opacity-10 text-primary">
                        <i class="bi bi-receipt fs-4"></i>
                    </div>
                </div>
                <small class="text-muted mt-2 d-block border-top pt-2">Akumulasi seluruh faktur tagihan</small>
            </div>
        </div>

        <div class="col-sm-6 col-lg-3">
            <div class="card card-custom p-3 border-start border-success border-4">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <span class="text-muted small fw-semibold text-uppercase">Pembayaran Diterima</span>
                        <h4 class="fw-bold mb-0 text-success">Rp {{ number_format($metrics['total_paid'], 0, ',', '.') }}</h4>
                    </div>
                    <div class="p-3 rounded-3 bg-success bg-opacity-10 text-success">
                        <i class="bi bi-cash-stack fs-4"></i>
                    </div>
                </div>
                <small class="text-muted mt-2 d-block border-top pt-2">Kas & transfer bank masuk</small>
            </div>
        </div>

        <div class="col-sm-6 col-lg-3">
            <div class="card card-custom p-3 border-start border-warning border-4">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <span class="text-muted small fw-semibold text-uppercase">Piutang Berjalan (Unpaid)</span>
                        <h4 class="fw-bold mb-0 text-warning">Rp {{ number_format($metrics['total_receivables'], 0, ',', '.') }}</h4>
                    </div>
                    <div class="p-3 rounded-3 bg-warning bg-opacity-10 text-warning">
                        <i class="bi bi-hourglass-split fs-4"></i>
                    </div>
                </div>
                <small class="text-muted mt-2 d-block border-top pt-2">Tagihan tempo belum lunas</small>
            </div>
        </div>

        <div class="col-sm-6 col-lg-3">
            <div class="card card-custom p-3 border-start border-danger border-4">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <span class="text-muted small fw-semibold text-uppercase">Jatuh Tempo (Overdue)</span>
                        <h4 class="fw-bold mb-0 text-danger">{{ $metrics['overdue_count'] }} <small class="fs-6 text-muted">Faktur</small></h4>
                    </div>
                    <div class="p-3 rounded-3 bg-danger bg-opacity-10 text-danger">
                        <i class="bi bi-exclamation-triangle-fill fs-4"></i>
                    </div>
                </div>
                <div class="mt-2 pt-2 border-top d-flex justify-content-between small">
                    <span class="text-muted">Melewati batas tempo</span>
                    <a href="{{ route('finance.invoices.index', ['status' => 'overdue']) }}" class="text-danger fw-semibold text-decoration-none">Filter &rarr;</a>
                </div>
            </div>
        </div>
    </div>

    <!-- Filter Bar -->
    <div class="card card-custom p-3 mb-4">
        <form method="GET" action="{{ route('finance.invoices.index') }}" class="row g-2 align-items-center">
            <div class="col-md-4">
                <div class="input-group">
                    <span class="input-group-text bg-white border-end-0 text-muted"><i class="bi bi-search"></i></span>
                    <input type="text" name="search" class="form-control border-start-0" placeholder="Cari No. Faktur, No. Order, atau Pelanggan..." value="{{ request('search') }}">
                </div>
            </div>
            <div class="col-md-3">
                <select name="status" class="form-select">
                    <option value="">-- Semua Status Faktur --</option>
                    <option value="unpaid" {{ request('status') === 'unpaid' ? 'selected' : '' }}>Belum Dibayar</option>
                    <option value="partial" {{ request('status') === 'partial' ? 'selected' : '' }}>Dibayar Sebagian</option>
                    <option value="paid" {{ request('status') === 'paid' ? 'selected' : '' }}>Lunas</option>
                    <option value="overdue" {{ request('status') === 'overdue' ? 'selected' : '' }}>Melewati Jatuh Tempo (Overdue)</option>
                </select>
            </div>
            <div class="col-md-3">
                <select name="customer_id" class="form-select">
                    <option value="">-- Semua Pelanggan --</option>
                    @foreach($customers as $c)
                        <option value="{{ $c->id }}" {{ request('customer_id') == $c->id ? 'selected' : '' }}>{{ $c->business_name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2 d-flex gap-2">
                <button type="submit" class="btn btn-outline-coffee flex-grow-1">
                    <i class="bi bi-funnel me-1"></i> Filter
                </button>
                @if(request()->hasAny(['search', 'status', 'customer_id']))
                    <a href="{{ route('finance.invoices.index') }}" class="btn btn-outline-secondary" title="Reset">
                        <i class="bi bi-x-circle"></i>
                    </a>
                @endif
            </div>
        </form>
    </div>

    <!-- Invoices Table -->
    <div class="card card-custom overflow-hidden">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th class="ps-3 py-3 text-secondary text-uppercase small fw-bold">No. Faktur</th>
                        <th class="py-3 text-secondary text-uppercase small fw-bold">Tanggal</th>
                        <th class="py-3 text-secondary text-uppercase small fw-bold">No. Order</th>
                        <th class="py-3 text-secondary text-uppercase small fw-bold">Pelanggan Mitra</th>
                        <th class="py-3 text-secondary text-uppercase small fw-bold">Jatuh Tempo</th>
                        <th class="py-3 text-secondary text-uppercase small fw-bold text-end">Total Tagihan</th>
                        <th class="py-3 text-secondary text-uppercase small fw-bold text-end">Sisa Tagihan</th>
                        <th class="py-3 text-secondary text-uppercase small fw-bold text-center">Status</th>
                        <th class="pe-3 py-3 text-secondary text-uppercase small fw-bold text-end">Aksi</th>
                    </tr>
                </thead>
                <tbody class="border-top-0">
                    @forelse($invoices as $inv)
                        <tr>
                            <td class="ps-3 font-monospace fw-bold text-dark">
                                <a href="{{ route('finance.invoices.show', $inv) }}" class="text-decoration-none text-dark">
                                    {{ $inv->invoice_number }}
                                </a>
                            </td>
                            <td class="text-muted small">
                                <i class="bi bi-calendar3 me-1"></i>{{ $inv->invoice_date->format('d/m/Y') }}
                            </td>
                            <td>
                                <span class="font-monospace small text-primary">{{ $inv->order->order_number }}</span>
                            </td>
                            <td>
                                @if($inv->customer)
                                    <div class="fw-semibold text-dark">{{ $inv->customer->business_name }}</div>
                                    <small class="text-muted">{{ $inv->customer->pic_name }} &middot; {{ $inv->customer->phone }}</small>
                                @else
                                    <div class="fw-semibold text-dark">{{ $inv->order->recipient_name ?? '-' }}</div>
                                    <small class="text-muted">{{ $inv->order->recipient_phone ?? '-' }}</small>
                                @endif
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
                            <td class="text-end fw-semibold text-dark">
                                {{ $inv->formatted_total }}
                            </td>
                            <td class="text-end fw-bold {{ $inv->remaining_amount > 0 ? 'text-danger' : 'text-success' }}">
                                {{ $inv->formatted_remaining }}
                            </td>
                            <td class="text-center">
                                <span class="badge {{ $inv->status_badge_class }} px-3 py-2 rounded-pill">
                                    {{ $inv->status_label }}
                                </span>
                            </td>
                            <td class="pe-3 text-end">
                                <a href="{{ route('finance.invoices.show', $inv) }}" class="btn btn-sm btn-outline-primary px-3">
                                    <i class="bi bi-eye me-1"></i> Detail / Bayar
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="text-center py-5 text-muted">
                                <i class="bi bi-receipt-cutoff fs-1 text-secondary opacity-50 mb-3 d-block"></i>
                                <h6 class="fw-semibold text-dark">Belum ada data faktur penjualan</h6>
                                <p class="small text-muted mb-0">Faktur dapat diterbitkan dari halaman detail pesanan penjualan yang telah dikonfirmasi.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($invoices->hasPages())
            <div class="card-footer bg-white border-top py-3 d-flex justify-content-between align-items-center">
                <small class="text-muted">Menampilkan {{ $invoices->firstItem() }} - {{ $invoices->lastItem() }} dari {{ $invoices->total() }} faktur</small>
                <div>{{ $invoices->links() }}</div>
            </div>
        @endif
    </div>
</div>
@endsection
