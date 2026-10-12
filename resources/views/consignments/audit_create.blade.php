@extends('layouts.app')

@section('title', 'Survei Mingguan Konsinyasi — ' . $consignment->customer->business_name)

@push('styles')
<style>
    .calc-row {
        transition: background-color 0.2s ease;
    }
    .calc-row:hover {
        background-color: #fffbeb !important;
    }
    .highlight-sold {
        background-color: #ecfdf5;
        color: #065f46;
        font-weight: 700;
        font-size: 1.1rem;
        padding: 0.35rem 0.65rem;
        border-radius: 8px;
        display: inline-block;
        border: 1px solid #a7f3d0;
    }
</style>
@endpush

@section('content')
<div class="container py-3">
    <!-- Breadcrumb & Header -->
    <div class="mb-4">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-1">
                <li class="breadcrumb-item"><a href="{{ route('dashboard') }}" class="text-decoration-none">Dashboard</a></li>
                <li class="breadcrumb-item"><a href="{{ route('consignments.index') }}" class="text-decoration-none">Konsinyasi</a></li>
                <li class="breadcrumb-item"><a href="{{ route('consignments.show', $consignment) }}" class="text-decoration-none">{{ $consignment->consignment_number }}</a></li>
                <li class="breadcrumb-item active">Survei Mingguan</li>
            </ol>
        </nav>
        <div class="d-flex flex-wrap align-items-center justify-content-between gap-3">
            <div>
                <h3 class="fw-bold mb-0 text-dark">
                    <i class="bi bi-clipboard2-check text-warning me-2"></i> Survei &amp; Tagih Produk Laku
                </h3>
                <p class="text-muted small mb-0">Hitung sisa fisik kopi di rak toko mitra. Sistem akan otomatis menghitung jumlah produk yang laku dan total uang yang wajib disetor.</p>
            </div>
            <a href="{{ route('consignments.show', $consignment) }}" class="btn btn-outline-secondary">
                <i class="bi bi-arrow-left me-1"></i> Batal / Kembali
            </a>
        </div>
    </div>

    @if($errors->any())
        <div class="alert alert-danger alert-dismissible fade show mb-4">
            <h6 class="fw-bold mb-1"><i class="bi bi-exclamation-triangle-fill me-1"></i> Terjadi Kesalahan Input:</h6>
            <ul class="mb-0 small">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <!-- Kartu Informasi Toko Mitra -->
    <div class="card card-custom p-3 p-md-4 shadow-sm mb-4 border-0" style="background: linear-gradient(135deg, #1e130c 0%, #2e1a11 100%); color: #ffffff;">
        <div class="row align-items-center g-3">
            <div class="col-md-8">
                <span class="badge bg-warning text-dark fw-bold px-3 py-1 rounded-pill mb-2">SURVEI MINGGUAN KONSINYASI</span>
                <h4 class="fw-bold mb-1 text-white">{{ $consignment->customer->business_name }}</h4>
                <p class="text-white-50 small mb-0">
                    <i class="bi bi-geo-alt me-1 text-warning"></i> {{ $consignment->customer->shipping_address ?: $consignment->customer->address }} &bull; 
                    <i class="bi bi-person me-1 text-warning"></i> PIC: {{ $consignment->customer->pic_name }} ({{ $consignment->customer->phone }})
                </p>
            </div>
            <div class="col-md-4 text-md-end">
                <small class="text-white-50 d-block">Surat Titip Awal:</small>
                <code class="text-warning fs-5 fw-bold">{{ $consignment->consignment_number }}</code>
            </div>
        </div>
    </div>

    <form method="POST" action="{{ route('consignments.audit.store', $consignment) }}" id="formAudit">
        @csrf

        <!-- Tabel Perhitungan Otomatis Sisa vs Laku -->
        <div class="card card-custom p-0 overflow-hidden shadow-sm mb-4">
            <div class="p-3 bg-light border-bottom d-flex justify-content-between align-items-center">
                <h5 class="fw-bold text-dark mb-0">
                    <i class="bi bi-calculator me-2 text-warning"></i> Cek Fisik Rak &amp; Hitung Otomatis Produk Laku
                </h5>
                <span class="small text-muted d-none d-md-inline"><i class="bi bi-info-circle me-1"></i> Ketik sisa fisik yang ada di rak sekarang</span>
            </div>

            <div class="table-responsive">
                <table class="table align-middle mb-0" id="auditTable">
                    <thead class="table-light">
                        <tr>
                            <th class="ps-4" style="min-width: 240px;">Produk Kopi &amp; Varian</th>
                            <th class="text-center" style="width: 120px;">Stok Awal Titip</th>
                            <th class="text-center bg-warning bg-opacity-10" style="width: 140px;">Sisa Fisik di Rak <span class="text-danger">*</span></th>
                            <th class="text-center" style="width: 120px;">Retur Rusak</th>
                            <th class="text-center bg-success bg-opacity-10" style="width: 140px;">Laku Terjual</th>
                            <th class="text-end" style="width: 130px;">Harga Satuan</th>
                            <th class="text-end bg-success bg-opacity-10" style="width: 160px;">Subtotal Tagihan</th>
                            <th class="text-center" style="width: 140px;">Titip Baru (Restock)</th>
                            <th class="text-center pe-4" style="width: 130px;">Stok Baru di Toko</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($consignment->items as $index => $item)
                            <tr class="calc-row" data-index="{{ $index }}">
                                <td class="ps-4">
                                    <input type="hidden" name="items[{{ $index }}][item_id]" value="{{ $item->id }}">
                                    <div class="d-flex align-items-center gap-2">
                                        <img src="{{ $item->variant->product->image_url }}" alt="{{ $item->variant->product->name }}" class="rounded-2 border bg-light p-1" style="width: 40px; height: 40px; object-fit: contain;">
                                        <div>
                                            <div class="fw-bold text-dark fs-6">{{ $item->variant->product->name }}</div>
                                            <small class="text-muted"><code class="fw-bold">{{ $item->variant->sku }}</code> ({{ $item->variant->package_weight_grams }}g)</small>
                                        </div>
                                    </div>
                                </td>

                                <!-- Stok Tercatat Sebelumnya -->
                                <td class="text-center">
                                    <span class="badge bg-light text-dark border fs-6 stock-before-badge" data-stock-before="{{ $item->current_quantity }}">
                                        {{ $item->current_quantity }} pack
                                    </span>
                                </td>

                                <!-- Input Sisa Fisik di Rak Toko -->
                                <td class="text-center bg-warning bg-opacity-10">
                                    <input type="number" 
                                           name="items[{{ $index }}][physical_remaining]" 
                                           class="form-control text-center fw-bold fs-6 input-physical border-warning" 
                                           value="{{ old('items.' . $index . '.physical_remaining', $item->current_quantity) }}" 
                                           min="0" 
                                           max="{{ $item->current_quantity }}" 
                                           required 
                                           oninput="recalcRow(this)">
                                    <small class="text-muted d-block" style="font-size: 0.72rem;">maks: {{ $item->current_quantity }}</small>
                                </td>

                                <!-- Input Retur Rusak / Expired -->
                                <td class="text-center">
                                    <input type="number" 
                                           name="items[{{ $index }}][quantity_returned]" 
                                           class="form-control form-control-sm text-center input-returned" 
                                           value="{{ old('items.' . $index . '.quantity_returned', 0) }}" 
                                           min="0" 
                                           max="{{ $item->current_quantity }}" 
                                           oninput="recalcRow(this)">
                                </td>

                                <!-- Produk yang LAKU (Otomatis) -->
                                <td class="text-center bg-success bg-opacity-10">
                                    <span class="highlight-sold display-sold">0 pack</span>
                                </td>

                                <!-- Harga Satuan -->
                                <td class="text-end text-muted font-monospace unit-price-display" data-price="{{ $item->selling_price }}">
                                    {{ $item->formatted_price }}
                                </td>

                                <!-- Subtotal Tagihan (Otomatis) -->
                                <td class="text-end bg-success bg-opacity-10 fw-bold fs-6 text-dark subtotal-display">
                                    Rp 0
                                </td>

                                <!-- Restock Baru Tambahan -->
                                <td class="text-center">
                                    <input type="number" 
                                           name="items[{{ $index }}][restock_quantity]" 
                                           class="form-control form-control-sm text-center input-restock border-info" 
                                           value="{{ old('items.' . $index . '.restock_quantity', 0) }}" 
                                           min="0" 
                                           placeholder="+0" 
                                           oninput="recalcRow(this)">
                                    <small class="text-muted d-block" style="font-size: 0.7rem;">Stok gdg: {{ $item->variant->current_stock }}</small>
                                </td>

                                <!-- Stok Baru di Rak untuk Periode Berikutnya -->
                                <td class="text-center pe-4">
                                    <span class="badge bg-primary fs-6 display-stock-after">
                                        {{ $item->current_quantity }} pack
                                    </span>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Box Penyelesaian & Pembayaran Uang Laku -->
        <div class="row g-4">
            <div class="col-lg-6">
                <div class="card card-custom p-4 shadow-sm h-100">
                    <h5 class="fw-bold text-dark mb-3 pb-2 border-bottom">
                        <i class="bi bi-wallet2 me-2 text-success"></i> Metode &amp; Status Pembayaran Toko
                    </h5>

                    <div class="mb-3">
                        <label for="audit_date" class="form-label fw-semibold">Tanggal Survei / Kunjungan <span class="text-danger">*</span></label>
                        <input type="date" name="audit_date" id="audit_date" class="form-control" value="{{ old('audit_date', date('Y-m-d')) }}" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold">Cara Toko Membayar <span class="text-danger">*</span></label>
                        <div class="d-flex gap-3">
                            <div class="form-check p-3 rounded-3 border flex-grow-1 cursor-pointer">
                                <input class="form-check-input ms-0 me-2" type="radio" name="payment_method" id="payCash" value="cash" checked>
                                <label class="form-check-label fw-bold text-dark" for="payCash">
                                    <i class="bi bi-cash-stack text-success me-1"></i> Tunai (Cash di Tempat)
                                </label>
                            </div>
                            <div class="form-check p-3 rounded-3 border flex-grow-1 cursor-pointer">
                                <input class="form-check-input ms-0 me-2" type="radio" name="payment_method" id="payTransfer" value="transfer">
                                <label class="form-check-label fw-bold text-dark" for="payTransfer">
                                    <i class="bi bi-bank text-primary me-1"></i> Transfer Bank
                                </label>
                            </div>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold">Status Penerimaan Uang <span class="text-danger">*</span></label>
                        <select name="payment_status" class="form-select fw-bold text-dark">
                            <option value="paid" selected>Lunas Diterima Hari Ini</option>
                            <option value="unpaid">Belum Dibayar (Pending Setor)</option>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label for="notes" class="form-label fw-semibold">Catatan Kondisi Toko / Rak</label>
                        <textarea name="notes" id="notes" rows="2" class="form-control" placeholder="Contoh: Kopi sachet sangat laris, minggu depan minta tambah 30 pack lagi.">{{ old('notes') }}</textarea>
                    </div>
                </div>
            </div>

            <div class="col-lg-6">
                <div class="card card-custom p-4 shadow-sm h-100 d-flex flex-column justify-content-between" style="background: linear-gradient(135deg, #fdfbf7 0%, #fef3c7 100%); border: 2px solid #fde68a;">
                    <div>
                        <h5 class="fw-bold text-dark mb-3 pb-2 border-bottom border-warning">
                            <i class="bi bi-receipt-cutoff me-2 text-warning"></i> Ringkasan Hasil Survei Hari Ini
                        </h5>

                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <span class="text-secondary">Total Produk Kopi yang Laku:</span>
                            <h4 class="fw-bold text-success mb-0" id="totalSoldQtyDisplay">0 pack</h4>
                        </div>

                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <span class="text-secondary">Restock Baru Tambahan:</span>
                            <span class="fw-bold text-info fs-5" id="totalRestockQtyDisplay">+0 pack</span>
                        </div>

                        <div class="p-3 bg-white rounded-3 border border-warning shadow-sm mb-4">
                            <small class="text-muted fw-bold d-block text-uppercase mb-1">Total Uang yang Wajib Dibayar Toko:</small>
                            <h2 class="fw-bold text-dark mb-0" id="totalAmountToCollectDisplay">Rp 0</h2>
                            <small class="text-success fw-semibold"><i class="bi bi-check-circle me-1"></i>Hanya ditagih untuk produk yang benar-benar laku terjual.</small>
                        </div>
                    </div>

                    <div>
                        <button type="submit" class="btn btn-warning btn-lg w-100 fw-bold text-dark shadow py-3">
                            <i class="bi bi-check-circle-fill me-1"></i> Simpan Hasil Survei &amp; Cetak Kuitansi
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>
@endsection

@push('scripts')
<script>
    function recalcRow(inputEl) {
        const row = inputEl.closest('.calc-row');
        const stockBefore = parseInt(row.querySelector('.stock-before-badge').getAttribute('data-stock-before') || 0);
        const physicalRemaining = parseInt(row.querySelector('.input-physical').value || 0);
        const returnedQty = parseInt(row.querySelector('.input-returned').value || 0);
        const restockQty = parseInt(row.querySelector('.input-restock').value || 0);
        const unitPrice = parseFloat(row.querySelector('.unit-price-display').getAttribute('data-price') || 0);

        // Laku = Stok Sebelum - Sisa Fisik - Retur
        let soldQty = stockBefore - physicalRemaining - returnedQty;
        if (soldQty < 0) {
            soldQty = 0;
        }

        const subtotal = soldQty * unitPrice;
        const stockAfter = physicalRemaining + restockQty;

        row.querySelector('.display-sold').textContent = soldQty + ' pack';
        row.querySelector('.subtotal-display').textContent = 'Rp ' + subtotal.toLocaleString('id-ID');
        row.querySelector('.display-stock-after').textContent = stockAfter + ' pack';

        recalcTotal();
    }

    function recalcTotal() {
        let totalSoldQty = 0;
        let totalSoldAmount = 0;
        let totalRestockQty = 0;

        document.querySelectorAll('.calc-row').forEach(row => {
            const stockBefore = parseInt(row.querySelector('.stock-before-badge').getAttribute('data-stock-before') || 0);
            const physicalRemaining = parseInt(row.querySelector('.input-physical').value || 0);
            const returnedQty = parseInt(row.querySelector('.input-returned').value || 0);
            const restockQty = parseInt(row.querySelector('.input-restock').value || 0);
            const unitPrice = parseFloat(row.querySelector('.unit-price-display').getAttribute('data-price') || 0);

            let soldQty = stockBefore - physicalRemaining - returnedQty;
            if (soldQty < 0) soldQty = 0;

            totalSoldQty += soldQty;
            totalSoldAmount += (soldQty * unitPrice);
            totalRestockQty += restockQty;
        });

        document.getElementById('totalSoldQtyDisplay').textContent = totalSoldQty + ' pack';
        document.getElementById('totalRestockQtyDisplay').textContent = '+' + totalRestockQty + ' pack';
        document.getElementById('totalAmountToCollectDisplay').textContent = 'Rp ' + totalSoldAmount.toLocaleString('id-ID');
    }

    // Hitung saat awal render
    document.addEventListener('DOMContentLoaded', function() {
        document.querySelectorAll('.input-physical').forEach(el => recalcRow(el));
    });
</script>
@endpush
