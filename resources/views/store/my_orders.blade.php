@extends('layouts.app')

@section('title', 'Riwayat Pesanan Saya - Kopi Distro')

@section('content')
<div class="container py-3">
    <!-- Header -->
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-4">
        <div>
            <h2 class="fw-bold text-dark mb-1">Riwayat Pesanan Saya</h2>
            <p class="text-muted mb-0">Pantau status pesanan kopi dan riwayat transaksi belanja Anda.</p>
        </div>
        <div>
            <a href="{{ route('store.catalog') }}" class="btn btn-coffee shadow-sm">
                <i class="bi bi-shop me-1"></i> Belanja Kopi Baru
            </a>
        </div>
    </div>

    <!-- Orders Table -->
    <div class="card card-custom overflow-hidden">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th class="ps-3 py-3 text-secondary text-uppercase small fw-bold">No. Pesanan</th>
                        <th class="py-3 text-secondary text-uppercase small fw-bold">Tanggal</th>
                        <th class="py-3 text-secondary text-uppercase small fw-bold">Item Kopi</th>
                        <th class="py-3 text-secondary text-uppercase small fw-bold text-end">Total Biaya</th>
                        <th class="py-3 text-secondary text-uppercase small fw-bold text-center">Status Pesanan</th>
                        <th class="pe-3 py-3 text-secondary text-uppercase small fw-bold text-center">Status Bayar</th>
                    </tr>
                </thead>
                <tbody class="border-top-0">
                    @forelse($orders as $ord)
                        <tr>
                            <td class="ps-3 font-monospace fw-bold text-primary">
                                {{ $ord->order_number }}
                            </td>
                            <td class="text-muted small">
                                <i class="bi bi-calendar3 me-1"></i>{{ $ord->order_date->format('d/m/Y') }}
                            </td>
                            <td>
                                <ul class="list-unstyled mb-0 small">
                                    @foreach($ord->items as $item)
                                        <li>{{ $item->quantity }}x {{ $item->variant->product->name }} ({{ $item->variant->variant_name }})</li>
                                    @endforeach
                                </ul>
                            </td>
                            <td class="text-end fw-bold text-dark">
                                {{ $ord->formatted_total }}
                            </td>
                            <td class="text-center">
                                <span class="badge {{ $ord->status_badge_class }} px-3 py-2 rounded-pill">
                                    {{ $ord->status_label }}
                                </span>
                            </td>
                            <td class="pe-3 text-center">
                                <span class="badge {{ $ord->payment_status_badge_class }} px-3 py-2 rounded-pill">
                                    {{ $ord->payment_status_label }}
                                </span>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center py-5 text-muted">
                                <i class="bi bi-receipt fs-1 text-secondary opacity-50 mb-3 d-block"></i>
                                <h6 class="fw-semibold text-dark">Anda belum memiliki riwayat pesanan</h6>
                                <p class="small text-muted mb-3">Silakan jelajahi aneka kopi pilihan di katalog kami.</p>
                                <a href="{{ route('store.catalog') }}" class="btn btn-sm btn-coffee">
                                    <i class="bi bi-shop me-1"></i> Buka Katalog Kopi
                                </a>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($orders->hasPages())
            <div class="card-footer bg-white border-top py-3 d-flex justify-content-between align-items-center">
                <small class="text-muted">Menampilkan {{ $orders->firstItem() }} - {{ $orders->lastItem() }} dari {{ $orders->total() }} pesanan</small>
                <div>{{ $orders->links() }}</div>
            </div>
        @endif
    </div>
</div>
@endsection
