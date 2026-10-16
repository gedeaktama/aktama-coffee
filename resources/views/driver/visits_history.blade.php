@extends('layouts.app')

@section('title', 'Riwayat Kunjungan Toko - Kopi Distro')

@section('content')
<div class="py-2">

    <!-- Header & Date Filter -->
    <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-3">
        <div>
            <h4 class="fw-bold mb-0">Riwayat Kunjungan Toko</h4>
            <small class="text-muted">Daftar rekapitulasi kehadiran dan pengiriman armada lapangan</small>
        </div>

        <form action="{{ route('driver.visits.history') }}" method="GET" class="d-flex align-items-center gap-2">
            <input type="date" name="date" class="form-control form-control-sm" value="{{ $date }}" onchange="this.form.submit()">
            <a href="{{ route('driver.dashboard') }}" class="btn btn-sm btn-outline-secondary">
                <i class="bi bi-arrow-left"></i> Dashboard
            </a>
        </form>
    </div>

    <!-- Table of Visits -->
    <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th class="ps-3">No. Kunjungan</th>
                        <th>Toko / Mitra</th>
                        <th>Jam Check-In</th>
                        <th>Durasi</th>
                        <th>Produk Laku</th>
                        <th>Restock</th>
                        <th>Penerima</th>
                        <th>Status</th>
                        <th class="text-end pe-3">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($visits as $v)
                    <tr>
                        <td class="ps-3 fw-bold text-dark font-monospace small">
                            {{ $v->visit_number }}
                        </td>
                        <td>
                            <strong class="d-block text-dark">{{ $v->customer->business_name }}</strong>
                            <small class="text-muted">{{ $v->customer->shipping_address ?? $v->customer->address }}</small>
                        </td>
                        <td>
                            <span class="badge bg-light text-dark border">
                                <i class="bi bi-clock me-1"></i> {{ $v->check_in_at->format('H:i') }} WITA
                            </span>
                        </td>
                        <td>
                            @if($v->duration_minutes !== null)
                                <span class="small text-muted">{{ $v->duration_minutes }} menit</span>
                            @else
                                <span class="small text-warning">Sedang berlangsung</span>
                            @endif
                        </td>
                        <td>
                            <strong class="text-success">{{ $v->summary_sold_qty }} pack</strong>
                            @if($v->summary_sold_amount > 0)
                                <small class="d-block text-muted">Rp {{ number_format($v->summary_sold_amount, 0, ',', '.') }}</small>
                            @endif
                        </td>
                        <td>
                            <span class="badge bg-warning-subtle text-warning">
                                +{{ $v->summary_restock_qty }} pack
                            </span>
                        </td>
                        <td>
                            <span class="small">{{ $v->recipient_name ?? '-' }}</span>
                        </td>
                        <td>
                            <span class="badge {{ $v->status_badge_class }} rounded-pill px-2 py-1">
                                {{ $v->status_label }}
                            </span>
                        </td>
                        <td class="text-end pe-3">
                            @if($v->status === \App\Models\StoreVisit::STATUS_IN_PROGRESS)
                                <a href="{{ route('driver.visit.action', $v->id) }}" class="btn btn-sm btn-warning fw-bold rounded-pill px-3">
                                    Lanjutkan
                                </a>
                            @else
                                <span class="text-success small fw-bold">
                                    <i class="bi bi-check-circle-fill"></i> Lengkap
                                </span>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="9" class="text-center py-4 text-muted">
                            <i class="bi bi-inbox fs-2 d-block mb-1"></i>
                            Tidak ada data kunjungan pada tanggal {{ \Carbon\Carbon::parse($date)->translatedFormat('d F Y') }}.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($visits->hasPages())
        <div class="card-footer bg-white border-0 py-3">
            {{ $visits->appends(['date' => $date])->links() }}
        </div>
        @endif
    </div>

</div>
@endsection
