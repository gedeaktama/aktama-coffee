@extends('layouts.app')

@section('title', 'Detail Pesanan ' . $order->order_number . ' - Kopi Distro')

@section('content')
<div class="container py-3">
    <!-- Breadcrumb & Top Actions -->
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-4">
        <div>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-1">
                    <li class="breadcrumb-item"><a href="{{ route('orders.index') }}" class="text-decoration-none">Penjualan</a></li>
                    <li class="breadcrumb-item active" aria-current="page">{{ $order->order_number }}</li>
                </ol>
            </nav>
            <div class="d-flex align-items-center gap-2 flex-wrap">
                <h2 class="fw-bold text-dark font-monospace mb-0">{{ $order->order_number }}</h2>
                @if($order->isB2B())
                    <span class="badge bg-primary bg-opacity-10 text-primary border border-primary border-opacity-25 px-2 py-1">
                        <i class="bi bi-building me-1"></i> B2B Grosir
                    </span>
                @else
                    <span class="badge bg-info bg-opacity-10 text-info border border-info border-opacity-25 px-2 py-1">
                        <i class="bi bi-cart3 me-1"></i> Retail Online
                    </span>
                @endif
                <span class="badge {{ $order->status_badge_class }} px-3 py-1 fs-6 rounded-pill">
                    {{ $order->status_label }}
                </span>
                <span class="badge {{ $order->payment_status_badge_class }} px-3 py-1 fs-6 rounded-pill">
                    {{ $order->payment_status_label }}
                </span>
            </div>
        </div>

        <div class="d-flex gap-2">
            <button type="button" class="btn btn-outline-secondary" onclick="window.print()">
                <i class="bi bi-printer me-1"></i> Cetak Dokumen
            </button>
            <a href="{{ route('orders.index') }}" class="btn btn-outline-coffee">
                <i class="bi bi-arrow-left me-1"></i> Daftar Order
            </a>
        </div>
    </div>

    <!-- Status Action Banners -->
    @if($order->isPending())
        <div class="card bg-warning bg-opacity-10 border-warning border-opacity-50 p-3 mb-4">
            <div class="d-flex align-items-start gap-3">
                <i class="bi bi-exclamation-triangle-fill text-warning fs-3 mt-1"></i>
                <div class="flex-grow-1">
                    <h6 class="fw-bold text-dark mb-1">Pesanan Menunggu Konfirmasi & Pemotongan Stok</h6>
                    <p class="small text-dark mb-2">
                        Pesanan ini baru diterbitkan dan saldo persediaan di gudang <strong>belum dipotong</strong>. 
                        Klik tombol <strong>"Konfirmasi Pesanan"</strong> untuk memotong saldo stok varian kopi secara otomatis menggunakan transaksi pesimistis anti-stok minus.
                    </p>
                    <div class="d-flex gap-2">
                        @if(auth()->user()->hasRole(['owner', 'admin', 'warehouse', 'sales']))
                            <form method="POST" action="{{ route('orders.confirm', $order) }}" onsubmit="return confirm('Konfirmasi pesanan {{ $order->order_number }}? Saldo stok fisik seluruh item kopi akan langsung dipotong dari gudang.');">
                                @csrf
                                <button type="submit" class="btn btn-success btn-sm px-3 shadow-sm">
                                    <i class="bi bi-check2-circle me-1"></i> Konfirmasi Pesanan & Potong Stok
                                </button>
                            </form>
                        @endif

                        @if(auth()->user()->hasRole(['owner', 'admin', 'sales']))
                            <form method="POST" action="{{ route('orders.cancel', $order) }}" onsubmit="return confirm('Apakah Anda yakin ingin membatalkan pesanan {{ $order->order_number }}?');">
                                @csrf
                                <button type="submit" class="btn btn-outline-danger btn-sm">
                                    <i class="bi bi-x-circle me-1"></i> Batalkan Pesanan
                                </button>
                            </form>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    @elseif($order->isConfirmed())
        <div class="card bg-success bg-opacity-10 border-success border-opacity-25 p-3 mb-4">
            <div class="d-flex align-items-center justify-content-between flex-wrap gap-2">
                <div class="d-flex align-items-center gap-3">
                    <i class="bi bi-check-circle-fill text-success fs-3"></i>
                    <div>
                        <h6 class="fw-bold text-success mb-1">Pesanan Telah Dikonfirmasi</h6>
                        <small class="text-muted">
                            Dikonfirmasi oleh <strong>{{ $order->confirmedBy->name ?? 'Staf' }}</strong> 
                            pada <strong>{{ $order->confirmed_at ? $order->confirmed_at->translatedFormat('d F Y, H:i') : '-' }} WIB</strong>. 
                            Stok fisik di gudang telah berhasil dipotong dan dicatat pada Kartu Mutasi.
                        </small>
                    </div>
                </div>

                @if(auth()->user()->hasRole(['owner', 'admin', 'sales']))
                    <form method="POST" action="{{ route('orders.cancel', $order) }}" onsubmit="return confirm('PERINGATAN: Membatalkan pesanan yang telah dikonfirmasi akan MENGEMBALIKAN saldo stok seluruh item kopi kembali ke gudang. Lanjutkan pembatalan?');">
                        @csrf
                        <button type="submit" class="btn btn-outline-danger btn-sm">
                            <i class="bi bi-x-circle me-1"></i> Batalkan & Kembalikan Stok
                        </button>
                    </form>
                @endif
            </div>
        </div>
    @elseif($order->isCancelled())
        <div class="card bg-danger bg-opacity-10 border-danger border-opacity-25 p-3 mb-4">
            <div class="d-flex align-items-center gap-3">
                <i class="bi bi-x-circle-fill text-danger fs-3"></i>
                <div>
                    <h6 class="fw-bold text-danger mb-0">Pesanan Telah Dibatalkan</h6>
                    <small class="text-muted">Transaksi ini tidak berlaku dan seluruh saldo stok telah dikembalikan ke gudang penyimpanan.</small>
                </div>
            </div>
        </div>
    @endif

    <div class="row g-4">
        <!-- Customer & Order Meta -->
        <div class="col-lg-4">
            <!-- Customer Info Card -->
            <div class="card card-custom p-4 mb-4">
                <h6 class="fw-bold text-dark mb-3 pb-2 border-bottom">
                    <i class="bi bi-building me-2 text-primary"></i> Informasi Pelanggan
                </h6>
                @if($order->customer)
                    <div class="mb-2">
                        <small class="text-muted d-block">Nama Usaha / Bisnis</small>
                        <span class="fw-bold text-dark fs-6">{{ $order->customer->business_name }}</span>
                        <span class="badge bg-light text-secondary border ms-1">{{ $order->customer->type_label }}</span>
                    </div>
                    <div class="mb-2">
                        <small class="text-muted d-block">Nama PIC</small>
                        <span class="fw-semibold">{{ $order->customer->pic_name }}</span>
                    </div>
                    <div class="mb-2">
                        <small class="text-muted d-block">Telepon / WhatsApp</small>
                        <span class="fw-semibold">{{ $order->customer->phone }}</span>
                    </div>
                    @if($order->customer->hasCreditFacility())
                        <div class="p-2 rounded bg-light border small mt-2">
                            <div class="d-flex justify-content-between">
                                <span class="text-muted">Plafon Kredit:</span>
                                <strong>{{ $order->customer->formatted_credit_limit }}</strong>
                            </div>
                            <div class="d-flex justify-content-between">
                                <span class="text-muted">Termin Tempo:</span>
                                <strong class="text-primary">{{ $order->customer->payment_terms_days }} Hari</strong>
                            </div>
                        </div>
                    @endif
                @else
                    <div class="mb-2">
                        <small class="text-muted d-block">Nama Pembeli Retail</small>
                        <span class="fw-bold text-dark fs-6">{{ $order->recipient_name ?? '-' }}</span>
                    </div>
                    <div class="mb-2">
                        <small class="text-muted d-block">Kontak Pembeli</small>
                        <span class="fw-semibold">{{ $order->recipient_phone ?? '-' }}</span>
                    </div>
                @endif
            </div>

            <!-- Shipping & Meta Card -->
            <div class="card card-custom p-4">
                <h6 class="fw-bold text-dark mb-3 pb-2 border-bottom">
                    <i class="bi bi-truck me-2 text-info"></i> Pengiriman & Penagihan
                </h6>
                <div class="mb-2">
                    <small class="text-muted d-block">Tanggal Order</small>
                    <span class="fw-semibold">{{ $order->order_date->translatedFormat('d F Y') }}</span>
                </div>
                <div class="mb-2">
                    <small class="text-muted d-block">Metode Pembayaran</small>
                    <span class="fw-semibold">{{ $order->payment_type_label }}</span>
                </div>
                @if($order->due_date)
                    <div class="mb-2">
                        <small class="text-muted d-block">Jatuh Tempo Pembayaran (TOP)</small>
                        <span class="fw-bold text-danger">{{ $order->due_date->translatedFormat('d F Y') }}</span>
                    </div>
                @endif
                <div class="mb-2">
                    <small class="text-muted d-block">Alamat Pengiriman</small>
                    <p class="small text-secondary mb-0">{{ $order->shipping_address ?? '-' }}</p>
                </div>
                <div class="mb-2">
                    <small class="text-muted d-block">Sales PIC / Pembuat Order</small>
                    <span class="small fw-semibold">{{ $order->salesUser->name ?? $order->user->name ?? 'Sistem' }}</span>
                </div>
                @if($order->notes)
                    <div class="border-top pt-2 mt-2">
                        <small class="text-muted d-block">Catatan Khusus</small>
                        <p class="small fst-italic text-secondary mb-0">{{ $order->notes }}</p>
                    </div>
                @endif
            </div>
        </div>

        <!-- Order Items & Calculation -->
        <div class="col-lg-8">
            <div class="card card-custom p-4">
                <h5 class="fw-bold text-dark mb-3 pb-2 border-bottom">
                    <i class="bi bi-bag-check me-2 text-success"></i> Rincian Item Kopi Terjual
                </h5>

                <div class="table-responsive mb-4">
                    <table class="table table-bordered align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th style="width: 50px;" class="text-center">No</th>
                                <th>Produk & Varian SKU</th>
                                <th style="width: 120px;" class="text-center">Jumlah (Pack)</th>
                                <th style="width: 170px;" class="text-end">Harga Satuan</th>
                                <th style="width: 170px;" class="text-end">Subtotal</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($order->items as $index => $item)
                                <tr>
                                    <td class="text-center text-muted">{{ $index + 1 }}</td>
                                    <td>
                                        <div class="fw-bold text-dark font-monospace small">{{ $item->variant->sku }}</div>
                                        <div class="fw-semibold text-coffee">{{ $item->variant->product->name }} &mdash; {{ $item->variant->variant_name }}</div>
                                        <small class="text-muted">
                                            Berat: {{ $item->variant->package_weight_grams }}g &middot; 
                                            Bentuk: {{ ucfirst($item->variant->grind_form) }}
                                        </small>
                                    </td>
                                    <td class="text-center fw-bold fs-6">
                                        {{ $item->quantity }} <small class="text-muted fw-normal">pack</small>
                                    </td>
                                    <td class="text-end text-secondary">
                                        {{ $item->formatted_price }}
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
                                <td class="text-end fw-semibold">{{ $order->formatted_subtotal }}</td>
                            </tr>
                            @if($order->discount > 0)
                                <tr>
                                    <td colspan="4" class="text-end text-muted small">Potongan Diskon:</td>
                                    <td class="text-end text-danger small">- Rp {{ number_format($order->discount, 0, ',', '.') }}</td>
                                </tr>
                            @endif
                            @if($order->shipping_cost > 0)
                                <tr>
                                    <td colspan="4" class="text-end text-muted small">Biaya Kirim Logistik:</td>
                                    <td class="text-end text-secondary small">+ Rp {{ number_format($order->shipping_cost, 0, ',', '.') }}</td>
                                </tr>
                            @endif
                            <tr class="table-active">
                                <td colspan="4" class="text-end fw-bold fs-5 text-dark">TOTAL TAGIHAN:</td>
                                <td class="text-end fw-bold fs-5 text-success">{{ $order->formatted_total }}</td>
                            </tr>
                        </tfoot>
                    </table>
                </div>

                <div class="d-flex justify-content-between align-items-center pt-3 border-top">
                    <a href="{{ route('orders.index') }}" class="btn btn-outline-secondary">
                        <i class="bi bi-arrow-left me-1"></i> Kembali ke Daftar Order
                    </a>

                    @if($order->isPending() && auth()->user()->hasRole(['owner', 'admin', 'warehouse', 'sales']))
                        <form method="POST" action="{{ route('orders.confirm', $order) }}" onsubmit="return confirm('Konfirmasi pesanan {{ $order->order_number }}? Saldo stok fisik akan dipotong dari gudang.');">
                            @csrf
                            <button type="submit" class="btn btn-success shadow-sm">
                                <i class="bi bi-check2-circle me-1"></i> Konfirmasi Pesanan & Potong Stok
                            </button>
                        </form>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
