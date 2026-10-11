@extends('layouts.app')

@section('title', 'Masuk - Kopi Distro')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-md-6 col-lg-5">
            <div class="card card-custom p-4 p-md-5">
                <div class="text-center mb-4">
                    <div class="d-inline-flex p-3 rounded-circle bg-light text-primary mb-2 shadow-sm" style="background-color: #f7ede2 !important; color: #a2673b !important;">
                        <i class="bi bi-shield-lock-fill fs-2"></i>
                    </div>
                    <h3 class="fw-bold mb-1">Masuk ke Sistem</h3>
                    <p class="text-muted small">Silakan masukkan email dan password akun Anda</p>
                </div>

                <form method="POST" action="{{ route('login') }}">
                    @csrf

                    <div class="mb-3">
                        <label for="email" class="form-label fw-semibold">Alamat Email</label>
                        <div class="input-group">
                            <span class="input-group-text bg-light"><i class="bi bi-envelope"></i></span>
                            <input id="email" type="email" class="form-control @error('email') is-invalid @enderror" name="email" value="{{ old('email') }}" required autocomplete="email" autofocus placeholder="nama@kopi.com">
                        </div>
                        @error('email')
                            <div class="invalid-feedback d-block mt-1">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="password" class="form-label fw-semibold">Password</label>
                        <div class="input-group">
                            <span class="input-group-text bg-light"><i class="bi bi-key"></i></span>
                            <input id="password" type="password" class="form-control @error('password') is-invalid @enderror" name="password" required autocomplete="current-password" placeholder="••••••••">
                        </div>
                        @error('password')
                            <div class="invalid-feedback d-block mt-1">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-4 form-check">
                        <input type="checkbox" class="form-check-input" id="remember" name="remember" {{ old('remember') ? 'checked' : '' }}>
                        <label class="form-check-label text-muted" for="remember">Ingat Saya di Perangkat Ini</label>
                    </div>

                    <button type="submit" class="btn btn-coffee w-100 py-2 fs-6">
                        <i class="bi bi-box-arrow-in-right me-1"></i> Masuk Sekarang
                    </button>
                </form>

                <div class="text-center mt-3 pt-3 border-top">
                    <small class="text-muted">Belum punya akun pelanggan? <a href="{{ route('register') }}" class="text-decoration-none fw-semibold" style="color: #a2673b;">Daftar Akun Retail</a></small>
                </div>
            </div>

            <!-- Quick Demo Credentials for Instant Testing -->
            <div class="card card-custom p-3 mt-4 bg-light border-0">
                <div class="d-flex align-items-center justify-content-between mb-2">
                    <small class="fw-bold text-uppercase text-secondary" style="font-size: 0.72rem; letter-spacing: 0.5px;">
                        <i class="bi bi-lightning-charge-fill text-warning me-1"></i> Akun Pengujian Cepat (Password: password123)
                    </small>
                </div>
                <div class="d-flex flex-wrap gap-1">
                    <button type="button" class="btn btn-sm btn-outline-dark demo-btn" onclick="fillLogin('owner@kopi.com', 'password123')">Owner</button>
                    <button type="button" class="btn btn-sm btn-outline-primary demo-btn" onclick="fillLogin('admin@kopi.com', 'password123')">Admin</button>
                    <button type="button" class="btn btn-sm btn-outline-success demo-btn" onclick="fillLogin('sales@kopi.com', 'password123')">Sales</button>
                    <button type="button" class="btn btn-sm btn-outline-warning text-dark demo-btn" onclick="fillLogin('warehouse@kopi.com', 'password123')">Warehouse</button>
                    <button type="button" class="btn btn-sm btn-outline-info text-dark demo-btn" onclick="fillLogin('finance@kopi.com', 'password123')">Finance</button>
                    <button type="button" class="btn btn-sm btn-outline-secondary demo-btn" onclick="fillLogin('pelanggan@gmail.com', 'password123')">Customer</button>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    function fillLogin(email, password) {
        document.getElementById('email').value = email;
        document.getElementById('password').value = password;
    }
</script>
@endpush
