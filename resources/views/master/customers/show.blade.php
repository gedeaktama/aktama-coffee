@extends('layouts.app')

@section('title', $customer->business_name . ' - Profil Pelanggan - Kopi Distro')

@section('content')
<div class="container py-3">
    <!-- Breadcrumb & Header -->
    <div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-4">
        <div>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-1">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}" class="text-decoration-none">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('customers.index') }}" class="text-decoration-none">Master Pelanggan</a></li>
                    <li class="breadcrumb-item active">{{ $customer->business_name }}</li>
                </ol>
            </nav>
            <h3 class="fw-bold mb-0 text-dark">{{ $customer->business_name }}</h3>
        </div>
        <div class="d-flex flex-wrap gap-2">
            <a href="{{ route('driver.print_qr', $customer) }}" target="_blank" class="btn btn-warning fw-bold text-dark">
                <i class="bi bi-qr-code me-1"></i> Cetak Stiker QR Toko
            </a>
            @if($customer->google_maps_url)
                <a href="{{ $customer->google_maps_url }}" target="_blank" class="btn btn-outline-primary">
                    <i class="bi bi-compass me-1"></i> Rute Maps
                </a>
            @endif
            <a href="{{ route('customers.edit', $customer) }}" class="btn btn-outline-secondary">
                <i class="bi bi-pencil me-1"></i> Edit
            </a>
            <a href="{{ route('customers.index') }}" class="btn btn-light border">
                <i class="bi bi-arrow-left me-1"></i> Kembali
            </a>
        </div>
    </div>

    <div class="row g-4">
        <!-- Main Customer Info -->
        <div class="col-lg-7">
            <div class="card card-custom p-4 mb-4">
                <div class="d-flex align-items-center justify-content-between mb-3 pb-2 border-bottom">
                    <div class="d-flex align-items-center gap-2">
                        <span class="badge bg-dark fs-6">{{ $customer->code }}</span>
                        <span class="badge bg-warning bg-opacity-25 text-dark border border-warning fs-6">
                            {{ $customer->type_label }}
                        </span>
                    </div>
                    @if($customer->is_active)
                        <span class="badge bg-success bg-opacity-10 text-success border border-success border-opacity-25">Aktif</span>
                    @else
                        <span class="badge bg-danger bg-opacity-10 text-danger border border-danger border-opacity-25">Nonaktif</span>
                    @endif
                </div>

                <div class="row g-3">
                    <div class="col-md-6">
                        <small class="text-muted d-block">Nama Usaha / Kafe</small>
                        <span class="fw-bold fs-5 text-dark">{{ $customer->business_name }}</span>
                    </div>
                    <div class="col-md-6">
                        <small class="text-muted d-block">Penanggung Jawab (PIC)</small>
                        <span class="fw-bold fs-5 text-dark">{{ $customer->pic_name }}</span>
                    </div>
                    <div class="col-md-6">
                        <small class="text-muted d-block">Kontak Telepon / WhatsApp</small>
                        <span class="fw-semibold"><i class="bi bi-telephone text-success me-1"></i>{{ $customer->phone }}</span>
                    </div>
                    <div class="col-md-6">
                        <small class="text-muted d-block">Email</small>
                        <span class="fw-semibold">{{ $customer->email ?: '-' }}</span>
                    </div>
                    <div class="col-12">
                        <small class="text-muted d-block">Alamat Penagihan</small>
                        <p class="mb-0 text-secondary">{{ $customer->address ?: '-' }}</p>
                    </div>
                    <div class="col-12">
                        <small class="text-muted d-block">Alamat Pengiriman Barang</small>
                        <p class="mb-0 text-secondary">{{ $customer->shipping_address ?: ($customer->address ?: '-') }}</p>
                    </div>
                    <div class="col-12">
                        <small class="text-muted d-block">Catatan Khusus</small>
                        <p class="mb-0 text-secondary italic">{{ $customer->notes ?: 'Tidak ada catatan.' }}</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Credit & Sales Account Info -->
        <div class="col-lg-5">
            <div class="card card-custom p-4 mb-4">
                <h5 class="fw-bold mb-3 pb-2 border-bottom text-dark">
                    <i class="bi bi-wallet2 me-2 text-success"></i> Fasilitas Kredit & Piutang
                </h5>

                <div class="p-3 rounded-3 bg-light border mb-3">
                    <small class="text-muted d-block">Batas Plafon Kredit (Credit Limit)</small>
                    <span class="fw-bold fs-4 text-primary">{{ $customer->formatted_credit_limit }}</span>
                    <small class="text-muted d-block mt-1">
                        @if($customer->credit_limit > 0)
                            <i class="bi bi-shield-check text-success me-1"></i> Memiliki fasilitas tempo kredit
                        @else
                            <i class="bi bi-info-circle text-secondary me-1"></i> Hanya transaksi tunai / langsung
                        @endif
                    </small>
                </div>

                <div class="mb-3">
                    <small class="text-muted d-block">Termin Jatuh Tempo (Terms of Payment)</small>
                    <span class="fw-bold text-dark fs-6">
                        @if($customer->payment_terms_days > 0)
                            <i class="bi bi-calendar-event text-info me-1"></i> {{ $customer->payment_terms_days }} Hari setelah faktur terbit
                        @else
                            <i class="bi bi-cash-stack text-success me-1"></i> Bayar Langsung / Tunai
                        @endif
                    </span>
                </div>

                <div class="mb-3">
                    <small class="text-muted d-block">Sales Penanggung Jawab (Account Exec)</small>
                    @if($customer->salesUser)
                        <div class="d-flex align-items-center gap-2 mt-1">
                            <span class="badge rounded-circle bg-success text-white p-2"><i class="bi bi-person"></i></span>
                            <div>
                                <span class="fw-bold d-block">{{ $customer->salesUser->name }}</span>
                                <small class="text-muted">{{ $customer->salesUser->email }}</small>
                            </div>
                        </div>
                    @else
                        <span class="text-muted small">Belum ditetapkan / Akun mandiri</span>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
