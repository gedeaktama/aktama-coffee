@extends('layouts.app')

@section('title', 'Edit Pelanggan: ' . $customer->business_name . ' - Kopi Distro')

@section('content')
<div class="container py-3">
    <!-- Breadcrumb & Header -->
    <div class="mb-4">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-1">
                <li class="breadcrumb-item"><a href="{{ route('dashboard') }}" class="text-decoration-none">Dashboard</a></li>
                <li class="breadcrumb-item"><a href="{{ route('customers.index') }}" class="text-decoration-none">Master Pelanggan</a></li>
                <li class="breadcrumb-item"><a href="{{ route('customers.show', $customer) }}" class="text-decoration-none">{{ $customer->business_name }}</a></li>
                <li class="breadcrumb-item active">Edit</li>
            </ol>
        </nav>
        <h3 class="fw-bold mb-0 text-dark">
            <i class="bi bi-pencil-square me-2 text-primary"></i> Edit Data Pelanggan: {{ $customer->business_name }}
        </h3>
    </div>

    <form method="POST" action="{{ route('customers.update', $customer) }}">
        @csrf
        @method('PUT')

        <div class="row g-4">
            <!-- Entitas & Kontak -->
            <div class="col-lg-7">
                <div class="card card-custom p-4 h-100">
                    <h5 class="fw-bold mb-3 pb-2 border-bottom text-dark">
                        <i class="bi bi-buildings me-2 text-primary"></i> 1. Profil Usaha & Kontak
                    </h5>

                    <div class="row">
                        <div class="col-md-5 mb-3">
                            <label class="form-label fw-semibold">Kode Pelanggan</label>
                            <input type="text" class="form-control bg-light" value="{{ $customer->code }}" disabled>
                            <small class="text-muted" style="font-size: 0.72rem;">Kode unik tidak dapat diubah</small>
                        </div>

                        <div class="col-md-7 mb-3">
                            <label class="form-label fw-semibold">Tipe Pelanggan <span class="text-danger">*</span></label>
                            <select name="customer_type" class="form-select" required>
                                <option value="kafe" {{ old('customer_type', $customer->customer_type) == 'kafe' ? 'selected' : '' }}>Kafe / Kedai Kopi</option>
                                <option value="toko" {{ old('customer_type', $customer->customer_type) == 'toko' ? 'selected' : '' }}>Toko Kecil & Kelontong</option>
                                <option value="reseller" {{ old('customer_type', $customer->customer_type) == 'reseller' ? 'selected' : '' }}>Reseller Kopi</option>
                                <option value="retail" {{ old('customer_type', $customer->customer_type) == 'retail' ? 'selected' : '' }}>Konsumen Retail</option>
                            </select>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold">Nama Usaha / Nama Entitas <span class="text-danger">*</span></label>
                        <input type="text" name="business_name" class="form-control" value="{{ old('business_name', $customer->business_name) }}" required>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-semibold">Penanggung Jawab (PIC) <span class="text-danger">*</span></label>
                            <input type="text" name="pic_name" class="form-control" value="{{ old('pic_name', $customer->pic_name) }}" required>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-semibold">Nomor Telepon / WhatsApp <span class="text-danger">*</span></label>
                            <input type="text" name="phone" class="form-control" value="{{ old('phone', $customer->phone) }}" required>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold">Alamat Email</label>
                        <input type="email" name="email" class="form-control" value="{{ old('email', $customer->email) }}">
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold">Alamat Penagihan</label>
                        <textarea name="address" class="form-control" rows="2">{{ old('address', $customer->address) }}</textarea>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold">Alamat Pengiriman Barang</label>
                        <textarea name="shipping_address" class="form-control" rows="2">{{ old('shipping_address', $customer->shipping_address) }}</textarea>
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
                        <select name="sales_user_id" class="form-select">
                            <option value="">-- Tanpa Sales / Mandiri --</option>
                            @foreach($salesList as $sales)
                                <option value="{{ $sales->id }}" {{ old('sales_user_id', $customer->sales_user_id) == $sales->id ? 'selected' : '' }}>
                                    {{ $sales->name }} ({{ $sales->email }})
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold">Batas Plafon Kredit (Rp)</label>
                        <div class="input-group">
                            <span class="input-group-text">Rp</span>
                            <input type="number" step="1000" name="credit_limit" class="form-control @error('credit_limit') is-invalid @enderror" value="{{ old('credit_limit', (int)$customer->credit_limit) }}" min="0">
                        </div>
                        @error('credit_limit') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold">Termin Jatuh Tempo (Hari)</label>
                        <select name="payment_terms_days" class="form-select">
                            <option value="0" {{ old('payment_terms_days', $customer->payment_terms_days) == 0 ? 'selected' : '' }}>0 Hari (Tunai / Bayar Langsung)</option>
                            <option value="7" {{ old('payment_terms_days', $customer->payment_terms_days) == 7 ? 'selected' : '' }}>7 Hari</option>
                            <option value="14" {{ old('payment_terms_days', $customer->payment_terms_days) == 14 ? 'selected' : '' }}>14 Hari</option>
                            <option value="30" {{ old('payment_terms_days', $customer->payment_terms_days) == 30 ? 'selected' : '' }}>30 Hari</option>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold">Status Pelanggan</label>
                        <select name="is_active" class="form-select">
                            <option value="1" {{ old('is_active', $customer->is_active) ? 'selected' : '' }}>Aktif</option>
                            <option value="0" {{ !old('is_active', $customer->is_active) ? 'selected' : '' }}>Nonaktif</option>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold">Catatan Khusus</label>
                        <textarea name="notes" class="form-control" rows="3">{{ old('notes', $customer->notes) }}</textarea>
                    </div>
                </div>
            </div>
        </div>

        <div class="d-flex justify-content-end gap-2 mt-4">
            <a href="{{ route('customers.show', $customer) }}" class="btn btn-light px-4">Batal</a>
            <button type="submit" class="btn btn-coffee px-5">Simpan Perubahan</button>
        </div>
    </form>
</div>
@endsection
