@extends('layouts.app')

@section('title', 'Dashboard ERP - Kopi Distro')

@push('styles')
<style>
    .kpi-card {
        background: #ffffff;
        border: 1px solid #e2e8f0;
        border-radius: 14px;
        padding: 1.25rem 1.5rem;
        transition: all 0.25s ease;
        position: relative;
        overflow: hidden;
    }
    .kpi-card:hover {
        transform: translateY(-3px);
        box-shadow: 0 10px 24px rgba(0, 0, 0, 0.05);
        border-color: #cbd5e1;
    }
    .kpi-icon-wrap {
        width: 52px;
        height: 52px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.4rem;
        flex-shrink: 0;
    }
    .action-quick-btn {
        display: flex;
        align-items: center;
        gap: 0.75rem;
        padding: 0.9rem 1.25rem;
        background: #ffffff;
        border: 1px solid #e2e8f0;
        border-radius: 12px;
        text-decoration: none;
        color: #1e293b;
        font-weight: 600;
        font-size: 0.9rem;
        transition: all 0.2s ease;
    }
    .action-quick-btn:hover {
        background: #f8fafc;
        border-color: #f59e0b;
        color: #b45309;
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(245, 158, 11, 0.15);
    }
    .action-quick-btn i {
        font-size: 1.25rem;
    }

    @media (max-width: 768px) {
        .kpi-card {
            padding: 1rem !important;
        }
        .kpi-icon-wrap {
            width: 42px !important;
            height: 42px !important;
            font-size: 1.15rem !important;
        }
        .action-quick-btn {
            padding: 0.65rem 0.75rem !important;
            font-size: 0.8rem !important;
            gap: 0.45rem !important;
        }
        .action-quick-btn i {
            font-size: 1.05rem !important;
        }
    }
</style>
@endpush

@section('content')
<div class="py-2">

    <!-- 1. EXECUTIVE WELCOME BANNER -->
    <div class="card p-4 mb-4 border-0 text-white shadow-sm" style="background: linear-gradient(135deg, #18110c 0%, #2b170e 55%, #422214 100%); border-radius: 16px;">
        <div class="row align-items-center gy-3">
            <div class="col-lg-8">
                <div class="d-flex align-items-center gap-2 mb-2">
                    <span class="badge badge-role badge-role-{{ $user->role }} px-3 py-1">
                        HAK AKSES: {{ strtoupper($user->role) }}
                    </span>
                    <span class="badge bg-success bg-opacity-75">
                        <i class="bi bi-shield-check me-1"></i> Sistem ERP Beroperasi Normal
                    </span>
                </div>
                <h2 class="fw-bold text-white mb-2">Selamat Datang, {{ $user->name }}!</h2>
                <p class="text-white-50 mb-0" style="max-width: 720px; line-height: 1.55;">
                    @if($user->isOwner())
                        Anda memiliki <strong>akses eksekutif penuh</strong> atas seluruh konfigurasi bisnis, plafon kredit pelanggan B2B, audit persediaan gudang, serta analisis laba kotor &amp; HPP.
                    @elseif($user->isAdmin())
                        Anda mengelola master data produk &amp; SKU gramasi, mitra pemasok, pelanggan B2B, dan seluruh transaksi penjualan harian.
                    @elseif($user->isSales())
                        Anda mengelola kemitraan pelanggan grosir binaan, memantau sisa plafon kredit, dan menerbitkan Pesanan Grosir B2B baru.
                    @elseif($user->isWarehouse())
                        Anda bertanggung jawab atas penerimaan PO bahan baku supplier, mutasi stok fisik, opname audit, dan penerbitan Surat Jalan pengiriman.
                    @elseif($user->isFinance())
                        Anda mengelola penerbitan faktur penjualan, verifikasi pembayaran bertahap/lunas, pemulihan otomatis plafon kredit, dan penagihan piutang tempo.
                    @else
                        Anda terhubung ke sistem terpadu operasional Kopi Distro.
                    @endif
                </p>
            </div>
            <div class="col-lg-4 text-lg-end">
                <div class="d-inline-block text-start p-3 rounded-3 bg-white bg-opacity-10 border border-white border-opacity-20">
                    <small class="text-white-50 d-block">Sesi Waktu Saat Ini</small>
                    <span class="fw-bold text-white fs-6">{{ now()->translatedFormat('l, d F Y') }}</span>
                    <small class="text-white-50 d-block mt-1">Status Database: <span class="text-success fw-bold">Sinkron &amp; Terpusat</span></small>
                </div>
            </div>
        </div>
    </div>

    <!-- 2. EXECUTIVE KPI METRICS (FINANCIAL & OPERATIONAL) -->
    <div class="row g-3 mb-4">
        <!-- KPI 1: Total Omset Penjualan -->
        <div class="col-sm-6 col-xl-3">
            <div class="kpi-card h-100">
                <div class="d-flex align-items-center justify-content-between mb-2">
                    <span class="text-muted small fw-bold text-uppercase" style="letter-spacing: 0.5px;">Total Omset Penjualan</span>
                    <div class="kpi-icon-wrap bg-success bg-opacity-10 text-success">
                        <i class="bi bi-cash-stack"></i>
                    </div>
                </div>
                <div class="fw-bold text-dark fs-4 mb-1">
                    Rp {{ number_format($masterCounts['total_sales_amount'], 0, ',', '.') }}
                </div>
                <div class="d-flex align-items-center justify-content-between pt-2 border-top small text-muted">
                    <span>Dari {{ $masterCounts['total_orders'] }} transaksi</span>
                    <a href="{{ route('reports.sales') }}" class="text-success fw-semibold text-decoration-none">Laporan &rarr;</a>
                </div>
            </div>
        </div>

        <!-- KPI 2: Piutang Dagang B2B Aktif -->
        <div class="col-sm-6 col-xl-3">
            <div class="kpi-card h-100">
                <div class="d-flex align-items-center justify-content-between mb-2">
                    <span class="text-muted small fw-bold text-uppercase" style="letter-spacing: 0.5px;">Piutang Dagang B2B</span>
                    <div class="kpi-icon-wrap bg-warning bg-opacity-15 text-warning">
                        <i class="bi bi-wallet2"></i>
                    </div>
                </div>
                <div class="fw-bold text-dark fs-4 mb-1">
                    Rp {{ number_format($masterCounts['active_receivables'], 0, ',', '.') }}
                </div>
                <div class="d-flex align-items-center justify-content-between pt-2 border-top small text-muted">
                    <span>{{ $masterCounts['overdue_invoices'] }} faktur jatuh tempo</span>
                    <a href="{{ route('finance.receivables.index') }}" class="text-warning text-dark fw-semibold text-decoration-none">Monitor &rarr;</a>
                </div>
            </div>
        </div>

        <!-- KPI 3: Pesanan Penjualan -->
        <div class="col-sm-6 col-xl-3">
            <div class="kpi-card h-100">
                <div class="d-flex align-items-center justify-content-between mb-2">
                    <span class="text-muted small fw-bold text-uppercase" style="letter-spacing: 0.5px;">Pesanan Penjualan</span>
                    <div class="kpi-icon-wrap bg-primary bg-opacity-10 text-primary">
                        <i class="bi bi-receipt"></i>
                    </div>
                </div>
                <div class="fw-bold text-dark fs-4 mb-1">
                    {{ $masterCounts['total_orders'] }} <small class="fs-6 text-muted font-normal">Order</small>
                </div>
                <div class="d-flex align-items-center justify-content-between pt-2 border-top small text-muted">
                    <span>{{ $masterCounts['pending_orders'] }} pending konfirmasi</span>
                    <a href="{{ route('orders.index') }}" class="text-primary fw-semibold text-decoration-none">Semua &rarr;</a>
                </div>
            </div>
        </div>

        <!-- KPI 4: Persediaan Fisik Gudang -->
        <div class="col-sm-6 col-xl-3">
            <div class="kpi-card h-100">
                <div class="d-flex align-items-center justify-content-between mb-2">
                    <span class="text-muted small fw-bold text-uppercase" style="letter-spacing: 0.5px;">Persediaan Gudang</span>
                    <div class="kpi-icon-wrap bg-info bg-opacity-10 text-info">
                        <i class="bi bi-boxes"></i>
                    </div>
                </div>
                <div class="fw-bold text-dark fs-4 mb-1">
                    {{ number_format($masterCounts['total_stock'], 0, ',', '.') }} <small class="fs-6 text-muted">Pack</small>
                </div>
                <div class="d-flex align-items-center justify-content-between pt-2 border-top small text-muted">
                    <span>{{ $masterCounts['variants'] }} SKU ({{ $masterCounts['low_stock'] }} menipis)</span>
                    <a href="{{ route('inventory.index') }}" class="text-info fw-semibold text-decoration-none">Cek Stok &rarr;</a>
                </div>
            </div>
        </div>
    </div>

    <!-- 3. SHORTCUT AKSI CEPAT BISNIS -->
    <div class="card p-3 mb-4 bg-white border border-light-subtle rounded-4 shadow-sm">
        <div class="d-flex align-items-center justify-content-between mb-2 px-1">
            <small class="fw-bold text-uppercase text-secondary" style="font-size: 0.72rem; letter-spacing: 0.6px;">
                <i class="bi bi-lightning-charge-fill text-warning me-1"></i> Aksi Cepat Operasional Bisnis
            </small>
        </div>
        <div class="row g-2">
            @if($user->hasRole(['owner', 'admin', 'sales']))
                <div class="col-sm-6 col-md-4 col-xl-2">
                    <a href="{{ route('orders.create') }}" class="action-quick-btn">
                        <i class="bi bi-plus-circle text-success"></i>
                        <span>Input Order B2B</span>
                    </a>
                </div>
            @endif

            @if($user->hasRole(['owner', 'admin', 'warehouse']))
                <div class="col-sm-6 col-md-4 col-xl-2">
                    <a href="{{ route('purchases.create') }}" class="action-quick-btn">
                        <i class="bi bi-cart-plus text-info"></i>
                        <span>Buat PO Supplier</span>
                    </a>
                </div>
                <div class="col-sm-6 col-md-4 col-xl-2">
                    <a href="{{ route('delivery.index') }}" class="action-quick-btn">
                        <i class="bi bi-truck text-warning"></i>
                        <span>Surat Jalan</span>
                    </a>
                </div>
            @endif

            @if($user->hasRole(['owner', 'admin', 'sales', 'finance']))
                <div class="col-sm-6 col-md-4 col-xl-2">
                    <a href="{{ route('finance.invoices.index') }}" class="action-quick-btn">
                        <i class="bi bi-file-earmark-text text-primary"></i>
                        <span>Faktur Invoices</span>
                    </a>
                </div>
                <div class="col-sm-6 col-md-4 col-xl-2">
                    <a href="{{ route('finance.receivables.index') }}" class="action-quick-btn">
                        <i class="bi bi-hourglass-split text-danger"></i>
                        <span>Kontrol Piutang</span>
                    </a>
                </div>
            @endif

            @if($user->hasRole(['owner', 'admin', 'finance']))
                <div class="col-sm-6 col-md-4 col-xl-2">
                    <a href="{{ route('reports.profit') }}" class="action-quick-btn">
                        <i class="bi bi-graph-up-arrow text-success"></i>
                        <span>Laba Kotor &amp; HPP</span>
                    </a>
                </div>
            @endif
        </div>
    </div>

    <!-- 4. DUA PANEL UTAMA: RECENT ORDERS & CRITICAL ALERTS -->
    <div class="row g-4">
        <!-- Panel Kiri: Pesanan Penjualan Terbaru -->
        <div class="col-lg-8">
            <div class="card p-4 h-100 bg-white border border-light-subtle rounded-4 shadow-sm">
                <div class="d-flex align-items-center justify-content-between mb-3 pb-2 border-bottom">
                    <div>
                        <h5 class="fw-bold text-dark mb-0">Pesanan Penjualan Terbaru</h5>
                        <small class="text-muted">Aktivitas pesanan grosir B2B dan transaksi retail online</small>
                    </div>
                    <a href="{{ route('orders.index') }}" class="btn btn-outline-secondary btn-sm">
                        Lihat Semua &rarr;
                    </a>
                </div>

                @if($recentOrders->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0 text-nowrap">
                            <thead class="table-light small">
                                <tr>
                                    <th>No. Pesanan</th>
                                    <th>Pelanggan</th>
                                    <th>Kanal</th>
                                    <th class="text-end">Total Nilai</th>
                                    <th class="text-center">Status</th>
                                    <th class="text-end">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($recentOrders as $ord)
                                    <tr>
                                        <td>
                                            <a href="{{ route('orders.show', $ord) }}" class="fw-bold font-monospace text-decoration-none text-dark">
                                                {{ $ord->order_number }}
                                            </a>
                                            <small class="d-block text-muted">{{ $ord->order_date->format('d/m/Y') }}</small>
                                        </td>
                                        <td>
                                            <span class="fw-semibold text-dark">{{ $ord->customer->name ?? 'Pelanggan Walk-in' }}</span>
                                            <small class="d-block text-muted">{{ $ord->customer->phone ?? '-' }}</small>
                                        </td>
                                        <td>
                                            @if($ord->order_type === 'b2b')
                                                <span class="badge bg-primary bg-opacity-10 text-primary">Grosir B2B</span>
                                            @else
                                                <span class="badge bg-warning bg-opacity-20 text-dark">Retail B2C</span>
                                            @endif
                                        </td>
                                        <td class="text-end fw-bold text-dark">
                                            Rp {{ number_format($ord->total_amount, 0, ',', '.') }}
                                        </td>
                                        <td class="text-center">
                                            <span class="badge bg-{{ $ord->status_badge }}">
                                                {{ $ord->status_label }}
                                            </span>
                                        </td>
                                        <td class="text-end">
                                            <a href="{{ route('orders.show', $ord) }}" class="btn btn-sm btn-light border py-1 px-2" title="Lihat Detail">
                                                <i class="bi bi-eye"></i>
                                            </a>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <div class="py-4 text-center text-muted">
                        <i class="bi bi-receipt fs-1 text-secondary opacity-50 mb-2 d-block"></i>
                        <p class="mb-0">Belum ada transaksi pesanan yang dicatat.</p>
                    </div>
                @endif
            </div>
        </div>

        <!-- Panel Kanan: Alert Stok Rendah & Piutang Overdue -->
        <div class="col-lg-4">
            <div class="d-flex flex-column gap-4 h-100">
                <!-- Widget Peringatan Stok Rendah -->
                <div class="card p-3 bg-white border border-light-subtle rounded-4 shadow-sm flex-grow-1">
                    <div class="d-flex align-items-center justify-content-between mb-3 pb-2 border-bottom">
                        <h6 class="fw-bold text-dark mb-0">
                            <i class="bi bi-exclamation-triangle text-warning me-1"></i> Alert Stok Kritis
                        </h6>
                        <a href="{{ route('inventory.index') }}" class="small text-decoration-none">Gudang &rarr;</a>
                    </div>

                    @if($lowStockVariants->count() > 0)
                        <div class="d-flex flex-column gap-2">
                            @foreach($lowStockVariants as $lsv)
                                <div class="p-2 rounded-3 bg-light border d-flex justify-content-between align-items-center">
                                    <div class="overflow-hidden">
                                        <div class="fw-semibold text-dark text-truncate small">{{ $lsv->variant_name }}</div>
                                        <small class="text-muted font-monospace">{{ $lsv->sku }}</small>
                                    </div>
                                    <div class="text-end flex-shrink-0 ms-2">
                                        <span class="badge bg-danger">
                                            {{ $lsv->current_stock }} pack
                                        </span>
                                        <small class="d-block text-muted" style="font-size: 0.68rem;">Min: {{ $lsv->minimum_stock }}</small>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="py-3 text-center text-muted small">
                            <i class="bi bi-check-circle text-success fs-3 d-block mb-1"></i>
                            Seluruh persediaan stok berada di atas batas aman.
                        </div>
                    @endif
                </div>

                <!-- Widget Piutang Overdue -->
                <div class="card p-3 bg-white border border-light-subtle rounded-4 shadow-sm flex-grow-1">
                    <div class="d-flex align-items-center justify-content-between mb-3 pb-2 border-bottom">
                        <h6 class="fw-bold text-dark mb-0">
                            <i class="bi bi-clock-history text-danger me-1"></i> Tagihan Overdue
                        </h6>
                        <a href="{{ route('reports.receivables') }}" class="small text-decoration-none">Aging &rarr;</a>
                    </div>

                    @if($overdueInvoicesList->count() > 0)
                        <div class="d-flex flex-column gap-2">
                            @foreach($overdueInvoicesList as $inv)
                                <div class="p-2 rounded-3 bg-danger bg-opacity-10 border border-danger border-opacity-25 d-flex justify-content-between align-items-center">
                                    <div class="overflow-hidden">
                                        <a href="{{ route('finance.invoices.show', $inv) }}" class="fw-bold text-danger text-truncate small font-monospace d-block text-decoration-none">
                                            {{ $inv->invoice_number }}
                                        </a>
                                        <small class="text-dark">{{ $inv->order->customer->name ?? 'Mitra B2B' }}</small>
                                    </div>
                                    <div class="text-end flex-shrink-0 ms-2">
                                        <div class="fw-bold text-danger small">Rp {{ number_format($inv->remaining_amount, 0, ',', '.') }}</div>
                                        <small class="text-muted" style="font-size: 0.68rem;">Tempo: {{ $inv->due_date->format('d/m/Y') }}</small>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="py-3 text-center text-muted small">
                            <i class="bi bi-shield-check text-success fs-3 d-block mb-1"></i>
                            Tidak ada faktur piutang yang menunggak tempo saat ini.
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

</div>
@endsection
