@extends('layouts.app')

@section('title', 'Master Pelanggan Grosir & Retail - Kopi Distro')

@section('content')
<div class="container py-3">
    <!-- Breadcrumb & Header -->
    <div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-4">
        <div>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-1">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}" class="text-decoration-none">Dashboard</a></li>
                    <li class="breadcrumb-item active">Master Pelanggan</li>
                </ol>
            </nav>
            <h3 class="fw-bold mb-0 text-dark">
                <i class="bi bi-people-fill me-2 text-warning"></i> Data Pelanggan Grosir (B2B) & Retail
            </h3>
        </div>
        <a href="{{ route('customers.create') }}" class="btn btn-coffee">
            <i class="bi bi-person-plus-fill me-1"></i> Tambah Pelanggan Bisnis
        </a>
    </div>

    <!-- Filter Bar & Tabs -->
    <div class="card card-custom p-3 mb-4">
        <div class="d-flex flex-wrap gap-2 mb-3 pb-3 border-bottom">
            <a href="{{ route('customers.index') }}" class="btn btn-sm {{ !request('type') ? 'btn-dark' : 'btn-outline-secondary' }}">
                Semua Pelanggan
            </a>
            <a href="{{ route('customers.index', ['type' => 'kafe']) }}" class="btn btn-sm {{ request('type') == 'kafe' ? 'btn-dark' : 'btn-outline-secondary' }}">
                <i class="bi bi-cup-hot me-1 text-warning"></i> Kafe & Kedai Kopi
            </a>
            <a href="{{ route('customers.index', ['type' => 'toko']) }}" class="btn btn-sm {{ request('type') == 'toko' ? 'btn-dark' : 'btn-outline-secondary' }}">
                <i class="bi bi-shop me-1 text-primary"></i> Toko Kelontong
            </a>
            <a href="{{ route('customers.index', ['type' => 'reseller']) }}" class="btn btn-sm {{ request('type') == 'reseller' ? 'btn-dark' : 'btn-outline-secondary' }}">
                <i class="bi bi-box-seam me-1 text-success"></i> Reseller Kopi
            </a>
            <a href="{{ route('customers.index', ['type' => 'retail']) }}" class="btn btn-sm {{ request('type') == 'retail' ? 'btn-dark' : 'btn-outline-secondary' }}">
                <i class="bi bi-person me-1 text-info"></i> Konsumen Retail
            </a>
        </div>

        <form method="GET" action="{{ route('customers.index') }}" class="row g-2 align-items-center">
            @if(request('type'))
                <input type="hidden" name="type" value="{{ request('type') }}">
            @endif
            <div class="col-md-9">
                <div class="input-group">
                    <span class="input-group-text bg-light"><i class="bi bi-search"></i></span>
                    <input type="text" name="search" class="form-control" placeholder="Cari kode pelanggan, nama usaha, PIC, atau nomor telepon..." value="{{ request('search') }}">
                </div>
            </div>
            <div class="col-md-3 d-flex gap-2">
                <button type="submit" class="btn btn-dark w-100"><i class="bi bi-search"></i> Cari</button>
                @if(request('search'))
                    <a href="{{ route('customers.index', request()->only('type')) }}" class="btn btn-outline-secondary" title="Reset Cari"><i class="bi bi-x-lg"></i></a>
                @endif
            </div>
        </form>
    </div>

    <!-- Customers Table -->
    <div class="card card-custom p-0 overflow-hidden">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th class="ps-4">Kode Pelanggan</th>
                        <th>Tipe</th>
                        <th>Nama Usaha & Penanggung Jawab</th>
                        <th>Telepon / WhatsApp</th>
                        <th class="text-end">Plafon Kredit</th>
                        <th class="text-center">Termin Tempo</th>
                        <th>Sales PIC</th>
                        <th class="text-center">Status</th>
                        <th class="text-end pe-4">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($customers as $customer)
                        <tr>
                            <td class="ps-4">
                                <code class="fw-bold">{{ $customer->code }}</code>
                            </td>
                            <td>
                                @if($customer->customer_type == 'kafe')
                                    <span class="badge bg-warning bg-opacity-25 text-dark border border-warning">Kafe</span>
                                @elseif($customer->customer_type == 'toko')
                                    <span class="badge bg-primary bg-opacity-10 text-primary border border-primary border-opacity-25">Toko</span>
                                @elseif($customer->customer_type == 'reseller')
                                    <span class="badge bg-success bg-opacity-10 text-success border border-success border-opacity-25">Reseller</span>
                                @else
                                    <span class="badge bg-secondary bg-opacity-10 text-secondary border">Retail</span>
                                @endif
                            </td>
                            <td>
                                <div class="fw-bold text-dark fs-6">{{ $customer->business_name }}</div>
                                <small class="text-muted"><i class="bi bi-person me-1"></i>PIC: {{ $customer->pic_name }}</small>
                            </td>
                            <td>
                                <div><i class="bi bi-whatsapp text-success me-1"></i>{{ $customer->phone }}</div>
                            </td>
                            <td class="text-end">
                                @if($customer->credit_limit > 0)
                                    <strong class="text-primary">{{ $customer->formatted_credit_limit }}</strong>
                                @else
                                    <span class="text-muted small">Tanpa Plafon (Rp 0)</span>
                                @endif
                            </td>
                            <td class="text-center">
                                @if($customer->payment_terms_days > 0)
                                    <span class="badge bg-info bg-opacity-10 text-info border border-info border-opacity-25">
                                        {{ $customer->payment_terms_days }} Hari
                                    </span>
                                @else
                                    <span class="badge bg-light text-muted border">Tunai / Langsung</span>
                                @endif
                            </td>
                            <td>
                                @if($customer->salesUser)
                                    <small class="badge bg-light text-dark border">
                                        <i class="bi bi-person-badge text-success me-1"></i>{{ $customer->salesUser->name }}
                                    </small>
                                @else
                                    <small class="text-muted">-</small>
                                @endif
                            </td>
                            <td class="text-center">
                                @if($customer->is_active)
                                    <span class="badge bg-success bg-opacity-10 text-success border border-success border-opacity-25">Aktif</span>
                                @else
                                    <span class="badge bg-danger bg-opacity-10 text-danger border border-danger border-opacity-25">Nonaktif</span>
                                @endif
                            </td>
                            <td class="text-end pe-4">
                                <a href="{{ route('driver.print_qr', $customer) }}" target="_blank" class="btn btn-sm btn-outline-warning text-dark me-1" title="Cetak Stiker QR Code Toko">
                                    <i class="bi bi-qr-code"></i>
                                </a>
                                @if($customer->google_maps_url)
                                    <a href="{{ $customer->google_maps_url }}" target="_blank" class="btn btn-sm btn-outline-info text-dark me-1" title="Buka Rute di Google Maps">
                                        <i class="bi bi-compass"></i>
                                    </a>
                                @endif
                                <a href="{{ route('customers.show', $customer) }}" class="btn btn-sm btn-outline-dark me-1" title="Lihat Profil">
                                    <i class="bi bi-eye"></i>
                                </a>
                                <a href="{{ route('customers.edit', $customer) }}" class="btn btn-sm btn-outline-primary me-1" title="Edit Pelanggan">
                                    <i class="bi bi-pencil"></i>
                                </a>
                                <form action="{{ route('customers.toggle', $customer) }}" method="POST" class="d-inline">
                                    @csrf
                                    @method('PATCH')
                                    <button type="submit" class="btn btn-sm {{ $customer->is_active ? 'btn-outline-danger' : 'btn-outline-success' }}" 
                                        title="{{ $customer->is_active ? 'Nonaktifkan' : 'Aktifkan' }}"
                                        onclick="return confirm('Ubah status pelanggan ini?')">
                                        <i class="bi {{ $customer->is_active ? 'bi-slash-circle' : 'bi-check-circle' }}"></i>
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="text-center py-5 text-muted">
                                <i class="bi bi-person-x fs-1 text-muted d-block mb-2"></i>
                                Tidak ada data pelanggan yang sesuai kriteria.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($customers->hasPages())
            <div class="p-3 border-top">
                {{ $customers->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
