@extends('layouts.app')

@section('title', 'Tambah Pelanggan Bisnis - Kopi Distro')

@section('content')
<div class="container py-3">
    <!-- Breadcrumb & Header -->
    <div class="mb-4">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-1">
                <li class="breadcrumb-item"><a href="{{ route('dashboard') }}" class="text-decoration-none">Dashboard</a></li>
                <li class="breadcrumb-item"><a href="{{ route('customers.index') }}" class="text-decoration-none">Master Pelanggan</a></li>
                <li class="breadcrumb-item active">Tambah Pelanggan</li>
            </ol>
        </nav>
        <h3 class="fw-bold mb-0 text-dark">
            <i class="bi bi-person-plus-fill me-2 text-warning"></i> Tambah Pelanggan Bisnis (Kafe / Toko / Reseller)
        </h3>
    </div>

    <form method="POST" action="{{ route('customers.store') }}">
        @csrf

        <div class="row g-4">
            <!-- Entitas & Kontak -->
            <div class="col-lg-7">
                <div class="card card-custom p-4 h-100">
                    <h5 class="fw-bold mb-3 pb-2 border-bottom text-dark">
                        <i class="bi bi-buildings me-2 text-primary"></i> 1. Profil Usaha & Kontak
                    </h5>

                    <div class="row">
                        <div class="col-md-5 mb-3">
                            <label class="form-label fw-semibold">Kode Pelanggan <span class="text-danger">*</span></label>
                            <input type="text" name="code" class="form-control @error('code') is-invalid @enderror" value="{{ old('code') }}" placeholder="CUST-KAFE-002" required>
                            @error('code') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <div class="col-md-7 mb-3">
                            <label class="form-label fw-semibold">Tipe Pelanggan <span class="text-danger">*</span></label>
                            <select name="customer_type" class="form-select @error('customer_type') is-invalid @enderror" required>
                                <option value="kafe" {{ old('customer_type') == 'kafe' ? 'selected' : '' }}>Kafe / Kedai Kopi</option>
                                <option value="toko" {{ old('customer_type') == 'toko' ? 'selected' : '' }}>Toko Kecil & Kelontong</option>
                                <option value="konsinyasi" {{ old('customer_type') == 'konsinyasi' ? 'selected' : '' }}>Mitra Titip Jual (Konsinyasi)</option>
                                <option value="reseller" {{ old('customer_type') == 'reseller' ? 'selected' : '' }}>Reseller Kopi</option>
                                <option value="retail" {{ old('customer_type') == 'retail' ? 'selected' : '' }}>Konsumen Retail</option>
                            </select>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-semibold">Jaringan / Brand Toko</label>
                            <select name="store_chain" class="form-select">
                                <option value="lainnya">Lainnya / Mitra Bebas</option>
                                <option value="alfamidi" {{ old('store_chain') == 'alfamidi' ? 'selected' : '' }}>Alfamidi</option>
                                <option value="alfamart" {{ old('store_chain') == 'alfamart' ? 'selected' : '' }}>Alfamart</option>
                                <option value="indomaret" {{ old('store_chain') == 'indomaret' ? 'selected' : '' }}>Indomaret</option>
                                <option value="kelontong" {{ old('store_chain') == 'kelontong' ? 'selected' : '' }}>Toko Kelontong / Grosir</option>
                                <option value="kafe" {{ old('store_chain') == 'kafe' ? 'selected' : '' }}>Kafe / Kedai Kopi</option>
                            </select>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold">Nama Usaha / Nama Entitas <span class="text-danger">*</span></label>
                        <input type="text" name="business_name" class="form-control @error('business_name') is-invalid @enderror" value="{{ old('business_name') }}" placeholder="Contoh: Alfamart Hayam Wuruk / Kopi Kulo" required>
                        @error('business_name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-semibold">Penanggung Jawab (PIC) <span class="text-danger">*</span></label>
                            <input type="text" name="pic_name" class="form-control @error('pic_name') is-invalid @enderror" value="{{ old('pic_name') }}" placeholder="Nama kepala toko / manager" required>
                            @error('pic_name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-semibold">Nomor Telepon / WhatsApp <span class="text-danger">*</span></label>
                            <input type="text" name="phone" class="form-control @error('phone') is-invalid @enderror" value="{{ old('phone') }}" placeholder="08123456789" required>
                            @error('phone') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold">Alamat Email</label>
                        <input type="email" name="email" class="form-control" value="{{ old('email') }}" placeholder="usaha@example.com">
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-semibold">Alamat Penagihan</label>
                            <textarea name="address" class="form-control" rows="2" placeholder="Alamat kantor / outlet utama...">{{ old('address') }}</textarea>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-semibold">Alamat Pengiriman Barang</label>
                            <textarea name="shipping_address" class="form-control" rows="2" placeholder="Alamat tujuan pengiriman...">{{ old('shipping_address') }}</textarea>
                        </div>
                    </div>

                    <!-- Koordinat Lokasi GPS Toko & Batas Stok Aman -->
                    <div class="p-3 bg-light rounded-3 mb-3 border">
                        <div class="d-flex align-items-center gap-1 mb-2">
                            <i class="bi bi-geo-alt-fill text-danger"></i>
                            <strong class="small text-dark">Koordinat GPS Peta & Batas Stok</strong>
                        </div>
                        <div class="row g-2">
                            <div class="col-md-4">
                                <label class="form-label small text-muted mb-1">Latitude</label>
                                <input type="text" name="latitude" class="form-control form-control-sm" value="{{ old('latitude') }}" placeholder="-8.653400">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label small text-muted mb-1">Longitude</label>
                                <input type="text" name="longitude" class="form-control form-control-sm" value="{{ old('longitude') }}" placeholder="115.228500">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label small text-muted mb-1">Batas Minimal Stok</label>
                                <input type="number" name="min_stock_alert" class="form-control form-control-sm" value="{{ old('min_stock_alert', 5) }}" min="1">
                            </div>
                        </div>
                        <small class="text-muted mt-1 d-block" style="font-size: 0.72rem;">
                            Koordinat GPS digunakan untuk Peta Toko, Peta Rute Sopir, dan validasi kehadiran (anti-fraud).
                        </small>
                    </div>
                </div>
            </div>

            <!-- Kredit & Penugasan Sales -->
            <div class="col-lg-5">
                <div class="card card-custom p-4 h-100">
                    <h5 class="fw-bold mb-3 pb-2 border-bottom text-dark">
                        <i class="bi bi-credit-card me-2 text-success"></i> 2. Kebijakan Kredit & Sales PIC
                    </h5>

                    <div class="mb-3">
                        <label class="form-label fw-semibold">Sales Penanggung Jawab</label>
                        <select name="sales_user_id" class="form-select form-select-searchable" data-placeholder="-- Cari Sales PIC (Nama / Email) --">
                            <option value="">-- Tanpa Sales / Mandiri --</option>
                            @foreach($salesList as $sales)
                                <option value="{{ $sales->id }}" data-role="Sales" data-email="{{ $sales->email }}" {{ old('sales_user_id') == $sales->id ? 'selected' : '' }}>
                                    {{ $sales->name }} ({{ $sales->email }})
                                </option>
                            @endforeach
                        </select>
                        <small class="text-muted" style="font-size: 0.72rem;">Sales yang mengelola akun pelanggan bisnis ini</small>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold">Batas Plafon Kredit (Rp)</label>
                        <div class="input-group">
                            <span class="input-group-text">Rp</span>
                            <input type="number" step="1000" name="credit_limit" class="form-control @error('credit_limit') is-invalid @enderror" value="{{ old('credit_limit', 0) }}" min="0">
                        </div>
                        <small class="text-muted" style="font-size: 0.72rem;">Isi 0 jika transaksi wajib tunai / transfer langsung</small>
                        @error('credit_limit') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold">Termin Jatuh Tempo Pembayaran (Hari)</label>
                        <select name="payment_terms_days" class="form-select">
                            <option value="0" {{ old('payment_terms_days', 0) == 0 ? 'selected' : '' }}>0 Hari (Tunai / Bayar Langsung)</option>
                            <option value="7" {{ old('payment_terms_days') == 7 ? 'selected' : '' }}>7 Hari (1 Minggu)</option>
                            <option value="14" {{ old('payment_terms_days') == 14 ? 'selected' : '' }}>14 Hari (2 Minggu)</option>
                            <option value="30" {{ old('payment_terms_days') == 30 ? 'selected' : '' }}>30 Hari (1 Bulan)</option>
                        </select>
                        <small class="text-muted" style="font-size: 0.72rem;">Jatuh tempo faktur penjualan tempo</small>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold">Catatan Khusus</label>
                        <textarea name="notes" class="form-control" rows="4" placeholder="Preferensi biji kopi, jadwal pengiriman mingguan, dll...">{{ old('notes') }}</textarea>
                    </div>
                </div>
            </div>
        </div>

        <div class="d-flex justify-content-end gap-2 mt-4">
            <a href="{{ route('customers.index') }}" class="btn btn-light px-4">Batal</a>
            <button type="submit" class="btn btn-coffee px-5">
                <i class="bi bi-check-circle me-1"></i> Simpan Data Pelanggan
            </button>
        </div>
    </form>
</div>
@endsection
