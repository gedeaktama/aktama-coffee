@extends('layouts.app')

@section('title', 'Faktur Penjualan ' . $invoice->invoice_number . ' - Kopi Distro')

@section('content')
<div class="container py-3">
    <!-- Breadcrumb & Top Actions -->
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-4">
        <div>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-1">
                    <li class="breadcrumb-item"><a href="{{ route('finance.invoices.index') }}" class="text-decoration-none">Faktur Penjualan</a></li>
                    <li class="breadcrumb-item active" aria-current="page">{{ $invoice->invoice_number }}</li>
                </ol>
            </nav>
            <div class="d-flex align-items-center gap-2 flex-wrap">
                <h2 class="fw-bold text-dark font-monospace mb-0">{{ $invoice->invoice_number }}</h2>
                <span class="badge {{ $invoice->status_badge_class }} px-3 py-1 fs-6 rounded-pill">
                    {{ $invoice->status_label }}
                </span>
                <span class="badge bg-light text-dark border px-3 py-1 fs-6 rounded-pill font-monospace">
                    Order: {{ $invoice->order->order_number }}
                </span>
            </div>
        </div>

        <div class="d-flex gap-2">
            <button type="button" class="btn btn-outline-secondary" onclick="window.print()">
                <i class="bi bi-printer me-1"></i> Cetak Faktur
            </button>
            <a href="{{ route('finance.invoices.index') }}" class="btn btn-outline-coffee">
                <i class="bi bi-arrow-left me-1"></i> Daftar Faktur
            </a>
        </div>
    </div>

    <!-- Overdue Banner Alert -->
    @if($invoice->isOverdue())
        <div class="alert alert-danger shadow-sm border-0 d-flex align-items-center mb-4" role="alert">
            <i class="bi bi-exclamation-triangle-fill fs-3 me-3 text-danger"></i>
            <div>
                <h6 class="fw-bold mb-1">Tagihan Faktur Telah Melewati Tanggal Jatuh Tempo!</h6>
                <small class="mb-0">
                    Faktur ini jatuh tempo pada <strong>{{ $invoice->due_date->translatedFormat('d F Y') }}</strong> 
                    (terlambat <strong>{{ $invoice->days_overdue }} hari</strong>). Segera lakukan penagihan kepada pelanggan untuk memulihkan plafon kredit mereka.
                </small>
            </div>
        </div>
    @endif

    <div class="row g-4">
        <!-- Left Column: Customer & Invoice Meta -->
        <div class="col-lg-4">
            <!-- Customer Card -->
            <div class="card card-custom p-4 mb-4">
                <h6 class="fw-bold text-dark mb-3 pb-2 border-bottom">
                    <i class="bi bi-building me-2 text-primary"></i> Data Pelanggan / Mitra
                </h6>
                @if($invoice->customer)
                    <div class="mb-2">
                        <small class="text-muted d-block">Nama Usaha / Mitra</small>
                        <span class="fw-bold text-dark fs-6">{{ $invoice->customer->business_name }}</span>
                        <span class="badge bg-light text-secondary border ms-1">{{ $invoice->customer->type_label }}</span>
                    </div>
                    <div class="mb-2">
                        <small class="text-muted d-block">Penanggung Jawab (PIC)</small>
                        <span class="fw-semibold">{{ $invoice->customer->pic_name }}</span>
                    </div>
                    <div class="mb-2">
                        <small class="text-muted d-block">Telepon / WhatsApp</small>
                        <span class="fw-semibold">{{ $invoice->customer->phone }}</span>
                    </div>
                    <div class="mb-2">
                        <small class="text-muted d-block">Alamat Penagihan</small>
                        <p class="small text-secondary mb-0">{{ $invoice->customer->address ?? '-' }}</p>
                    </div>
                    <div class="border-top pt-2 mt-2">
                        <a href="{{ route('finance.receivables.ledger', $invoice->customer) }}" class="btn btn-sm btn-outline-coffee w-100">
                            <i class="bi bi-journal-text me-1"></i> Buka Kartu Piutang Mitra
                        </a>
                    </div>
                @else
                    <div class="mb-2">
                        <small class="text-muted d-block">Nama Pembeli Retail</small>
                        <span class="fw-bold text-dark fs-6">{{ $invoice->order->recipient_name ?? '-' }}</span>
                    </div>
                    <div class="mb-2">
                        <small class="text-muted d-block">Kontak Penerima</small>
                        <span class="fw-semibold">{{ $invoice->order->recipient_phone ?? '-' }}</span>
                    </div>
                @endif
            </div>

            <!-- Invoice Summary Balance Card -->
            <div class="card card-custom p-4 mb-4">
                <h6 class="fw-bold text-dark mb-3 pb-2 border-bottom">
                    <i class="bi bi-cash-coin me-2 text-warning"></i> Ringkasan Saldo Tagihan
                </h6>
                <div class="d-flex justify-content-between mb-2">
                    <span class="text-muted">Total Tagihan Faktur:</span>
                    <strong class="text-dark">{{ $invoice->formatted_total }}</strong>
                </div>
                <div class="d-flex justify-content-between mb-2">
                    <span class="text-muted">Total Telah Dibayar:</span>
                    <strong class="text-success">{{ $invoice->formatted_paid }}</strong>
                </div>
                <hr>
                <div class="d-flex justify-content-between align-items-center">
                    <span class="fw-bold text-dark">Sisa Tagihan:</span>
                    <h4 class="fw-bold mb-0 {{ $invoice->remaining_amount > 0 ? 'text-danger' : 'text-success' }}">
                        {{ $invoice->formatted_remaining }}
                    </h4>
                </div>

                @if($invoice->remaining_amount > 0 && auth()->user()->hasRole(['owner', 'admin', 'finance']))
                    <div class="mt-3 pt-3 border-top">
                        <button type="button" class="btn btn-success w-100 shadow-sm" data-bs-toggle="modal" data-bs-target="#modalRecordPayment">
                            <i class="bi bi-plus-circle me-1"></i> Rekam Pembayaran Baru
                        </button>
                    </div>
                @endif
            </div>

            <!-- Due Date & Issuer Meta -->
            <div class="card card-custom p-4">
                <h6 class="fw-bold text-dark mb-3 pb-2 border-bottom">
                    <i class="bi bi-info-circle me-2 text-secondary"></i> Rincian Dokumen
                </h6>
                <div class="mb-2">
                    <small class="text-muted d-block">Tanggal Terbit Faktur</small>
                    <span class="fw-semibold">{{ $invoice->invoice_date->translatedFormat('d F Y') }}</span>
                </div>
                <div class="mb-2">
                    <small class="text-muted d-block">Jatuh Tempo Pembayaran</small>
                    <span class="fw-bold {{ $invoice->isOverdue() ? 'text-danger' : 'text-dark' }}">
                        {{ $invoice->due_date ? $invoice->due_date->translatedFormat('d F Y') : '-' }}
                    </span>
                </div>
                <div class="mb-2">
                    <small class="text-muted d-block">Diterbitkan Oleh</small>
                    <span class="small fw-semibold">{{ $invoice->user->name ?? 'Sistem Keuangan' }}</span>
                </div>
                @if($invoice->notes)
                    <div class="border-top pt-2 mt-2">
                        <small class="text-muted d-block">Catatan Faktur</small>
                        <p class="small text-secondary mb-0 fst-italic">{{ $invoice->notes }}</p>
                    </div>
                @endif
            </div>
        </div>

        <!-- Right Column: Order Items & Payment History -->
        <div class="col-lg-8">
            <!-- Items Table -->
            <div class="card card-custom p-4 mb-4">
                <h5 class="fw-bold text-dark mb-3 pb-2 border-bottom">
                    <i class="bi bi-bag-check me-2 text-primary"></i> Rincian Barang yang Ditagihkan
                </h5>

                <div class="table-responsive">
                    <table class="table table-bordered align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th style="width: 50px;" class="text-center">No</th>
                                <th>Produk & Varian SKU</th>
                                <th style="width: 120px;" class="text-center">Kuantitas</th>
                                <th style="width: 170px;" class="text-end">Harga Satuan</th>
                                <th style="width: 170px;" class="text-end">Subtotal</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($invoice->order->items as $index => $item)
                                <tr>
                                    <td class="text-center text-muted">{{ $index + 1 }}</td>
                                    <td>
                                        <div class="fw-bold text-dark font-monospace small">{{ $item->variant->sku }}</div>
                                        <div class="fw-semibold text-coffee">{{ $item->variant->product->name }} &mdash; {{ $item->variant->variant_name }}</div>
                                        <small class="text-muted">Berat: {{ $item->variant->package_weight_grams }}g</small>
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
                                <td class="text-end fw-semibold">{{ $invoice->order->formatted_subtotal }}</td>
                            </tr>
                            @if($invoice->order->discount > 0)
                                <tr>
                                    <td colspan="4" class="text-end text-muted small">Potongan Diskon:</td>
                                    <td class="text-end text-danger small">- Rp {{ number_format($invoice->order->discount, 0, ',', '.') }}</td>
                                </tr>
                            @endif
                            @if($invoice->order->shipping_cost > 0)
                                <tr>
                                    <td colspan="4" class="text-end text-muted small">Biaya Kirim Logistik:</td>
                                    <td class="text-end text-secondary small">+ Rp {{ number_format($invoice->order->shipping_cost, 0, ',', '.') }}</td>
                                </tr>
                            @endif
                            <tr class="table-active">
                                <td colspan="4" class="text-end fw-bold fs-5 text-dark">TOTAL FAKTUR:</td>
                                <td class="text-end fw-bold fs-5 text-success">{{ $invoice->formatted_total }}</td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>

            <!-- Payment History Table -->
            <div class="card card-custom p-4">
                <div class="d-flex justify-content-between align-items-center mb-3 pb-2 border-bottom">
                    <h5 class="fw-bold text-dark mb-0">
                        <i class="bi bi-clock-history me-2 text-success"></i> Riwayat Pembayaran Masuk
                    </h5>
                    @if($invoice->remaining_amount > 0 && auth()->user()->hasRole(['owner', 'admin', 'finance']))
                        <button type="button" class="btn btn-sm btn-outline-success" data-bs-toggle="modal" data-bs-target="#modalRecordPayment">
                            <i class="bi bi-plus-circle me-1"></i> Bayar
                        </button>
                    @endif
                </div>

                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>No. Kuitansi</th>
                                <th>Tanggal</th>
                                <th>Metode Bayar</th>
                                <th class="text-end">Jumlah Bayar</th>
                                <th>No. Bukti / Ref</th>
                                <th>Petugas Finance</th>
                                <th class="text-end">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($invoice->payments as $pay)
                                <tr>
                                    <td class="font-monospace fw-bold text-dark">
                                        {{ $pay->payment_number }}
                                    </td>
                                    <td class="small text-muted">
                                        {{ $pay->payment_date->format('d/m/Y') }}
                                    </td>
                                    <td>
                                        <span class="badge bg-light text-dark border">{{ $pay->method_label }}</span>
                                    </td>
                                    <td class="text-end fw-bold text-success">
                                        {{ $pay->formatted_amount }}
                                    </td>
                                    <td class="small text-secondary">
                                        {{ $pay->reference_number ?? '-' }}
                                    </td>
                                    <td class="small text-dark">
                                        {{ $pay->user->name ?? 'Finance' }}
                                    </td>
                                    <td class="text-end">
                                        <a href="{{ route('finance.payments.receipt', $pay) }}" class="btn btn-sm btn-outline-secondary" target="_blank" title="Cetak Kuitansi Pembayaran">
                                            <i class="bi bi-printer me-1"></i> Kuitansi
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="text-center py-4 text-muted">
                                        <i class="bi bi-cash-stack fs-2 text-secondary opacity-50 mb-2 d-block"></i>
                                        <span class="small">Belum ada pembayaran yang dicatat untuk faktur ini.</span>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal Rekam Pembayaran -->
<div class="modal fade" id="modalRecordPayment" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <form method="POST" action="{{ route('finance.payments.record', $invoice) }}">
                @csrf
                <div class="modal-header bg-light">
                    <h5 class="modal-title fw-bold text-dark">
                        <i class="bi bi-cash-coin me-2 text-success"></i> Catat Pembayaran Faktur
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>

                <div class="modal-body p-4">
                    <div class="p-3 bg-light rounded-3 border mb-3">
                        <div class="d-flex justify-content-between small mb-1">
                            <span class="text-muted">No. Faktur:</span>
                            <strong class="font-monospace text-dark">{{ $invoice->invoice_number }}</strong>
                        </div>
                        <div class="d-flex justify-content-between small mb-1">
                            <span class="text-muted">Total Tagihan:</span>
                            <span class="text-dark fw-semibold">{{ $invoice->formatted_total }}</span>
                        </div>
                        <div class="d-flex justify-content-between small border-top pt-1 mt-1">
                            <span class="text-muted">Sisa yang Wajib Dibayar:</span>
                            <strong class="text-danger fs-6">{{ $invoice->formatted_remaining }}</strong>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="amount" class="form-label fw-semibold">Jumlah Pembayaran (Rp) <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <span class="input-group-text">Rp</span>
                            <input type="number" name="amount" id="amount" class="form-control form-control-lg fw-bold text-end" value="{{ old('amount', $invoice->remaining_amount) }}" min="100" max="{{ $invoice->remaining_amount }}" step="100" required>
                        </div>
                        <div class="d-flex justify-content-end mt-1">
                            <button type="button" class="btn btn-sm btn-link text-decoration-none p-0" onclick="document.getElementById('amount').value = '{{ (int)$invoice->remaining_amount }}'">
                                Bayar Lunas Penuh (Rp {{ number_format($invoice->remaining_amount, 0, ',', '.') }})
                            </button>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="payment_method" class="form-label fw-semibold">Metode Pembayaran <span class="text-danger">*</span></label>
                        <select name="payment_method" id="payment_method" class="form-select" required>
                            @foreach(\App\Models\Payment::METHODS as $mKey => $mLbl)
                                <option value="{{ $mKey }}" {{ old('payment_method') === $mKey ? 'selected' : '' }}>{{ $mLbl }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="mb-3">
                        <label for="payment_date" class="form-label fw-semibold">Tanggal Pembayaran <span class="text-danger">*</span></label>
                        <input type="date" name="payment_date" id="payment_date" class="form-control" value="{{ old('payment_date', date('Y-m-d')) }}" required>
                    </div>

                    <div class="mb-3">
                        <label for="reference_number" class="form-label fw-semibold">Nomor Bukti Transfer / Referensi Bank</label>
                        <input type="text" name="reference_number" id="reference_number" class="form-control" value="{{ old('reference_number') }}" placeholder="Contoh: TRF-BCA-20261011-889">
                        <small class="text-muted">Nomor referensi mutasi rekening untuk keperluan rekonsiliasi audit.</small>
                    </div>

                    <div class="mb-3">
                        <label for="notes" class="form-label fw-semibold">Catatan Pembayaran</label>
                        <input type="text" name="notes" id="notes" class="form-control" value="{{ old('notes') }}" placeholder="Contoh: Pembayaran cicilan tahap 1 via mobile banking">
                    </div>
                </div>

                <div class="modal-footer bg-light">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-success px-4">
                        <i class="bi bi-check2-circle me-1"></i> Simpan & Rekam Pembayaran
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
