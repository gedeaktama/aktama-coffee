@extends('layouts.app')

@section('title', 'Daftar Pesanan Penjualan - Kopi Distro')

@section('content')
<div class="container py-3">
    <!-- Header Page -->
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-4">
        <div>
            <div class="d-flex align-items-center gap-2">
                <span class="badge bg-success bg-opacity-10 text-success px-3 py-2 rounded-pill fw-semibold">
                    <i class="bi bi-cart-check me-1"></i> Modul Penjualan & Pesanan
                </span>
                <span class="text-muted">&bull;</span>
                <span class="small text-muted">Tahap 4: Penjualan B2B & Retail</span>
            </div>
            <h2 class="fw-bold text-dark mt-2 mb-1">Daftar Pesanan Penjualan (Sales Orders)</h2>
            <p class="text-muted mb-0">Kelola pesanan grosir B2B untuk mitra kafe/toko/reseller dan pesanan retail online.</p>
        </div>
        <div class="d-flex gap-2">
            @if(auth()->user()->hasRole(['owner', 'admin', 'sales']))
                <a href="{{ route('orders.create') }}" class="btn btn-coffee d-inline-flex align-items-center gap-2 shadow-sm">
                    <i class="bi bi-plus-circle"></i>
                    <span>Input Order B2B Baru</span>
                </a>
            @endif
        </div>
    </div>

    <!-- KPI Metric Cards -->
    <div class="row g-3 mb-4">
        <div class="col-sm-6 col-lg-3">
            <div class="card card-custom p-3 border-start border-primary border-4">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <span class="text-muted small fw-semibold text-uppercase">Total Transaksi</span>
                        <h3 class="fw-bold mb-0 text-dark">{{ number_format($metrics['total_orders']) }} <small class="fs-6 text-muted">Order</small></h3>
                    </div>
                    <div class="p-3 rounded-3 bg-primary bg-opacity-10 text-primary">
                        <i class="bi bi-receipt fs-4"></i>
                    </div>
                </div>
                <small class="text-muted mt-2 d-block border-top pt-2">B2B Grosir & Retail Online</small>
            </div>
        </div>

        <div class="col-sm-6 col-lg-3">
            <div class="card card-custom p-3 border-start border-warning border-4">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <span class="text-muted small fw-semibold text-uppercase">Menunggu Konfirmasi</span>
                        <h3 class="fw-bold mb-0 text-warning">{{ number_format($metrics['pending_count']) }} <small class="fs-6 text-muted">Order</small></h3>
                    </div>
                    <div class="p-3 rounded-3 bg-warning bg-opacity-10 text-warning">
                        <i class="bi bi-clock-history fs-4"></i>
                    </div>
                </div>
                <div class="mt-2 pt-2 border-top d-flex justify-content-between small">
                    <span class="text-muted">Stok belum dipotong</span>
                    <a href="{{ route('orders.index', ['status' => 'pending']) }}" class="text-warning fw-semibold text-decoration-none">Lihat &rarr;</a>
                </div>
            </div>
        </div>

        <div class="col-sm-6 col-lg-3">
            <div class="card card-custom p-3 border-start border-info border-4">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <span class="text-muted small fw-semibold text-uppercase">Dikonfirmasi (Aktif)</span>
                        <h3 class="fw-bold mb-0 text-info">{{ number_format($metrics['confirmed_count']) }} <small class="fs-6 text-muted">Order</small></h3>
                    </div>
                    <div class="p-3 rounded-3 bg-info bg-opacity-10 text-info">
                        <i class="bi bi-box-seam fs-4"></i>
                    </div>
                </div>
                <div class="mt-2 pt-2 border-top d-flex justify-content-between small">
                    <span class="text-muted">Stok telah terpotong</span>
                    <a href="{{ route('orders.index', ['status' => 'confirmed']) }}" class="text-info fw-semibold text-decoration-none">Lihat &rarr;</a>
                </div>
            </div>
        </div>

        <div class="col-sm-6 col-lg-3">
            <div class="card card-custom p-3 border-start border-success border-4">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <span class="text-muted small fw-semibold text-uppercase">Nilai Penjualan Aktif</span>
                        <h3 class="fw-bold mb-0 text-success">Rp {{ number_format($metrics['total_sales'], 0, ',', '.') }}</h3>
                    </div>
                    <div class="p-3 rounded-3 bg-success bg-opacity-10 text-success">
                        <i class="bi bi-currency-dollar fs-4"></i>
                    </div>
                </div>
                <small class="text-muted mt-2 d-block border-top pt-2">Akumulasi pesanan non-batal</small>
            </div>
        </div>
    </div>

    <!-- Filter & Search Bar -->
    <div class="card card-custom p-3 mb-4">
        <form method="GET" action="{{ route('orders.index') }}" class="row g-2 align-items-center">
            <div class="col-md-3">
                <div class="input-group">
                    <span class="input-group-text bg-white border-end-0 text-muted"><i class="bi bi-search"></i></span>
                    <input type="text" name="search" class="form-control border-start-0" placeholder="No. Order / Pelanggan..." value="{{ request('search') }}">
                </div>
            </div>
            <div class="col-md-2">
                <select name="order_type" class="form-select">
                    <option value="">-- Semua Saluran --</option>
                    <option value="b2b" {{ request('order_type') === 'b2b' ? 'selected' : '' }}>Grosir B2B (Mitra)</option>
                    <option value="retail" {{ request('order_type') === 'retail' ? 'selected' : '' }}>Retail Online (B2C)</option>
                </select>
            </div>
            <div class="col-md-2">
                <select name="status" class="form-select">
                    <option value="">-- Semua Status --</option>
                    @foreach(\App\Models\Order::STATUSES as $stKey => $stLbl)
                        <option value="{{ $stKey }}" {{ request('status') === $stKey ? 'selected' : '' }}>{{ $stLbl }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2">
                <select name="payment_type" class="form-select">
                    <option value="">-- Pembayaran --</option>
                    <option value="cash" {{ request('payment_type') === 'cash' ? 'selected' : '' }}>Tunai (Cash)</option>
                    <option value="transfer" {{ request('payment_type') === 'transfer' ? 'selected' : '' }}>Transfer Bank</option>
                    <option value="credit" {{ request('payment_type') === 'credit' ? 'selected' : '' }}>Tempo / Kredit</option>
                </select>
            </div>
            <div class="col-md-3 d-flex gap-2">
                <button type="submit" class="btn btn-outline-coffee flex-grow-1">
                    <i class="bi bi-funnel me-1"></i> Filter
                </button>
                @if(request()->hasAny(['search', 'order_type', 'status', 'payment_type']))
                    <a href="{{ route('orders.index') }}" class="btn btn-outline-secondary" title="Reset Filter">
                        <i class="bi bi-x-circle"></i>
                    </a>
                @endif
            </div>
        </form>
    </div>

    <!-- Orders Table -->
    <div class="card card-custom overflow-hidden">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th class="ps-3 py-3 text-secondary text-uppercase small fw-bold">No. Order</th>
                        <th class="py-3 text-secondary text-uppercase small fw-bold">Tanggal</th>
                        <th class="py-3 text-secondary text-uppercase small fw-bold text-center">Saluran</th>
                        <th class="py-3 text-secondary text-uppercase small fw-bold">Pelanggan / Penerima</th>
                        <th class="py-3 text-secondary text-uppercase small fw-bold">Metode Bayar & Tempo</th>
                        <th class="py-3 text-secondary text-uppercase small fw-bold text-end">Total Nilai</th>
                        <th class="py-3 text-secondary text-uppercase small fw-bold text-center">Status Order</th>
                        <th class="pe-3 py-3 text-secondary text-uppercase small fw-bold text-end">Aksi</th>
                    </tr>
                </thead>
                <tbody class="border-top-0">
                    @forelse($orders as $ord)
                        <tr>
                            <td class="ps-3">
                                <a href="{{ route('orders.show', $ord) }}" class="font-monospace fw-bold text-dark text-decoration-none">
                                    {{ $ord->order_number }}
                                </a>
                                @if($ord->stock_reduced)
                                    <small class="d-block text-success" title="Stok gudang telah terpotong"><i class="bi bi-check-all"></i> Stok Terpotong</small>
                                @endif
                            </td>
                            <td class="text-muted small">
                                <i class="bi bi-calendar3 me-1"></i>{{ $ord->order_date->format('d/m/Y') }}
                            </td>
                            <td class="text-center">
                                @if($ord->isB2B())
                                    <span class="badge bg-primary bg-opacity-10 text-primary border border-primary border-opacity-25 px-2 py-1">
                                        <i class="bi bi-building me-1"></i> B2B Grosir
                                    </span>
                                @else
                                    <span class="badge bg-info bg-opacity-10 text-info border border-info border-opacity-25 px-2 py-1">
                                        <i class="bi bi-cart3 me-1"></i> Retail
                                    </span>
                                @endif
                            </td>
                            <td>
                                @if($ord->customer)
                                    <div class="fw-semibold text-dark">{{ $ord->customer->business_name }}</div>
                                    <small class="text-muted">PIC: {{ $ord->customer->pic_name }} &middot; {{ $ord->customer->phone }}</small>
                                @else
                                    <div class="fw-semibold text-dark">{{ $ord->recipient_name ?? '-' }}</div>
                                    <small class="text-muted">{{ $ord->recipient_phone ?? '-' }}</small>
                                @endif
                            </td>
                            <td>
                                <span class="badge bg-light text-dark border">
                                    {{ $ord->payment_type_label }}
                                </span>
                                @if($ord->payment_type === 'credit' && $ord->due_date)
                                    <small class="d-block text-danger mt-1">
                                        <i class="bi bi-clock me-1"></i>Tempo: {{ $ord->due_date->format('d/m/Y') }}
                                    </small>
                                @endif
                            </td>
                            <td class="text-end fw-bold text-dark">
                                {{ $ord->formatted_total }}
                            </td>
                            <td class="text-center">
                                <span class="badge {{ $ord->status_badge_class }} px-3 py-2 rounded-pill">
                                    {{ $ord->status_label }}
                                </span>
                            </td>
                            <td class="pe-3 text-end">
                                <a href="{{ route('orders.show', $ord) }}" class="btn btn-sm btn-outline-primary px-3">
                                    <i class="bi bi-eye me-1"></i> Detail
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="text-center py-5 text-muted">
                                <i class="bi bi-receipt fs-1 text-secondary opacity-50 mb-3 d-block"></i>
                                <h6 class="fw-semibold text-dark">Belum ada pesanan penjualan</h6>
                                <p class="small text-muted mb-3">Pesanan penjualan baru dari sales atau checkout retail akan tampil di sini.</p>
                                @if(auth()->user()->hasRole(['owner', 'admin', 'sales']))
                                    <a href="{{ route('orders.create') }}" class="btn btn-sm btn-coffee">
                                        <i class="bi bi-plus-circle me-1"></i> Buat Pesanan B2B Sekarang
                                    </a>
                                @endif
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($orders->hasPages())
            <div class="card-footer bg-white border-top py-3 d-flex justify-content-between align-items-center">
                <small class="text-muted">Menampilkan {{ $orders->firstItem() }} - {{ $orders->lastItem() }} dari {{ $orders->total() }} pesanan penjualan</small>
                <div>{{ $orders->links() }}</div>
            </div>
        @endif
    </div>
</div>
@endsection
