@extends('layouts.app')

@section('title', 'Pesanan Berhasil Dibuat - Kopi Distro')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-lg-7">
            <div class="card card-custom p-4 p-md-5 text-center shadow">
                <div class="mb-3">
                    <span class="badge rounded-circle bg-success bg-opacity-10 text-success p-3 display-4">
                        <i class="bi bi-check-circle-fill"></i>
                    </span>
                </div>

                <h3 class="fw-bold text-dark mb-1">Pesanan Anda Berhasil Diterbitkan!</h3>
                <p class="text-muted">Terima kasih telah berbelanja kopi nusantara di Kopi Distro.</p>

                <div class="p-3 bg-light rounded-3 border my-3 text-start">
                    <div class="d-flex justify-content-between mb-1">
                        <span class="text-muted">Nomor Pesanan:</span>
                        <strong class="font-monospace text-primary fs-6">{{ $order->order_number }}</strong>
                    </div>
                    <div class="d-flex justify-content-between mb-1">
                        <span class="text-muted">Penerima:</span>
                        <span class="fw-semibold text-dark">{{ $order->recipient_name }} ({{ $order->recipient_phone }})</span>
                    </div>
                    <div class="d-flex justify-content-between mb-1">
                        <span class="text-muted">Metode Pembayaran:</span>
                        <span class="badge bg-secondary">{{ $order->payment_type_label }}</span>
                    </div>
                    <div class="d-flex justify-content-between border-top pt-2 mt-2">
                        <span class="fw-bold text-dark">Total Pembayaran:</span>
                        <strong class="fw-bold text-success fs-5">{{ $order->formatted_total }}</strong>
                    </div>
                </div>

                @if($order->payment_type === 'transfer')
                    <div class="alert alert-info text-start small border-0 mb-4">
                        <h6 class="fw-bold mb-2"><i class="bi bi-info-circle-fill me-1"></i> Informasi Rekening Pembayaran Transfer:</h6>
                        <div class="mb-1"><strong>Bank BCA:</strong> 8899-1234-56 a/n PT Kopi Nusantara Distro</div>
                        <div><strong>Bank Mandiri:</strong> 137-00-9876543-2 a/n PT Kopi Nusantara Distro</div>
                        <small class="text-muted d-block mt-2">Sertakan nomor pesanan <code>{{ $order->order_number }}</code> pada berita transfer Anda.</small>
                    </div>
                @else
                    <div class="alert alert-warning text-start small border-0 mb-4">
                        <h6 class="fw-bold mb-1"><i class="bi bi-cash-stack me-1"></i> Pembayaran Tunai (COD):</h6>
                        <p class="mb-0">Silakan siapkan uang pas sebesar <strong>{{ $order->formatted_total }}</strong> saat kurir mengantarkan paket ke alamat Anda.</p>
                    </div>
                @endif

                <div class="d-flex justify-content-center gap-2">
                    <a href="{{ route('store.catalog') }}" class="btn btn-outline-coffee">
                        <i class="bi bi-shop me-1"></i> Belanja Kopi Lainnya
                    </a>
                    @auth
                        @if(auth()->user()->isCustomer())
                            <a href="{{ route('store.my_orders') }}" class="btn btn-coffee">
                                <i class="bi bi-box-seam me-1"></i> Riwayat Pesanan Saya
                            </a>
                        @endif
                    @endauth
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
