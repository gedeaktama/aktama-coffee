@extends('layouts.app')

@section('title', 'Laporan Rekapitulasi Umur Piutang (AR Aging) - Kopi Distro')

@section('content')
<div class="container py-3">
    <!-- Header Page -->
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-4">
        <div>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-1">
                    <li class="breadcrumb-item"><a href="{{ route('reports.index') }}" class="text-decoration-none">Laporan</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Aging Piutang</li>
                </ol>
            </nav>
            <div class="d-flex align-items-center gap-2">
                <h2 class="fw-bold text-dark mb-0">Laporan Rekapitulasi Umur Piutang (AR Aging)</h2>
                <span class="badge bg-danger bg-opacity-10 text-danger border border-danger border-opacity-25 px-2 py-1">
                    Accounts Receivable
                </span>
            </div>
            <p class="text-muted small mb-0">Klasifikasi piutang tempo mitra bisnis berdasarkan ketepatan pembayaran dan lama hari keterlambatan.</p>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('reports.receivables', ['export' => 'csv']) }}" class="btn btn-outline-success d-inline-flex align-items-center gap-2">
                <i class="bi bi-file-earmark-spreadsheet"></i>
                <span>Download Aging (CSV)</span>
            </a>
            <a href="{{ route('finance.receivables.index') }}" class="btn btn-outline-coffee">
                <i class="bi bi-credit-card-2-front me-1"></i> Monitoring Piutang
            </a>
        </div>
    </div>

    <!-- Aging Category Cards -->
    <div class="row g-3 mb-4">
        <div class="col-sm-6 col-lg-3">
            <div class="card card-custom p-3 border-start border-primary border-4">
                <span class="text-muted small fw-semibold text-uppercase">Total Piutang Berjalan</span>
                <h4 class="fw-bold mb-0 text-dark">Rp {{ number_format($data['total_debt'], 0, ',', '.') }}</h4>
                <small class="text-muted mt-2 d-block border-top pt-2">Akumulasi seluruh mitra B2B</small>
            </div>
        </div>

        <div class="col-sm-6 col-lg-3">
            <div class="card card-custom p-3 border-start border-success border-4">
                <span class="text-muted small fw-semibold text-uppercase">Lancar / Belum Jatuh Tempo</span>
                <h4 class="fw-bold mb-0 text-success">Rp {{ number_format($data['total_current'], 0, ',', '.') }}</h4>
                <small class="text-muted mt-2 d-block border-top pt-2">Dalam masa termin tempo (TOP)</small>
            </div>
        </div>

        <div class="col-sm-6 col-lg-3">
            <div class="card card-custom p-3 border-start border-warning border-4">
                <span class="text-muted small fw-semibold text-uppercase">Telat 1 - 30 Hari</span>
                <h4 class="fw-bold mb-0 text-warning">Rp {{ number_format($data['total_aging1_30'], 0, ',', '.') }}</h4>
                <small class="text-muted mt-2 d-block border-top pt-2">Keterlambatan ringan / reminder</small>
            </div>
        </div>

        <div class="col-sm-6 col-lg-3">
            <div class="card card-custom p-3 border-start border-danger border-4">
                <span class="text-muted small fw-semibold text-uppercase">Telat > 30 Hari (Kritis)</span>
                <h4 class="fw-bold mb-0 text-danger">Rp {{ number_format($data['total_aging31_60'] + $data['total_agingOver60'], 0, ',', '.') }}</h4>
                <small class="text-muted mt-2 d-block border-top pt-2">Prioritas penagihan tegas & kunci kredit</small>
            </div>
        </div>
    </div>

    <!-- Main Aging Table -->
    <div class="card card-custom overflow-hidden border-0 shadow-sm">
        <div class="card-header bg-white py-3 border-bottom d-flex justify-content-between align-items-center">
            <h5 class="fw-bold text-dark mb-0">
                <i class="bi bi-clock-history text-secondary me-2"></i> Matriks Umur Piutang per Mitra Usaha
            </h5>
            <span class="badge bg-light text-secondary border">{{ $data['customers']->count() }} Mitra B2B</span>
        </div>
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th class="ps-3 py-3 text-secondary text-uppercase small fw-bold">Mitra B2B</th>
                        <th class="py-3 text-secondary text-uppercase small fw-bold text-end">Plafon</th>
                        <th class="py-3 text-secondary text-uppercase small fw-bold text-end">Lancar (Current)</th>
                        <th class="py-3 text-secondary text-uppercase small fw-bold text-end">1 - 30 Hari</th>
                        <th class="py-3 text-secondary text-uppercase small fw-bold text-end">31 - 60 Hari</th>
                        <th class="py-3 text-secondary text-uppercase small fw-bold text-end">> 60 Hari</th>
                        <th class="py-3 text-secondary text-uppercase small fw-bold text-end">Total Piutang</th>
                        <th class="pe-3 py-3 text-secondary text-uppercase small fw-bold text-end">Aksi</th>
                    </tr>
                </thead>
                <tbody class="border-top-0">
                    @forelse($data['customers'] as $cRow)
                        @php
                            $c = $cRow['customer'];
                        @endphp
                        <tr>
                            <td class="ps-3">
                                <div class="fw-bold text-dark">{{ $c->business_name }}</div>
                                <small class="text-muted">{{ $c->pic_name }} &middot; {{ $c->phone }}</small>
                            </td>
                            <td class="text-end font-monospace text-secondary">
                                Rp {{ number_format($cRow['credit_limit'], 0, ',', '.') }}
                            </td>
                            <td class="text-end font-monospace {{ $cRow['current'] > 0 ? 'text-success fw-semibold' : 'text-muted' }}">
                                {{ $cRow['current'] > 0 ? 'Rp ' . number_format($cRow['current'], 0, ',', '.') : '-' }}
                            </td>
                            <td class="text-end font-monospace {{ $cRow['aging1_30'] > 0 ? 'text-warning fw-semibold' : 'text-muted' }}">
                                {{ $cRow['aging1_30'] > 0 ? 'Rp ' . number_format($cRow['aging1_30'], 0, ',', '.') : '-' }}
                            </td>
                            <td class="text-end font-monospace {{ $cRow['aging31_60'] > 0 ? 'text-danger fw-semibold' : 'text-muted' }}">
                                {{ $cRow['aging31_60'] > 0 ? 'Rp ' . number_format($cRow['aging31_60'], 0, ',', '.') : '-' }}
                            </td>
                            <td class="text-end font-monospace {{ $cRow['agingOver60'] > 0 ? 'text-danger fw-bold' : 'text-muted' }}">
                                {{ $cRow['agingOver60'] > 0 ? 'Rp ' . number_format($cRow['agingOver60'], 0, ',', '.') : '-' }}
                            </td>
                            <td class="text-end font-monospace fw-bold {{ $cRow['total_debt'] > 0 ? 'text-dark' : 'text-muted' }}">
                                Rp {{ number_format($cRow['total_debt'], 0, ',', '.') }}
                            </td>
                            <td class="pe-3 text-end">
                                <a href="{{ route('finance.receivables.ledger', $c) }}" class="btn btn-sm btn-outline-coffee px-2" title="Buka Kartu Piutang">
                                    <i class="bi bi-journal-text me-1"></i> Ledger
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="text-center py-5 text-muted">
                                Tidak ada data piutang mitra B2B yang tercatat.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
                @if($data['customers']->count() > 0)
                    <tfoot class="table-light">
                        <tr>
                            <th class="ps-3 fw-bold text-end">TOTAL KESELURUHAN:</th>
                            <th></th>
                            <th class="text-end font-monospace text-success fw-bold">Rp {{ number_format($data['total_current'], 0, ',', '.') }}</th>
                            <th class="text-end font-monospace text-warning fw-bold">Rp {{ number_format($data['total_aging1_30'], 0, ',', '.') }}</th>
                            <th class="text-end font-monospace text-danger fw-bold">Rp {{ number_format($data['total_aging31_60'], 0, ',', '.') }}</th>
                            <th class="text-end font-monospace text-danger fw-bold">Rp {{ number_format($data['total_agingOver60'], 0, ',', '.') }}</th>
                            <th class="text-end font-monospace text-dark fw-bold fs-6">Rp {{ number_format($data['total_debt'], 0, ',', '.') }}</th>
                            <th></th>
                        </tr>
                    </tfoot>
                @endif
            </table>
        </div>
    </div>
</div>
@endsection
