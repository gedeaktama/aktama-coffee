@extends('layouts.app')

@section('title', 'Aktivitas Kunjungan Toko - Kopi Distro')

@push('styles')
<style>
    .visit-card {
        background: #ffffff;
        border: 1px solid #e2e8f0;
        border-radius: 18px;
        padding: 1.25rem;
        box-shadow: 0 4px 15px rgba(0, 0, 0, 0.04);
        margin-bottom: 1.25rem;
    }
    .qty-input {
        width: 75px !important;
        text-align: center;
        font-weight: 700;
        border-radius: 10px;
    }
    .item-audit-row {
        background: #f8fafc;
        border: 1px solid #edf2f7;
        border-radius: 12px;
        padding: 0.85rem 1rem;
        margin-bottom: 0.75rem;
        transition: all 0.2s ease;
    }
    .item-audit-row:hover {
        background: #ffffff;
        border-color: #cbd5e1;
    }
    .total-summary-card {
        background: linear-gradient(135deg, #18181b 0%, #27272a 100%);
        color: #ffffff;
        border-radius: 16px;
        padding: 1.25rem;
    }
</style>
@endpush

@section('content')
<div class="py-2" style="max-width: 720px; margin: 0 auto;">

    <!-- 1. HEADER TOKO & STATUS CHECK-IN -->
    <div class="visit-card border-top border-4 border-warning">
        <div class="d-flex justify-content-between align-items-start mb-2">
            <div>
                <span class="badge bg-warning text-dark fw-bold px-2 py-1 rounded-pill mb-1">
                    <i class="bi bi-clock-history me-1"></i> CHECK-IN: {{ $visit->check_in_at->format('H:i') }} WITA
                </span>
                <h4 class="fw-bold mb-0 text-dark">{{ $customer->business_name }}</h4>
            </div>
            
            @if($visit->distance_meters !== null)
                @if($visit->is_geofence_valid)
                    <span class="badge bg-success-subtle text-success border border-success border-opacity-25 px-2 py-1 rounded-pill">
                        <i class="bi bi-geo-alt-fill me-1"></i> GPS Valid ({{ $visit->distance_meters }}m)
                    </span>
                @else
                    <span class="badge bg-warning-subtle text-warning border border-warning border-opacity-25 px-2 py-1 rounded-pill">
                        <i class="bi bi-exclamation-triangle-fill me-1"></i> Jarak {{ $visit->distance_meters }}m
                    </span>
                @endif
            @endif
        </div>

        <p class="text-muted small mb-2">
            <i class="bi bi-geo-alt me-1 text-danger"></i> {{ $customer->shipping_address ?? $customer->address }}
        </p>

        <div class="d-flex align-items-center gap-2 pt-2 border-top small">
            <span class="text-muted">PIC Toko:</span>
            <strong>{{ $customer->pic_name ?? '-' }}</strong>
            <span class="text-muted ms-2">Telepon:</span>
            <strong>{{ $customer->phone ?? '-' }}</strong>
        </div>
    </div>

    <!-- FORMULIR AUDIT STOK & CHECK-OUT -->
    <form action="{{ route('driver.visit.complete', $visit->id) }}" method="POST" enctype="multipart/form-data">
        @csrf

        <!-- 2. AUDIT SISA STOK RAK & BARANG LAKU -->
        <div class="visit-card">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <div>
                    <h5 class="fw-bold mb-0">Audit Sisa Stok Rak Kopi</h5>
                    <small class="text-muted">Ketik sisa fisik di rak kopi toko untuk menghitung barang laku</small>
                </div>
                <span class="badge bg-primary-subtle text-primary border px-3 py-1 rounded-pill">
                    Konsinyasi
                </span>
            </div>

            @if($activeConsignment && $activeConsignment->items->isNotEmpty())
                @foreach($activeConsignment->items as $item)
                <div class="item-audit-row">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <div>
                            <strong class="d-block text-dark">{{ $item->variant->product->name ?? 'Produk Kopi' }}</strong>
                            <small class="text-muted">{{ $item->variant->variant_name }} &bull; Rp {{ number_format($item->selling_price, 0, ',', '.') }}/pack</small>
                        </div>
                        <span class="badge bg-light text-dark border px-2 py-1">
                            Stok Tercatat: <strong class="fs-6">{{ $item->current_quantity }}</strong>
                        </span>
                    </div>

                    <div class="row g-2 align-items-center pt-2 border-top">
                        <!-- Sisa Fisik di Rak -->
                        <div class="col-4">
                            <label class="form-label text-muted small mb-1">Sisa di Rak</label>
                            <input type="number" 
                                   name="audit_items[{{ $item->id }}][physical_remaining]" 
                                   class="form-control qty-input input-physical" 
                                   data-item-id="{{ $item->id }}"
                                   data-stock-before="{{ $item->current_quantity }}"
                                   data-price="{{ $item->selling_price }}"
                                   value="{{ $item->current_quantity }}" 
                                   min="0" 
                                   max="{{ $item->current_quantity }}" 
                                   required>
                        </div>

                        <!-- Hitungan Laku Otomatis -->
                        <div class="col-4 text-center">
                            <label class="form-label text-muted small mb-1">Laku Terjual</label>
                            <div>
                                <span class="badge bg-success text-white fs-6 px-3 py-2 fw-bold span-sold-qty" id="sold-qty-{{ $item->id }}">
                                    0 pack
                                </span>
                            </div>
                        </div>

                        <!-- Restock Tambahan Baru -->
                        <div class="col-4 text-end">
                            <label class="form-label text-muted small mb-1">+ Tambah Restock</label>
                            <div class="d-flex justify-content-end">
                                <input type="number" 
                                       name="audit_items[{{ $item->id }}][restock_qty]" 
                                       class="form-control qty-input input-restock text-warning" 
                                       data-item-id="{{ $item->id }}"
                                       value="0" 
                                       min="0">
                            </div>
                        </div>
                    </div>
                </div>
                @endforeach
            @else
                <div class="alert alert-info small py-3 rounded-3 mb-0">
                    <i class="bi bi-info-circle me-1"></i> Toko ini belum memiliki data rak titip konsinyasi aktif. Kunjungan akan dicatat sebagai pengiriman biasa / survei.
                </div>
            @endif

            <!-- Live Summary Card -->
            <div class="total-summary-card mt-3">
                <div class="d-flex justify-content-between align-items-center mb-1">
                    <span class="text-white-50 small">Total Barang Laku Terjual:</span>
                    <strong class="fs-5 text-success" id="grand-sold-qty">0 Pack</strong>
                </div>
                <div class="d-flex justify-content-between align-items-center mb-1">
                    <span class="text-white-50 small">Estimasi Uang Terkumpul:</span>
                    <strong class="fs-4 text-warning" id="grand-sold-amount">Rp 0</strong>
                </div>
                <div class="d-flex justify-content-between align-items-center">
                    <span class="text-white-50 small">Total Restock Baru:</span>
                    <strong class="fs-6 text-info" id="grand-restock-qty">0 Pack</strong>
                </div>
            </div>
        </div>

        <!-- 3. SERAH TERIMA & BUKTI FOTO (POD) -->
        <div class="visit-card">
            <h5 class="fw-bold mb-3">Bukti Kunjungan & Serah Terima (POD)</h5>

            <div class="row g-3">
                <div class="col-12 col-md-6">
                    <label class="form-label small fw-bold text-muted">Nama Staf / Kasir Toko Penerima</label>
                    <input type="text" name="recipient_name" class="form-control" placeholder="Contoh: Bli Made (Kasir)">
                </div>
                <div class="col-12 col-md-6">
                    <label class="form-label small fw-bold text-muted">No. HP / WA Staf Toko (Opsional)</label>
                    <input type="text" name="recipient_phone" class="form-control" placeholder="0812xxxxxxxx">
                </div>

                <div class="col-12">
                    <label class="form-label small fw-bold text-muted">
                        <i class="bi bi-camera me-1"></i> Foto Rak Kopi / Nota Serah Terima
                    </label>
                    <input type="file" name="photo_proof" class="form-control" accept="image/*" capture="environment">
                    <small class="text-muted" style="font-size: 0.75rem;">
                        Ambil foto rak kopi yang tertata rapi atau nota bertanda tangan sebagai bukti fisik kunjungan.
                    </small>
                </div>

                <div class="col-12">
                    <label class="form-label small fw-bold text-muted">Catatan Kunjungan / Kondisi Toko</label>
                    <textarea name="notes" class="form-control" rows="2" placeholder="Contoh: Rak kopi bersih di dekat kasir, permintaan restock varian gula aren minggu depan..."></textarea>
                </div>
            </div>
        </div>

        <!-- TOMBOL SELESAIKAN KUNJUNGAN -->
        <div class="d-grid gap-2 mb-4">
            <button type="submit" class="btn btn-warning btn-lg fw-bold rounded-pill shadow">
                <i class="bi bi-check-circle-fill me-2"></i> SELESAIKAN KUNJUNGAN & SIMPAN
            </button>
            <a href="{{ route('driver.dashboard') }}" class="btn btn-outline-secondary rounded-pill">
                Kembali ke Dashboard
            </a>
        </div>
    </form>

</div>
@endsection

@push('scripts')
<script>
    function recalculateAudit() {
        let grandSoldQty = 0;
        let grandSoldAmount = 0;
        let grandRestockQty = 0;

        document.querySelectorAll('.input-physical').forEach(input => {
            const stockBefore = parseInt(input.dataset.stockBefore) || 0;
            const price = parseFloat(input.dataset.price) || 0;
            const physical = parseInt(input.value) || 0;
            const itemId = input.dataset.itemId;

            const soldQty = Math.max(0, stockBefore - physical);
            const soldAmount = soldQty * price;

            const soldSpan = document.getElementById(`sold-qty-${itemId}`);
            if (soldSpan) {
                soldSpan.textContent = `${soldQty} pack`;
            }

            grandSoldQty += soldQty;
            grandSoldAmount += soldAmount;
        });

        document.querySelectorAll('.input-restock').forEach(input => {
            const restock = parseInt(input.value) || 0;
            grandRestockQty += restock;
        });

        document.getElementById('grand-sold-qty').textContent = `${grandSoldQty} Pack`;
        document.getElementById('grand-sold-amount').textContent = `Rp ${grandSoldAmount.toLocaleString('id-ID')}`;
        document.getElementById('grand-restock-qty').textContent = `${grandRestockQty} Pack`;
    }

    document.querySelectorAll('.input-physical, .input-restock').forEach(input => {
        input.addEventListener('input', recalculateAudit);
        input.addEventListener('change', recalculateAudit);
    });

    // Hitung pertama kali saat halaman dimuat
    recalculateAudit();
</script>
@endpush
