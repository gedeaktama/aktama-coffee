@extends('layouts.app')

@section('title', 'Detail PO ' . $purchase->purchase_number . ' - Kopi Distro')

@section('content')
<div class="container py-3">
    <!-- Breadcrumb & Top Actions -->
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-4">
        <div>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-1">
                    <li class="breadcrumb-item"><a href="{{ route('purchases.index') }}" class="text-decoration-none">Pembelian</a></li>
                    <li class="breadcrumb-item active" aria-current="page">{{ $purchase->purchase_number }}</li>
                </ol>
            </nav>
            <div class="d-flex align-items-center gap-3">
                <h2 class="fw-bold text-dark font-monospace mb-0">{{ $purchase->purchase_number }}</h2>
                @if($purchase->status === \App\Models\Purchase::STATUS_ORDERED)
                    <span class="badge bg-warning text-dark px-3 py-2 rounded-pill fs-6">
                        <i class="bi bi-clock-history me-1"></i> Menunggu Penerimaan Fisik
                    </span>
                @elseif($purchase->status === \App\Models\Purchase::STATUS_RECEIVED)
                    <span class="badge bg-success px-3 py-2 rounded-pill fs-6">
                        <i class="bi bi-check-circle-fill me-1"></i> Barang Diterima Fisik (Stok Masuk)
                    </span>
                @elseif($purchase->status === \App\Models\Purchase::STATUS_CANCELLED)
                    <span class="badge bg-secondary px-3 py-2 rounded-pill fs-6">
                        <i class="bi bi-x-circle me-1"></i> Dibatalkan
                    </span>
                @endif
            </div>
        </div>

        <div class="d-flex gap-2">
            <button type="button" class="btn btn-outline-secondary" onclick="window.print()">
                <i class="bi bi-printer me-1"></i> Cetak PO
            </button>
            <a href="{{ route('purchases.index') }}" class="btn btn-outline-coffee">
                <i class="bi bi-arrow-left me-1"></i> Daftar PO
            </a>
        </div>
    </div>

    <!-- Status Notice Banner -->
    @if($purchase->status === \App\Models\Purchase::STATUS_ORDERED)
        <div class="card bg-warning bg-opacity-10 border-warning border-opacity-50 p-3 mb-4">
            <div class="d-flex align-items-start gap-3">
                <i class="bi bi-exclamation-triangle-fill text-warning fs-3 mt-1"></i>
                <div class="flex-grow-1">
                    <h6 class="fw-bold text-dark mb-1">Pesanan Sedang Dipesan & Menunggu Kedatangan Fisik</h6>
                    <p class="small text-dark mb-2">
                        Sesuai kaidah pengendalian persediaan, saldo stok di sistem <strong>belum bertambah</strong>. 
                        Setelah tim gudang menerima dan memeriksa kelayakan fisik barang dari supplier, klik tombol 
                        <strong>"Konfirmasi Penerimaan Fisik Barang"</strong> untuk menambahkan saldo stok secara otomatis ke sistem.
                    </p>
                    @if(auth()->user()->hasRole(['owner', 'admin', 'warehouse']))
                        <div class="d-flex gap-2 mt-2">
                            <form method="POST" action="{{ route('purchases.receive', $purchase) }}" onsubmit="return confirm('Konfirmasi penerimaan barang fisik untuk PO {{ $purchase->purchase_number }}? Saldo stok seluruh item akan langsung bertambah ke sistem gudang.');">
                                @csrf
                                <button type="submit" class="btn btn-success btn-sm px-3 shadow-sm">
                                    <i class="bi bi-box-seam me-1"></i> Konfirmasi Penerimaan Fisik Barang (Tambah Stok)
                                </button>
                            </form>

                            @if(auth()->user()->hasRole(['owner', 'admin']))
                                <form method="POST" action="{{ route('purchases.cancel', $purchase) }}" onsubmit="return confirm('Apakah Anda yakin ingin membatalkan pesanan PO {{ $purchase->purchase_number }}?');">
                                    @csrf
                                    <button type="submit" class="btn btn-outline-danger btn-sm">
                                        <i class="bi bi-x-circle me-1"></i> Batalkan PO
                                    </button>
                                </form>
                            @endif
                        </div>
                    @endif
                </div>
            </div>
        </div>
    @elseif($purchase->status === \App\Models\Purchase::STATUS_RECEIVED)
        <div class="card bg-success bg-opacity-10 border-success border-opacity-25 p-3 mb-4">
            <div class="d-flex align-items-center gap-3">
                <i class="bi bi-check-circle-fill text-success fs-3"></i>
                <div>
                    <h6 class="fw-bold text-success mb-1">Barang Telah Diterima Fisik di Gudang</h6>
                    <p class="small text-muted mb-0">
                        Dikonfirmasi oleh <strong>{{ $purchase->receivedBy->name ?? 'Staf Gudang' }}</strong> 
                        pada <strong>{{ $purchase->received_at ? $purchase->received_at->translatedFormat('d F Y, H:i') : '-' }} WIB</strong>. 
                        Kuantitas telah bertambah dan tercatat pada Kartu Mutasi Stok.
                    </p>
                </div>
            </div>
        </div>
    @endif

    <div class="row g-4">
        <!-- Supplier & Metadata -->
        <div class="col-lg-4">
            <!-- Supplier Info Card -->
            <div class="card card-custom p-4 mb-4">
                <h6 class="fw-bold text-dark mb-3 pb-2 border-bottom">
                    <i class="bi bi-truck me-2 text-info"></i> Informasi Pemasok / Supplier
                </h6>
                <div class="mb-2">
                    <small class="text-muted d-block">Nama Supplier</small>
                    <span class="fw-bold text-dark fs-6">{{ $purchase->supplier->name }}</span>
                </div>
                <div class="mb-2">
                    <small class="text-muted d-block">Person In Charge (PIC)</small>
                    <span class="fw-semibold">{{ $purchase->supplier->contact_person ?? '-' }}</span>
                </div>
                <div class="mb-2">
                    <small class="text-muted d-block">Nomor Telepon / WhatsApp</small>
                    <span class="fw-semibold">{{ $purchase->supplier->phone ?? '-' }}</span>
                </div>
                <div class="mb-2">
                    <small class="text-muted d-block">Email</small>
                    <span class="fw-semibold">{{ $purchase->supplier->email ?? '-' }}</span>
                </div>
                <div>
                    <small class="text-muted d-block">Alamat</small>
                    <p class="small mb-0 text-secondary">{{ $purchase->supplier->address ?? '-' }}</p>
                </div>
            </div>

            <!-- Order Metadata Card -->
            <div class="card card-custom p-4">
                <h6 class="fw-bold text-dark mb-3 pb-2 border-bottom">
                    <i class="bi bi-info-circle me-2 text-secondary"></i> Rincian Dokumen
                </h6>
                <div class="mb-2">
                    <small class="text-muted d-block">Tanggal Pemesanan PO</small>
                    <span class="fw-semibold">{{ $purchase->purchase_date->translatedFormat('d F Y') }}</span>
                </div>
                <div class="mb-2">
                    <small class="text-muted d-block">Diterbitkan Oleh (Pembuat PO)</small>
                    <span class="fw-semibold">{{ $purchase->user->name ?? '-' }}</span>
                    <span class="badge badge-role badge-role-{{ $purchase->user->role ?? 'admin' }} scale-75 ms-1">
                        {{ $purchase->user->role ?? '-' }}
                    </span>
                </div>
                @if($purchase->received_at)
                    <div class="mb-2">
                        <small class="text-muted d-block">Diterima Fisik Oleh</small>
                        <span class="fw-semibold text-success">{{ $purchase->receivedBy->name ?? '-' }}</span>
                        <small class="d-block text-muted">{{ $purchase->received_at->translatedFormat('d M Y, H:i') }} WIB</small>
                    </div>
                @endif
                <div>
                    <small class="text-muted d-block">Catatan Dokumen</small>
                    <p class="small text-secondary mb-0 fst-italic">{{ $purchase->notes ?? 'Tidak ada catatan khusus.' }}</p>
                </div>
            </div>
        </div>

        <!-- Order Items & Calculation -->
        <div class="col-lg-8">
            <div class="card card-custom p-4">
                <h5 class="fw-bold text-dark mb-3 pb-2 border-bottom">
                    <i class="bi bi-bag-check me-2 text-primary"></i> Daftar Item Kopi yang Dipesan
                </h5>

                <div class="table-responsive mb-4">
                    <table class="table table-bordered align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th style="width: 50px;" class="text-center">No</th>
                                <th>Produk & Varian SKU</th>
                                <th style="width: 120px;" class="text-center">Qty Dipesan</th>
                                <th style="width: 170px;" class="text-end">Harga Satuan</th>
                                <th style="width: 170px;" class="text-end">Subtotal</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($purchase->items as $index => $item)
                                <tr>
                                    <td class="text-center text-muted">{{ $index + 1 }}</td>
                                    <td>
                                        <div class="fw-bold text-dark font-monospace small">{{ $item->variant->sku }}</div>
                                        <div class="fw-semibold text-coffee">{{ $item->variant->product->name }} &mdash; {{ $item->variant->variant_name }}</div>
                                        <small class="text-muted">
                                            Berat: {{ $item->variant->package_weight_grams }}g &middot; 
                                            Bentuk: {{ ucfirst($item->variant->grind_form) }} &middot; 
                                            Stok Sekarang di Gudang: <strong>{{ $item->variant->current_stock }} pack</strong>
                                        </small>
                                    </td>
                                    <td class="text-center fw-bold fs-6">
                                        {{ $item->quantity }} <small class="text-muted fw-normal">pack</small>
                                    </td>
                                    <td class="text-end text-secondary">
                                        {{ $item->formatted_cost }}
                                    </td>
                                    <td class="text-end fw-bold text-dark">
                                        {{ $item->formatted_subtotal }}
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                        <tfoot class="table-light">
                            <tr>
                                <td colspan="4" class="text-end fw-semibold">Subtotal Item:</td>
                                <td class="text-end fw-semibold">Rp {{ number_format($purchase->subtotal, 0, ',', '.') }}</td>
                            </tr>
                            <tr>
                                <td colspan="4" class="text-end text-muted small">Potongan Diskon:</td>
                                <td class="text-end text-danger small">- Rp {{ number_format($purchase->discount, 0, ',', '.') }}</td>
                            </tr>
                            <tr>
                                <td colspan="4" class="text-end text-muted small">Biaya Kirim / Logistik:</td>
                                <td class="text-end text-secondary small">+ Rp {{ number_format($purchase->shipping_cost, 0, ',', '.') }}</td>
                            </tr>
                            <tr class="table-active">
                                <td colspan="4" class="text-end fw-bold fs-5 text-dark">TOTAL PEMBELIAN:</td>
                                <td class="text-end fw-bold fs-5 text-success">{{ $purchase->formatted_total }}</td>
                            </tr>
                        </tfoot>
                    </table>
                </div>

                <div class="d-flex justify-content-between align-items-center pt-3 border-top">
                    <a href="{{ route('purchases.index') }}" class="btn btn-outline-secondary">
                        <i class="bi bi-arrow-left me-1"></i> Kembali ke Daftar PO
                    </a>

                    @if($purchase->status === \App\Models\Purchase::STATUS_ORDERED && auth()->user()->hasRole(['owner', 'admin', 'warehouse']))
                        <form method="POST" action="{{ route('purchases.receive', $purchase) }}" onsubmit="return confirm('Konfirmasi penerimaan barang fisik untuk PO {{ $purchase->purchase_number }}? Saldo stok seluruh item akan langsung bertambah ke sistem gudang.');">
                            @csrf
                            <button type="submit" class="btn btn-success shadow-sm">
                                <i class="bi bi-check-circle me-1"></i> Terima Fisik Barang & Tambah Stok
                            </button>
                        </form>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
