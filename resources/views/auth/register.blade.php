@extends('layouts.app')

@section('title', 'Daftar Akun Pelanggan - Kopi Distro')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-md-7 col-lg-6">
            <div class="card card-custom p-4 p-md-5">
                <div class="text-center mb-4">
                    <div class="d-inline-flex p-3 rounded-circle bg-light text-success mb-2 shadow-sm" style="background-color: #eafaf1 !important; color: #27ae60 !important;">
                        <i class="bi bi-person-plus-fill fs-2"></i>
                    </div>
                    <h3 class="fw-bold mb-1">Daftar Akun Retail</h3>
                    <p class="text-muted small">Buat akun untuk memesan kopi kemasan premium dan melihat riwayat belanja</p>
                </div>

                <form method="POST" action="{{ route('register') }}">
                    @csrf

                    <div class="mb-3">
                        <label for="name" class="form-label fw-semibold">Nama Lengkap</label>
                        <div class="input-group">
                            <span class="input-group-text bg-light"><i class="bi bi-person"></i></span>
                            <input id="name" type="text" class="form-control @error('name') is-invalid @enderror" name="name" value="{{ old('name') }}" required autofocus placeholder="Contoh: Budi Santoso">
                        </div>
                        @error('name')
                            <div class="invalid-feedback d-block mt-1">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="email" class="form-label fw-semibold">Alamat Email</label>
                        <div class="input-group">
                            <span class="input-group-text bg-light"><i class="bi bi-envelope"></i></span>
                            <input id="email" type="email" class="form-control @error('email') is-invalid @enderror" name="email" value="{{ old('email') }}" required placeholder="budi@example.com">
                        </div>
                        @error('email')
                            <div class="invalid-feedback d-block mt-1">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="phone" class="form-label fw-semibold">Nomor WhatsApp / Telepon</label>
                        <div class="input-group">
                            <span class="input-group-text bg-light"><i class="bi bi-telephone"></i></span>
                            <input id="phone" type="text" class="form-control @error('phone') is-invalid @enderror" name="phone" value="{{ old('phone') }}" placeholder="08123456789">
                        </div>
                        @error('phone')
                            <div class="invalid-feedback d-block mt-1">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="password" class="form-label fw-semibold">Password</label>
                            <div class="input-group">
                                <span class="input-group-text bg-light"><i class="bi bi-lock"></i></span>
                                <input id="password" type="password" class="form-control @error('password') is-invalid @enderror" name="password" required placeholder="Minimal 8 karakter">
                            </div>
                            @error('password')
                                <div class="invalid-feedback d-block mt-1">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6 mb-3">
                            <label for="password_confirmation" class="form-label fw-semibold">Ulangi Password</label>
                            <div class="input-group">
                                <span class="input-group-text bg-light"><i class="bi bi-lock-fill"></i></span>
                                <input id="password_confirmation" type="password" class="form-control" name="password_confirmation" required placeholder="Ulangi password">
                            </div>
                        </div>
                    </div>

                    <div class="alert alert-light border small text-muted mb-4">
                        <i class="bi bi-info-circle me-1 text-primary"></i> Pendaftaran publik secara otomatis didaftarkan sebagai akun <strong>Pelanggan Retail</strong>. Untuk pesanan grosir (kafe, toko, reseller) akan ditangani langsung oleh Sales/Admin.
                    </div>

                    <button type="submit" class="btn btn-coffee w-100 py-2 fs-6">
                        <i class="bi bi-check-circle me-1"></i> Selesaikan Pendaftaran
                    </button>
                </form>

                <div class="text-center mt-4 pt-3 border-top">
                    <small class="text-muted">Sudah punya akun? <a href="{{ route('login') }}" class="text-decoration-none fw-semibold" style="color: #a2673b;">Masuk di sini</a></small>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
