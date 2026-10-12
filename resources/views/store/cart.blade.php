@extends('layouts.app')

@section('title', 'Keranjang Belanja Kopi - Kopi Distro')

@section('content')
<div class="container py-3">
    <!-- Header -->
    <div class="mb-4">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-1">
                <li class="breadcrumb-item"><a href="{{ route('store.catalog') }}" class="text-decoration-none">Katalog Kopi</a></li>
                <li class="breadcrumb-item active" aria-current="page">Keranjang Belanja</li>
            </ol>
        </nav>
        <h2 class="fw-bold text-dark mb-1">Keranjang Belanja Kopi Anda</h2>
        <p class="text-muted mb-0">Periksa kembali item biji dan bubuk kopi yang ingin Anda pesan sebelum melakukan checkout.</p>
    </div>

    @if(empty($cart))
        <div class="card card-custom p-5 text-center my-4">
            <i class="bi bi-cart-x display-3 text-secondary opacity-50 mb-3"></i>
            <h4 class="fw-bold text-dark">Keranjang Belanja Anda Kosong</h4>
            <p class="text-muted mb-4">Temukan aneka varian biji dan bubuk kopi khas nusantara di katalog kami.</p>
            <div>
                <a href="{{ route('store.catalog') }}" class="btn btn-coffee px-4 shadow-sm">
                    <i class="bi bi-shop me-1"></i> Jelajahi Katalog Kopi
                </a>
            </div>
        </div>
    @else
        <div class="row g-4">
            <!-- Cart Items Table -->
            <div class="col-lg-8">
                <div class="card card-custom p-4">
                    <h5 class="fw-bold text-dark mb-3 pb-2 border-bottom">
                        <i class="bi bi-bag-check me-2 text-primary"></i> Daftar Item Pesanan ({{ count($cart) }})
                    </h5>

                    <div class="table-responsive">
                        <table class="table align-middle">
                            <thead class="table-light">
                                <tr>
                                    <th>Produk & Varian Kopi</th>
                                    <th style="width: 140px;" class="text-end">Harga Satuan</th>
                                    <th style="width: 140px;" class="text-center">Jumlah (Pack)</th>
                                    <th style="width: 140px;" class="text-end">Subtotal</th>
                                    <th style="width: 50px;" class="text-center">#</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($cart as $item)
                                    <tr>
                                        <td>
                                            <div class="d-flex align-items-center gap-3">
                                                @if(!empty($item['image']))
                                                    <img src="{{ $item['image'] }}" alt="{{ $item['name'] }}" class="rounded-2 border bg-light p-1" style="width: 52px; height: 52px; object-fit: contain;">
                                                @endif
                                                <div>
                                                    <div class="font-monospace small text-muted">{{ $item['sku'] }}</div>
                                                    <div class="fw-bold text-dark">{{ $item['name'] }}</div>
                                                    <small class="text-muted">Berat: {{ $item['weight'] }}g &middot; Bentuk: {{ ucfirst($item['grind_form']) }}</small>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="text-end text-secondary">
                                            Rp {{ number_format($item['price'], 0, ',', '.') }}
                                        </td>
                                        <td>
                                            <form method="POST" action="{{ route('store.cart.update') }}" class="d-flex align-items-center justify-content-center gap-1">
                                                @csrf
                                                <input type="hidden" name="variant_id" value="{{ $item['variant_id'] }}">
                                                <input type="number" name="quantity" class="form-control form-control-sm text-center" value="{{ $item['quantity'] }}" min="1" style="width: 60px;">
                                                <button type="submit" class="btn btn-sm btn-outline-secondary p-1" title="Perbarui Jumlah">
                                                    <i class="bi bi-arrow-clockwise"></i>
                                                </button>
                                            </form>
                                        </td>
                                        <td class="text-end fw-bold text-dark">
                                            Rp {{ number_format($item['price'] * $item['quantity'], 0, ',', '.') }}
                                        </td>
                                        <td class="text-center">
                                            <form method="POST" action="{{ route('store.cart.remove', $item['variant_id']) }}">
                                                @csrf
                                                <button type="submit" class="btn btn-sm btn-outline-danger p-1" title="Hapus Item">
                                                    <i class="bi bi-trash"></i>
                                                </button>
                                            </form>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <div class="d-flex justify-content-between align-items-center mt-3 pt-3 border-top">
                        <a href="{{ route('store.catalog') }}" class="btn btn-outline-secondary">
                            <i class="bi bi-arrow-left me-1"></i> Tambah Belanjaan Lain
                        </a>
                    </div>
                </div>
            </div>

            <!-- Order Summary Card -->
            <div class="col-lg-4">
                <div class="card card-custom p-4 sticky-top" style="top: 80px;">
                    <h5 class="fw-bold text-dark mb-3 pb-2 border-bottom">
                        <i class="bi bi-receipt me-2 text-warning"></i> Ringkasan Belanja
                    </h5>

                    <div class="d-flex justify-content-between mb-2">
                        <span class="text-muted">Subtotal Produk:</span>
                        <span class="fw-bold text-dark">Rp {{ number_format($total, 0, ',', '.') }}</span>
                    </div>
                    <div class="d-flex justify-content-between mb-2">
                        <span class="text-muted">Estimasi Biaya Kirim:</span>
                        <span class="text-secondary">Rp 15.000 <small class="text-muted">(Flat)</small></span>
                    </div>

                    <hr>

                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <span class="fw-bold text-dark fs-5">Total Bayar:</span>
                        <span class="fw-bold text-success fs-5">Rp {{ number_format($total + 15000, 0, ',', '.') }}</span>
                    </div>

                    <a href="{{ route('store.checkout') }}" class="btn btn-coffee w-100 py-2 fw-bold shadow-sm">
                        <i class="bi bi-credit-card me-1"></i> Lanjut ke Checkout &rarr;
                    </a>
                </div>
            </div>
        </div>
    @endif
</div>
@endsection
