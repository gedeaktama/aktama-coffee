@extends('layouts.app')

@section('title', 'Checkout Pesanan Retail - Kopi Distro')

@section('content')
<div class="container py-3">
    <!-- Header -->
    <div class="mb-4">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-1">
                <li class="breadcrumb-item"><a href="{{ route('store.catalog') }}" class="text-decoration-none">Katalog</a></li>
                <li class="breadcrumb-item"><a href="{{ route('store.cart') }}" class="text-decoration-none">Keranjang</a></li>
                <li class="breadcrumb-item active" aria-current="page">Checkout Pesanan</li>
            </ol>
        </nav>
        <h2 class="fw-bold text-dark mb-1">Penyelesaian Pesanan (Checkout)</h2>
        <p class="text-muted mb-0">Lengkapi data tujuan pengiriman dan pilih metode pembayaran untuk memproses pesanan kopi Anda.</p>
    </div>

    @if ($errors->any())
        <div class="alert alert-danger alert-dismissible fade show shadow-sm mb-4" role="alert">
            <div class="d-flex align-items-center mb-1">
                <i class="bi bi-exclamation-triangle-fill fs-5 me-2"></i>
                <h6 class="fw-bold mb-0">Terdapat kesalahan pada input pengiriman:</h6>
            </div>
            <ul class="mb-0 ps-4 small">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <form method="POST" action="{{ route('store.checkout.process') }}">
        @csrf

        <div class="row g-4">
            <!-- Left Column: Shipping & Payment Form -->
            <div class="col-lg-7">
                <div class="card card-custom p-4 mb-4">
                    <h5 class="fw-bold text-dark mb-3 pb-2 border-bottom">
                        <i class="bi bi-geo-alt me-2 text-danger"></i> Tujuan Pengiriman Pesanan
                    </h5>

                    <div class="mb-3">
                        <label for="recipient_name" class="form-label fw-semibold">Nama Lengkap Penerima <span class="text-danger">*</span></label>
                        <input type="text" name="recipient_name" id="recipient_name" class="form-control @error('recipient_name') is-invalid @enderror" value="{{ old('recipient_name', $customer->pic_name ?? $user?->name) }}" required>
                    </div>

                    <div class="mb-3">
                        <label for="recipient_phone" class="form-label fw-semibold">Nomor Kontak / WhatsApp Aktif <span class="text-danger">*</span></label>
                        <input type="text" name="recipient_phone" id="recipient_phone" class="form-control @error('recipient_phone') is-invalid @enderror" value="{{ old('recipient_phone', $customer->phone ?? $user?->phone) }}" placeholder="Contoh: 081234567890" required>
                    </div>

                    <div class="mb-3">
                        <label for="shipping_address" class="form-label fw-semibold">Alamat Lengkap Pengiriman <span class="text-danger">*</span></label>
                        <textarea name="shipping_address" id="shipping_address" rows="3" class="form-control @error('shipping_address') is-invalid @enderror" placeholder="Sertakan nama jalan, nomor rumah, RT/RW, kelurahan, kecamatan, kota & kode pos" required>{{ old('shipping_address', $customer->shipping_address ?? $customer->address) }}</textarea>
                    </div>

                    <div class="mb-3">
                        <label for="notes" class="form-label fw-semibold">Catatan untuk Kurir (Opsional)</label>
                        <input type="text" name="notes" id="notes" class="form-control" value="{{ old('notes') }}" placeholder="Contoh: Titipkan di pos satpam, pagar warna hitam">
                    </div>
                </div>

                <div class="card card-custom p-4">
                    <h5 class="fw-bold text-dark mb-3 pb-2 border-bottom">
                        <i class="bi bi-wallet2 me-2 text-success"></i> Metode Pembayaran
                    </h5>

                    <div class="form-check p-3 rounded-3 border mb-2">
                        <input class="form-check-input ms-0 me-3" type="radio" name="payment_type" id="payTransfer" value="transfer" {{ old('payment_type', 'transfer') === 'transfer' ? 'checked' : '' }} required>
                        <label class="form-check-label w-100" for="payTransfer">
                            <div class="fw-bold text-dark">Transfer Rekening Bank</div>
                            <small class="text-muted d-block">BCA, Mandiri, BRI &mdash; Konfirmasi otomatis setelah transfer diterima.</small>
                        </label>
                    </div>

                    <div class="form-check p-3 rounded-3 border">
                        <input class="form-check-input ms-0 me-3" type="radio" name="payment_type" id="payCash" value="cash" {{ old('payment_type') === 'cash' ? 'checked' : '' }}>
                        <label class="form-check-label w-100" for="payCash">
                            <div class="fw-bold text-dark">Bayar Tunai di Tempat (COD / Di Toko)</div>
                            <small class="text-muted d-block">Bayar tunai kepada kurir atau saat pengambilan langsung di distributor.</small>
                        </label>
                    </div>
                </div>
            </div>

            <!-- Right Column: Order Summary -->
            <div class="col-lg-5">
                <div class="card card-custom p-4 sticky-top" style="top: 80px;">
                    <div class="d-flex justify-content-between align-items-center mb-3 pb-2 border-bottom">
                        <h5 class="fw-bold text-dark mb-0">
                            <i class="bi bi-bag-check me-2 text-primary"></i> Rincian Belanja
                        </h5>
                        @if($currentCustomerType === 'toko')
                            <span class="badge bg-success bg-opacity-10 text-success border border-success-subtle px-2 py-1 rounded-pill small fw-bold">
                                <i class="bi bi-shop me-1"></i> Mitra Toko
                            </span>
                        @elseif($currentCustomerType === 'kafe')
                            <span class="badge bg-primary bg-opacity-10 text-primary border border-primary-subtle px-2 py-1 rounded-pill small fw-bold">
                                <i class="bi bi-cup-hot-fill me-1"></i> Mitra Kafe
                            </span>
                        @endif
                    </div>

                    <div class="list-group list-group-flush mb-3">
                        @foreach($cart as $item)
                            <div class="list-group-item px-0 py-2 d-flex justify-content-between align-items-center">
                                <div>
                                    <div class="fw-semibold text-dark small">{{ $item['name'] }}</div>
                                    <small class="text-muted">{{ $item['quantity'] }} pack &times; Rp {{ number_format($item['price'], 0, ',', '.') }}</small>
                                </div>
                                <span class="fw-bold text-dark small">Rp {{ number_format($item['price'] * $item['quantity'], 0, ',', '.') }}</span>
                            </div>
                        @endforeach
                    </div>

                    @php
                        $checkoutSavings = 0;
                        foreach($cart as $c) {
                            if (isset($c['regular_price']) && $c['regular_price'] > $c['price']) {
                                $checkoutSavings += ($c['regular_price'] - $c['price']) * $c['quantity'];
                            }
                        }
                    @endphp

                    <div class="d-flex justify-content-between mb-2">
                        <span class="text-muted">Subtotal:</span>
                        <span class="fw-bold text-dark">Rp {{ number_format($subtotal, 0, ',', '.') }}</span>
                    </div>

                    @if($checkoutSavings > 0)
                        <div class="d-flex justify-content-between mb-2 text-success fw-semibold small">
                            <span><i class="bi bi-tag-fill me-1"></i> Hemat Harga Mitra:</span>
                            <span>- Rp {{ number_format($checkoutSavings, 0, ',', '.') }}</span>
                        </div>
                    @endif

                    <div class="d-flex justify-content-between mb-2">
                        <span class="text-muted">Biaya Kirim Flat:</span>
                        <span class="text-secondary">Rp 15.000</span>
                    </div>

                    <hr>

                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <span class="fw-bold text-dark fs-5">Total Pembayaran:</span>
                        <span class="fw-bold text-success fs-5">Rp {{ number_format($subtotal + 15000, 0, ',', '.') }}</span>
                    </div>

                    <button type="submit" class="btn btn-coffee w-100 py-3 fw-bold shadow">
                        <i class="bi bi-shield-lock-fill me-1"></i> Konfirmasi & Buat Pesanan
                    </button>

                    <div class="text-center mt-3">
                        <small class="text-muted"><i class="bi bi-shield-check text-success me-1"></i> Transaksi Terenkripsi & Aman</small>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>
@endsection
