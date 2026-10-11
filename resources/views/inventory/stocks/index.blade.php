@extends('layouts.app')

@section('title', 'Monitoring Persediaan Stok & Alert - Kopi Distro')

@section('content')
<div class="container py-3">
    <!-- Header Page -->
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-4">
        <div>
            <div class="d-flex align-items-center gap-2">
                <span class="badge bg-warning bg-opacity-25 text-dark px-3 py-2 rounded-pill fw-semibold">
                    <i class="bi bi-boxes me-1"></i> Modul Manajemen Persediaan
                </span>
                <span class="text-muted">&bull;</span>
                <span class="small text-muted">Tahap 3: Persediaan & Pembelian</span>
            </div>
            <h2 class="fw-bold text-dark mt-2 mb-1">Monitoring Persediaan & Alert Stok</h2>
            <p class="text-muted mb-0">Pantau ketersediaan fisik per SKU kopi kemasan, deteksi stok menipis, dan lakukan penyesuaian stock opname audit.</p>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('inventory.movements') }}" class="btn btn-outline-coffee d-inline-flex align-items-center gap-2">
                <i class="bi bi-journal-text"></i>
                <span>Kartu Mutasi Stok</span>
            </a>
            @if(auth()->user()->hasRole(['owner', 'admin', 'warehouse']))
                <a href="{{ route('purchases.create') }}" class="btn btn-coffee d-inline-flex align-items-center gap-2 shadow-sm">
                    <i class="bi bi-cart-plus"></i>
                    <span>Restock Beli ke Supplier</span>
                </a>
            @endif
        </div>
    </div>

    <!-- Inventory Metric Cards -->
    <div class="row g-3 mb-4">
        <div class="col-sm-6 col-lg-4">
            <div class="card card-custom p-3 border-start border-primary border-4">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <span class="text-muted small fw-semibold text-uppercase">Total Saldo Fisik</span>
                        <h3 class="fw-bold mb-0 text-dark">{{ number_format($metrics['total_items']) }} <small class="fs-6 text-muted">Pack</small></h3>
                    </div>
                    <div class="p-3 rounded-3 bg-primary bg-opacity-10 text-primary">
                        <i class="bi bi-boxes fs-4"></i>
                    </div>
                </div>
                <small class="text-muted mt-2 d-block border-top pt-2">Akumulasi seluruh varian kopi kemasan</small>
            </div>
        </div>

        <div class="col-sm-6 col-lg-4">
            <div class="card card-custom p-3 border-start border-warning border-4">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <span class="text-muted small fw-semibold text-uppercase">Alert Stok Menipis</span>
                        <h3 class="fw-bold mb-0 text-warning">{{ $metrics['low_stock_count'] }} <small class="fs-6 text-muted">SKU</small></h3>
                    </div>
                    <div class="p-3 rounded-3 bg-warning bg-opacity-10 text-warning">
                        <i class="bi bi-exclamation-triangle-fill fs-4"></i>
                    </div>
                </div>
                <div class="mt-2 pt-2 border-top d-flex justify-content-between small">
                    <span class="text-muted">Kuantitas &le; Stok Minimum</span>
                    <a href="{{ route('inventory.index', ['filter' => 'low_stock']) }}" class="text-warning fw-semibold text-decoration-none">Filter SKU &rarr;</a>
                </div>
            </div>
        </div>

        <div class="col-sm-6 col-lg-4">
            <div class="card card-custom p-3 border-start border-danger border-4">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <span class="text-muted small fw-semibold text-uppercase">Stok Habis (Kosong)</span>
                        <h3 class="fw-bold mb-0 text-danger">{{ $metrics['out_of_stock_count'] }} <small class="fs-6 text-muted">SKU</small></h3>
                    </div>
                    <div class="p-3 rounded-3 bg-danger bg-opacity-10 text-danger">
                        <i class="bi bi-x-circle-fill fs-4"></i>
                    </div>
                </div>
                <div class="mt-2 pt-2 border-top d-flex justify-content-between small">
                    <span class="text-muted">Saldo 0 Pack (Perlu Restock)</span>
                    <a href="{{ route('inventory.index', ['filter' => 'out_of_stock']) }}" class="text-danger fw-semibold text-decoration-none">Filter SKU &rarr;</a>
                </div>
            </div>
        </div>
    </div>

    <!-- Filter & Search Bar -->
    <div class="card card-custom p-3 mb-4">
        <form method="GET" action="{{ route('inventory.index') }}" class="row g-2 align-items-center">
            <div class="col-md-5">
                <div class="input-group">
                    <span class="input-group-text bg-white border-end-0 text-muted"><i class="bi bi-search"></i></span>
                    <input type="text" name="search" class="form-control border-start-0" placeholder="Cari SKU, varian, atau nama kopi..." value="{{ request('search') }}">
                </div>
            </div>
            <div class="col-md-4">
                <select name="filter" class="form-select">
                    <option value="">-- Semua Ketersediaan Stok --</option>
                    <option value="low_stock" {{ request('filter') === 'low_stock' ? 'selected' : '' }}>Alert: Stok Menipis (&le; Minimum)</option>
                    <option value="out_of_stock" {{ request('filter') === 'out_of_stock' ? 'selected' : '' }}>Habis (Saldo = 0)</option>
                </select>
            </div>
            <div class="col-md-3 d-flex gap-2">
                <button type="submit" class="btn btn-outline-coffee flex-grow-1">
                    <i class="bi bi-funnel me-1"></i> Filter
                </button>
                @if(request()->hasAny(['search', 'filter']))
                    <a href="{{ route('inventory.index') }}" class="btn btn-outline-secondary" title="Reset Filter">
                        <i class="bi bi-x-circle"></i>
                    </a>
                @endif
            </div>
        </form>
    </div>

    <!-- Stock Table -->
    <div class="card card-custom overflow-hidden">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th class="ps-3 py-3 text-secondary text-uppercase small fw-bold">SKU / Varian Kopi</th>
                        <th class="py-3 text-secondary text-uppercase small fw-bold">Kategori</th>
                        <th class="py-3 text-secondary text-uppercase small fw-bold text-center">Bentuk & Gramasi</th>
                        <th class="py-3 text-secondary text-uppercase small fw-bold text-end">Harga Satuan</th>
                        <th class="py-3 text-secondary text-uppercase small fw-bold text-center">Stok Min.</th>
                        <th class="py-3 text-secondary text-uppercase small fw-bold text-center">Saldo Fisik</th>
                        <th class="py-3 text-secondary text-uppercase small fw-bold text-center">Status</th>
                        <th class="pe-3 py-3 text-secondary text-uppercase small fw-bold text-end">Aksi</th>
                    </tr>
                </thead>
                <tbody class="border-top-0">
                    @forelse($variants as $variant)
                        <tr>
                            <td class="ps-3">
                                <div class="font-monospace fw-bold text-dark">{{ $variant->sku }}</div>
                                <div class="fw-semibold text-coffee">{{ $variant->product->name }} &mdash; {{ $variant->variant_name }}</div>
                            </td>
                            <td>
                                <span class="badge bg-light text-secondary border">
                                    {{ $variant->product->category->name ?? '-' }}
                                </span>
                            </td>
                            <td class="text-center small text-secondary">
                                <span class="badge bg-secondary bg-opacity-10 text-secondary">{{ ucfirst($variant->grind_form) }}</span>
                                <span class="fw-semibold">{{ $variant->package_weight_grams }}g</span>
                            </td>
                            <td class="text-end">
                                <div class="small text-muted">Modal: {{ $variant->formatted_cost }}</div>
                                <div class="fw-semibold text-dark">Jual: {{ $variant->formatted_price }}</div>
                            </td>
                            <td class="text-center text-muted small">
                                {{ $variant->minimum_stock }} pack
                            </td>
                            <td class="text-center">
                                @if($variant->current_stock <= 0)
                                    <span class="badge bg-danger px-3 py-2 rounded-pill fs-6">0 Pack</span>
                                @elseif($variant->isLowStock())
                                    <span class="badge bg-warning text-dark px-3 py-2 rounded-pill fs-6">
                                        <i class="bi bi-exclamation-triangle-fill me-1"></i> {{ $variant->current_stock }} Pack
                                    </span>
                                @else
                                    <span class="badge bg-success bg-opacity-10 text-success border border-success border-opacity-25 px-3 py-2 rounded-pill fs-6 fw-bold">
                                        {{ $variant->current_stock }} Pack
                                    </span>
                                @endif
                            </td>
                            <td class="text-center">
                                @if($variant->current_stock <= 0)
                                    <span class="badge bg-danger bg-opacity-10 text-danger border border-danger border-opacity-25">Habis</span>
                                @elseif($variant->isLowStock())
                                    <span class="badge bg-warning bg-opacity-10 text-warning border border-warning border-opacity-25">Menipis</span>
                                @else
                                    <span class="badge bg-success bg-opacity-10 text-success border border-success border-opacity-25">Aman</span>
                                @endif
                            </td>
                            <td class="pe-3 text-end">
                                <div class="btn-group btn-group-sm">
                                    <a href="{{ route('inventory.movements', ['variant_id' => $variant->id]) }}" class="btn btn-outline-secondary" title="Lihat Riwayat Kartu Stok">
                                        <i class="bi bi-journal-text me-1"></i> Kartu Stok
                                    </a>
                                    @if(auth()->user()->hasRole(['owner', 'admin', 'warehouse']))
                                        <button type="button" class="btn btn-outline-coffee btn-adjust-stock" 
                                            data-id="{{ $variant->id }}"
                                            data-sku="{{ $variant->sku }}"
                                            data-name="{{ $variant->product->name }} - {{ $variant->variant_name }}"
                                            data-stock="{{ $variant->current_stock }}"
                                            title="Penyesuaian Fisik / Stock Opname">
                                            <i class="bi bi-sliders me-1"></i> Opname
                                        </button>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="text-center py-5 text-muted">
                                <i class="bi bi-boxes fs-1 text-secondary opacity-50 mb-3 d-block"></i>
                                <h6 class="fw-semibold text-dark">Tidak ada data persediaan yang cocok</h6>
                                <p class="small text-muted mb-0">Coba ubah kata kunci pencarian atau reset filter.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($variants->hasPages())
            <div class="card-footer bg-white border-top py-3 d-flex justify-content-between align-items-center">
                <small class="text-muted">Menampilkan {{ $variants->firstItem() }} - {{ $variants->lastItem() }} dari {{ $variants->total() }} varian SKU</small>
                <div>{{ $variants->links() }}</div>
            </div>
        @endif
    </div>
</div>

<!-- Modal Penyesuaian Stok (Stock Opname Audit) -->
<div class="modal fade" id="modalStockAdjust" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <form method="POST" action="{{ route('inventory.adjust') }}">
                @csrf
                <input type="hidden" name="product_variant_id" id="adjust_variant_id">

                <div class="modal-header bg-light">
                    <h5 class="modal-title fw-bold text-dark">
                        <i class="bi bi-sliders me-2 text-warning"></i> Penyesuaian Stok Fisik (Opname)
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>

                <div class="modal-body p-4">
                    <div class="p-3 bg-light rounded-3 border mb-3">
                        <div class="font-monospace fw-bold text-primary" id="adjust_sku_display">SKU-XXXX</div>
                        <div class="fw-semibold text-dark" id="adjust_name_display">Nama Produk</div>
                        <div class="small text-muted mt-1">
                            Stok Saat Ini di Sistem: <strong id="adjust_current_stock_display" class="text-dark">0</strong> pack
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="actual_stock" class="form-label fw-semibold">Jumlah Stok Fisik Hasil Hitung (Aktual) <span class="text-danger">*</span></label>
                        <input type="number" name="actual_stock" id="actual_stock" class="form-control form-control-lg font-monospace text-center fw-bold" min="0" required>
                        <div class="d-flex justify-content-between align-items-center mt-1">
                            <small class="text-muted">Masukkan jumlah fisik nyata di rak gudang</small>
                            <small id="adjust_diff_display" class="fw-bold"></small>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="reason" class="form-label fw-semibold">Alasan Penyesuaian / Audit Trail <span class="text-danger">*</span></label>
                        <textarea name="reason" id="reason" rows="3" class="form-control" placeholder="Contoh: Selisih stock opname triwulan, kemasan robek di gudang, kalibrasi awal" minlength="5" maxlength="255" required></textarea>
                        <small class="text-muted">Wajib diisi sebagai jejak audit kepatuhan persediaan gudang.</small>
                    </div>
                </div>

                <div class="modal-footer bg-light">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-coffee px-4">
                        <i class="bi bi-check2-circle me-1"></i> Simpan Penyesuaian
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const modalAdjust = new bootstrap.Modal(document.getElementById('modalStockAdjust'));
    const inputVariantId = document.getElementById('adjust_variant_id');
    const displaySku = document.getElementById('adjust_sku_display');
    const displayName = document.getElementById('adjust_name_display');
    const displayCurrentStock = document.getElementById('adjust_current_stock_display');
    const inputActualStock = document.getElementById('actual_stock');
    const displayDiff = document.getElementById('adjust_diff_display');

    let currentStockVal = 0;

    document.querySelectorAll('.btn-adjust-stock').forEach(btn => {
        btn.addEventListener('click', function () {
            const id = this.dataset.id;
            const sku = this.dataset.sku;
            const name = this.dataset.name;
            const stock = parseInt(this.dataset.stock) || 0;

            inputVariantId.value = id;
            displaySku.textContent = sku;
            displayName.textContent = name;
            displayCurrentStock.textContent = stock;
            currentStockVal = stock;
            inputActualStock.value = stock;

            updateDiff();
            modalAdjust.show();
        });
    });

    function updateDiff() {
        const actual = parseInt(inputActualStock.value) || 0;
        const diff = actual - currentStockVal;

        if (diff > 0) {
            displayDiff.textContent = `Selisih: +${diff} (Bertambah)`;
            displayDiff.className = 'fw-bold text-success';
        } else if (diff < 0) {
            displayDiff.textContent = `Selisih: ${diff} (Berkurang)`;
            displayDiff.className = 'fw-bold text-danger';
        } else {
            displayDiff.textContent = 'Tidak ada perubahan selisih';
            displayDiff.className = 'fw-bold text-muted';
        }
    }

    inputActualStock.addEventListener('input', updateDiff);
});
</script>
@endpush
@endsection
