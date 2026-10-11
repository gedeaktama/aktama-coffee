@extends('layouts.app')

@section('title', 'Monitoring Piutang Mitra B2B - Kopi Distro')

@section('content')
<div class="container py-3">
    <!-- Header Page -->
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-4">
        <div>
            <div class="d-flex align-items-center gap-2">
                <span class="badge bg-danger bg-opacity-10 text-danger px-3 py-2 rounded-pill fw-semibold">
                    <i class="bi bi-graph-up-arrow me-1"></i> Modul Manajemen Piutang
                </span>
                <span class="text-muted">&bull;</span>
                <span class="small text-muted">Tahap 5: Kontrol Kredit & Tempo</span>
            </div>
            <h2 class="fw-bold text-dark mt-2 mb-1">Monitoring Piutang Pelanggan B2B</h2>
            <p class="text-muted mb-0">Pantau utilisasi plafon kredit grosir, saldo piutang berjalan, dan deteksi tagihan yang melewati jatuh tempo.</p>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('finance.invoices.index') }}" class="btn btn-outline-coffee d-inline-flex align-items-center gap-2">
                <i class="bi bi-file-earmark-text"></i>
                <span>Faktur Penjualan</span>
            </a>
            <a href="{{ route('customers.index') }}" class="btn btn-coffee d-inline-flex align-items-center gap-2 shadow-sm">
                <i class="bi bi-people"></i>
                <span>Master Pelanggan</span>
            </a>
        </div>
    </div>

    <!-- KPI Metric Cards -->
    <div class="row g-3 mb-4">
        <div class="col-sm-6 col-lg-4">
            <div class="card card-custom p-3 border-start border-primary border-4">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <span class="text-muted small fw-semibold text-uppercase">Total Plafon Kredit Diberikan</span>
                        <h4 class="fw-bold mb-0 text-dark">Rp {{ number_format($metrics['total_credit_granted'], 0, ',', '.') }}</h4>
                    </div>
                    <div class="p-3 rounded-3 bg-primary bg-opacity-10 text-primary">
                        <i class="bi bi-shield-check fs-4"></i>
                    </div>
                </div>
                <small class="text-muted mt-2 d-block border-top pt-2">Batas kredit akumulasi seluruh mitra B2B</small>
            </div>
        </div>

        <div class="col-sm-6 col-lg-4">
            <div class="card card-custom p-3 border-start border-warning border-4">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <span class="text-muted small fw-semibold text-uppercase">Total Piutang Berjalan</span>
                        <h4 class="fw-bold mb-0 text-warning">Rp {{ number_format($metrics['total_active_receivables'], 0, ',', '.') }}</h4>
                    </div>
                    <div class="p-3 rounded-3 bg-warning bg-opacity-10 text-warning">
                        <i class="bi bi-hourglass-split fs-4"></i>
                    </div>
                </div>
                <small class="text-muted mt-2 d-block border-top pt-2">Tagihan tempo yang belum dilunasi</small>
            </div>
        </div>

        <div class="col-sm-6 col-lg-4">
            <div class="card card-custom p-3 border-start {{ $metrics['total_overdue_mitra'] > 0 ? 'border-danger' : 'border-success' }} border-4">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <span class="text-muted small fw-semibold text-uppercase">Mitra Tagihan Overdue</span>
                        <h4 class="fw-bold mb-0 {{ $metrics['total_overdue_mitra'] > 0 ? 'text-danger' : 'text-success' }}">
                            {{ $metrics['total_overdue_mitra'] }} Mitra
                        </h4>
                    </div>
                    <div class="p-3 rounded-3 {{ $metrics['total_overdue_mitra'] > 0 ? 'bg-danger bg-opacity-10 text-danger' : 'bg-success bg-opacity-10 text-success' }}">
                        <i class="bi {{ $metrics['total_overdue_mitra'] > 0 ? 'bi-exclamation-triangle' : 'bi-check2-circle' }} fs-4"></i>
                    </div>
                </div>
                <small class="text-muted mt-2 d-block border-top pt-2">
                    {{ $metrics['total_overdue_mitra'] > 0 ? 'Wajib tindak lanjut penagihan segera' : 'Seluruh pembayaran piutang tertib & lancar' }}
                </small>
            </div>
        </div>
    </div>

    <!-- Filter & Search Toolbar -->
    <div class="card card-custom p-3 mb-4">
        <form method="GET" action="{{ route('finance.receivables.index') }}" class="row g-2 align-items-center">
            <!-- Filter Tabs -->
            <div class="col-md-6 d-flex gap-1 flex-wrap">
                <a href="{{ route('finance.receivables.index') }}" 
                   class="btn btn-sm {{ !request('filter') ? 'btn-secondary text-white' : 'btn-outline-secondary' }}">
                    Semua Mitra
                </a>
                <a href="{{ route('finance.receivables.index', array_merge(request()->except('page'), ['filter' => 'has_debt'])) }}" 
                   class="btn btn-sm {{ request('filter') === 'has_debt' ? 'btn-warning text-dark' : 'btn-outline-warning' }}">
                    <i class="bi bi-clock-history me-1"></i> Ada Piutang ({{ $customers->where('receivables', '>', 0)->count() }})
                </a>
                <a href="{{ route('finance.receivables.index', array_merge(request()->except('page'), ['filter' => 'overdue'])) }}" 
                   class="btn btn-sm {{ request('filter') === 'overdue' ? 'btn-danger' : 'btn-outline-danger' }}">
                    <i class="bi bi-exclamation-octagon me-1"></i> Jatuh Tempo ({{ $customers->where('overdue_count', '>', 0)->count() }})
                </a>
            </div>

            <!-- Search Input -->
            <div class="col-md-6">
                <div class="input-group">
                    <span class="input-group-text bg-white"><i class="bi bi-search text-muted"></i></span>
                    <input type="text" name="search" class="form-control" placeholder="Cari nama bisnis, PIC, atau kode pelanggan..." value="{{ request('search') }}">
                    <button class="btn btn-coffee" type="submit">Cari</button>
                    @if(request()->anyFilled(['search', 'filter']))
                        <a href="{{ route('finance.receivables.index') }}" class="btn btn-outline-secondary">Reset</a>
                    @endif
                </div>
            </div>
        </form>
    </div>

    <!-- Main Table -->
    <div class="card card-custom overflow-hidden border-0 shadow-sm">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th class="ps-3 py-3 text-secondary text-uppercase small fw-bold">Mitra B2B</th>
                        <th class="py-3 text-secondary text-uppercase small fw-bold">Kontak PIC</th>
                        <th class="py-3 text-secondary text-uppercase small fw-bold text-end">Plafon Kredit</th>
                        <th class="py-3 text-secondary text-uppercase small fw-bold text-end">Piutang Berjalan</th>
                        <th class="py-3 text-secondary text-uppercase small fw-bold text-end">Sisa Plafon</th>
                        <th class="py-3 text-secondary text-uppercase small fw-bold text-center">Utilisasi & Status</th>
                        <th class="pe-3 py-3 text-secondary text-uppercase small fw-bold text-end">Aksi</th>
                    </tr>
                </thead>
                <tbody class="border-top-0">
                    @forelse($customers as $row)
                        @php
                            $c = $row['customer'];
                            $limit = $row['credit_limit'];
                            $debt = $row['receivables'];
                            $avail = $row['available_credit'];
                            $overdue = $row['overdue_count'];
                            $utilizationPercent = $limit > 0 ? min(100, round(($debt / $limit) * 100)) : 0;
                        @endphp
                        <tr>
                            <td class="ps-3">
                                <div class="fw-bold text-dark">{{ $c->business_name }}</div>
                                <div class="d-flex align-items-center gap-1 mt-1">
                                    <span class="badge bg-light text-secondary border font-monospace small">{{ $c->code }}</span>
                                    <span class="badge bg-coffee bg-opacity-10 text-dark small">{{ $c->type_label }}</span>
                                    @if($c->payment_terms_days > 0)
                                        <span class="badge bg-info bg-opacity-10 text-info small">TOP {{ $c->payment_terms_days }} Hari</span>
                                    @endif
                                </div>
                            </td>
                            <td>
                                <div class="fw-semibold text-dark">{{ $c->pic_name }}</div>
                                <small class="text-muted">
                                    <i class="bi bi-telephone me-1"></i>{{ $c->phone }}
                                </small>
                            </td>
                            <td class="text-end fw-semibold text-secondary">
                                Rp {{ number_format($limit, 0, ',', '.') }}
                            </td>
                            <td class="text-end fw-bold {{ $debt > 0 ? 'text-danger' : 'text-muted' }}">
                                Rp {{ number_format($debt, 0, ',', '.') }}
                            </td>
                            <td class="text-end fw-bold {{ $avail > 0 ? 'text-success' : 'text-danger' }}">
                                Rp {{ number_format($avail, 0, ',', '.') }}
                            </td>
                            <td class="text-center" style="min-width: 140px;">
                                @if($limit > 0)
                                    <div class="progress mb-1" style="height: 6px;">
                                        <div class="progress-bar {{ $utilizationPercent > 80 ? 'bg-danger' : ($utilizationPercent > 50 ? 'bg-warning' : 'bg-success') }}" 
                                             role="progressbar" 
                                             style="width: {{ $utilizationPercent }}%;" 
                                             aria-valuenow="{{ $utilizationPercent }}" 
                                             aria-valuemin="0" 
                                             aria-valuemax="100"></div>
                                    </div>
                                    <small class="text-muted d-block" style="font-size: 0.72rem;">{{ $utilizationPercent }}% Terpakai</small>
                                @endif

                                @if($overdue > 0)
                                    <span class="badge bg-danger px-2 py-1 small mt-1">
                                        <i class="bi bi-exclamation-triangle-fill me-1"></i> {{ $overdue }} Overdue
                                    </span>
                                @elseif($debt > 0)
                                    <span class="badge bg-warning text-dark px-2 py-1 small mt-1">Tempo Berjalan</span>
                                @else
                                    <span class="badge bg-success bg-opacity-10 text-success px-2 py-1 small mt-1">Lunas / Nihil</span>
                                @endif
                            </td>
                            <td class="pe-3 text-end">
                                <a href="{{ route('finance.receivables.ledger', $c) }}" class="btn btn-sm btn-outline-coffee px-3" title="Buka Kartu Piutang">
                                    <i class="bi bi-journal-text me-1"></i> Kartu Piutang
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center py-5 text-muted">
                                <i class="bi bi-credit-card-2-front fs-1 text-secondary opacity-50 mb-3 d-block"></i>
                                <h6 class="fw-semibold text-dark">Tidak ada data piutang mitra B2B</h6>
                                <p class="small text-muted mb-0">Semua mitra bisnis telah melunasi tagihannya atau belum memiliki fasilitas plafon kredit.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
