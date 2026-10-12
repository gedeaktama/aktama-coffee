@extends('layouts.app')

@section('title', 'Daftar Akun Pelanggan & Mitra - Kopi Distro')

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-lg-8 col-xl-7">
            <div class="card card-custom p-4 p-md-5 border-0 shadow-sm rounded-4">
                <div class="text-center mb-4">
                    <div class="d-inline-flex p-3 rounded-circle bg-warning bg-opacity-20 text-warning mb-2 shadow-sm">
                        <i class="bi bi-person-badge fs-2"></i>
                    </div>
                    <h3 class="fw-bold text-dark mb-1">Daftar Akun Kopi Distro</h3>
                    <p class="text-muted small">Pilih kategori akun untuk mendapatkan penyesuaian harga khusus (Retail, Toko, atau Kafe).</p>
                </div>

                <form method="POST" action="{{ route('register') }}" id="registerForm">
                    @csrf

                    <!-- 1. PILIH KATEGORI AKUN (TIER HARGA) -->
                    <div class="mb-4">
                        <label class="form-label fw-bold text-dark mb-2">
                            <i class="bi bi-tags-fill me-1 text-warning"></i> Pilih Kategori Akun Anda:
                        </label>
                        <div class="row g-2">
                            <!-- Retail / Konsumen -->
                            <div class="col-md-4">
                                <label class="card h-100 p-3 text-center cursor-pointer border rounded-3 position-relative account-type-card" style="cursor: pointer;">
                                    <input type="radio" name="customer_type" value="retail" class="form-check-input position-absolute top-0 end-0 m-2" {{ old('customer_type', 'retail') === 'retail' ? 'checked' : '' }} onchange="toggleBusinessFields()">
                                    <div class="fs-3 text-primary mb-1"><i class="bi bi-person-fill"></i></div>
                                    <strong class="d-block text-dark small">Konsumen Retail</strong>
                                    <small class="text-muted d-block" style="font-size: 0.72rem;">Penikmat kopi umum &amp; rumahan (Harga Eceran)</small>
                                </label>
                            </div>

                            <!-- Toko / Warung -->
                            <div class="col-md-4">
                                <label class="card h-100 p-3 text-center cursor-pointer border rounded-3 position-relative account-type-card" style="cursor: pointer;">
                                    <input type="radio" name="customer_type" value="toko" class="form-check-input position-absolute top-0 end-0 m-2" {{ old('customer_type') === 'toko' ? 'checked' : '' }} onchange="toggleBusinessFields()">
                                    <div class="fs-3 text-success mb-1"><i class="bi bi-shop"></i></div>
                                    <strong class="d-block text-dark small">Mitra Toko / Warung</strong>
                                    <small class="text-muted d-block" style="font-size: 0.72rem;">Warung kelontong &amp; reseller (Harga Grosir Toko)</small>
                                </label>
                            </div>

                            <!-- Kafe / Kedai Kopi -->
                            <div class="col-md-4">
                                <label class="card h-100 p-3 text-center cursor-pointer border rounded-3 position-relative account-type-card" style="cursor: pointer;">
                                    <input type="radio" name="customer_type" value="kafe" class="form-check-input position-absolute top-0 end-0 m-2" {{ old('customer_type') === 'kafe' ? 'checked' : '' }} onchange="toggleBusinessFields()">
                                    <div class="fs-3 text-warning mb-1"><i class="bi bi-cup-hot-fill"></i></div>
                                    <strong class="d-block text-dark small">Mitra Kafe / Kedai</strong>
                                    <small class="text-muted d-block" style="font-size: 0.72rem;">Coffee Shop &amp; Horeca (Harga Khusus Kafe)</small>
                                </label>
                            </div>
                        </div>
                        @error('customer_type')
                            <div class="text-danger small mt-1">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- 2. DATA IDENTITAS & KONTAK -->
                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <label for="name" class="form-label fw-semibold small">Nama Lengkap Penanggung Jawab <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <span class="input-group-text bg-light"><i class="bi bi-person"></i></span>
                                <input id="name" type="text" class="form-control @error('name') is-invalid @enderror" name="name" value="{{ old('name') }}" required autofocus placeholder="Contoh: Budi Santoso">
                            </div>
                            @error('name')
                                <div class="invalid-feedback d-block mt-1">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6">
                            <label for="phone" class="form-label fw-semibold small">No. WhatsApp / Telepon <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <span class="input-group-text bg-light"><i class="bi bi-telephone"></i></span>
                                <input id="phone" type="text" class="form-control @error('phone') is-invalid @enderror" name="phone" value="{{ old('phone') }}" required placeholder="Contoh: 08123456789">
                            </div>
                            @error('phone')
                                <div class="invalid-feedback d-block mt-1">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <!-- 3. KOLOM NAMA BISNIS / USAHA (DINAMIS UNTUK TOKO & KAFE) -->
                    <div id="businessNameContainer" class="mb-3 {{ old('customer_type', 'retail') === 'retail' ? 'd-none' : '' }}">
                        <label for="business_name" class="form-label fw-semibold small">
                            <span id="businessLabelText">Nama Toko / Nama Kafe</span> <span class="text-danger">*</span>
                        </label>
                        <div class="input-group">
                            <span class="input-group-text bg-light"><i class="bi bi-building"></i></span>
                            <input id="business_name" type="text" class="form-control @error('business_name') is-invalid @enderror" name="business_name" value="{{ old('business_name') }}" placeholder="Contoh: Kafe Kopi Senja / Toko Barokah">
                        </div>
                        <small class="text-muted" style="font-size: 0.75rem;">Nama outlet atau badan usaha Anda untuk identitas kemitraan.</small>
                        @error('business_name')
                            <div class="invalid-feedback d-block mt-1">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="email" class="form-label fw-semibold small">Alamat Email Aktif <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <span class="input-group-text bg-light"><i class="bi bi-envelope"></i></span>
                            <input id="email" type="email" class="form-control @error('email') is-invalid @enderror" name="email" value="{{ old('email') }}" required placeholder="budi@example.com">
                        </div>
                        @error('email')
                            <div class="invalid-feedback d-block mt-1">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="address" class="form-label fw-semibold small">Alamat Lengkap Pengiriman / Lokasi Outlet</label>
                        <textarea id="address" class="form-control @error('address') is-invalid @enderror" name="address" rows="2" placeholder="Nama jalan, nomor ruko/rumah, kelurahan, kecamatan, kota">{{ old('address') }}</textarea>
                        @error('address')
                            <div class="invalid-feedback d-block mt-1">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- 4. KEAMANAN PASSWORD -->
                    <div class="row g-3 mb-4">
                        <div class="col-md-6">
                            <label for="password" class="form-label fw-semibold small">Password <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <span class="input-group-text bg-light"><i class="bi bi-lock"></i></span>
                                <input id="password" type="password" class="form-control @error('password') is-invalid @enderror" name="password" required placeholder="Minimal 8 karakter">
                            </div>
                            @error('password')
                                <div class="invalid-feedback d-block mt-1">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6">
                            <label for="password_confirmation" class="form-label fw-semibold small">Ulangi Password <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <span class="input-group-text bg-light"><i class="bi bi-lock-fill"></i></span>
                                <input id="password_confirmation" type="password" class="form-control" name="password_confirmation" required placeholder="Ulangi password">
                            </div>
                        </div>
                    </div>

                    <div class="alert alert-light border small text-muted mb-4 p-3 rounded-3">
                        <div class="d-flex align-items-center gap-2">
                            <i class="bi bi-shield-check fs-5 text-success"></i>
                            <div>
                                <strong class="d-block text-dark">Penetapan Harga Otomatis:</strong>
                                Setelah mendaftar dan masuk, seluruh katalog produk akan langsung menampilkan harga sesuai kategori akun Anda (Harga Umum, Harga Grosir Toko, atau Harga Kafe).
                            </div>
                        </div>
                    </div>

                    <button type="submit" class="btn btn-warning btn-lg w-100 fw-bold text-dark shadow-sm py-2">
                        <i class="bi bi-check-circle me-1"></i> Daftar Sekarang &amp; Buka Akun
                    </button>
                </form>

                <div class="text-center mt-4 pt-3 border-top">
                    <small class="text-muted">Sudah memiliki akun? <a href="{{ route('login') }}" class="text-decoration-none fw-semibold text-warning">Masuk di sini</a></small>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    function toggleBusinessFields() {
        const type = document.querySelector('input[name="customer_type"]:checked')?.value || 'retail';
        const container = document.getElementById('businessNameContainer');
        const label = document.getElementById('businessLabelText');
        const input = document.getElementById('business_name');

        if (type === 'retail') {
            container.classList.add('d-none');
            input.removeAttribute('required');
        } else {
            container.classList.remove('d-none');
            input.setAttribute('required', 'required');
            if (type === 'toko') {
                label.textContent = 'Nama Toko / Warung / Usaha Kelontong';
                input.placeholder = 'Contoh: Toko Berkah / Warung Kopi Madura';
            } else if (type === 'kafe') {
                label.textContent = 'Nama Kafe / Kedai Kopi / Resto';
                input.placeholder = 'Contoh: Kafe Kopi Senja / Sejiwa Coffee';
            }
        }

        // Highlight selected card
        document.querySelectorAll('.account-type-card').forEach(card => {
            const radio = card.querySelector('input[type="radio"]');
            if (radio && radio.checked) {
                card.style.borderColor = '#f59e0b';
                card.style.backgroundColor = '#fffbeb';
            } else {
                card.style.borderColor = '#e2e8f0';
                card.style.backgroundColor = '#ffffff';
            }
        });
    }

    document.addEventListener('DOMContentLoaded', toggleBusinessFields);
</script>
@endpush
@endsection
