@extends('layouts.app')

@section('title', 'Buat Pesanan Pembelian (PO) - Kopi Distro')

@section('content')
<div class="container py-3">
    <!-- Breadcrumb & Title -->
    <div class="mb-4">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-1">
                <li class="breadcrumb-item"><a href="{{ route('purchases.index') }}" class="text-decoration-none">Pembelian</a></li>
                <li class="breadcrumb-item active" aria-current="page">Buat PO Baru</li>
            </ol>
        </nav>
        <h2 class="fw-bold text-dark mb-1">Buat Pesanan Pembelian ke Supplier</h2>
        <p class="text-muted mb-0">Terbitkan dokumen Purchase Order (PO) resmi untuk pemesanan varian kopi ke pemasok.</p>
    </div>

    @if ($errors->any())
        <div class="alert alert-danger alert-dismissible fade show shadow-sm mb-4" role="alert">
            <div class="d-flex align-items-center mb-1">
                <i class="bi bi-exclamation-triangle-fill fs-5 me-2"></i>
                <h6 class="fw-bold mb-0">Terdapat kesalahan pada input formulir:</h6>
            </div>
            <ul class="mb-0 ps-4 small">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <form method="POST" action="{{ route('purchases.store') }}" id="purchaseForm">
        @csrf

        <div class="row g-4">
            <!-- Left Column: Header Information -->
            <div class="col-lg-4">
                <div class="card card-custom p-4 mb-4">
                    <h5 class="fw-bold text-dark mb-3 pb-2 border-bottom">
                        <i class="bi bi-file-earmark-text me-2 text-warning"></i> Dokumen PO
                    </h5>

                    <div class="mb-3">
                        <label for="purchase_number" class="form-label fw-semibold">Nomor PO <span class="text-danger">*</span></label>
                        <input type="text" name="purchase_number" id="purchase_number" class="form-control font-monospace fw-bold @error('purchase_number') is-invalid @enderror" value="{{ old('purchase_number', $autoPoNumber) }}" required>
                        <small class="text-muted">Nomor PO unik format PO-YYYYMM-XXXX</small>
                    </div>

                    <div class="mb-3">
                        <label for="purchase_date" class="form-label fw-semibold">Tanggal Pesanan <span class="text-danger">*</span></label>
                        <input type="date" name="purchase_date" id="purchase_date" class="form-control @error('purchase_date') is-invalid @enderror" value="{{ old('purchase_date', date('Y-m-d')) }}" required>
                    </div>

                    <div class="mb-3">
                        <label for="supplier_id" class="form-label fw-semibold">Pilih Supplier <span class="text-danger">*</span></label>
                        <select name="supplier_id" id="supplier_id" class="form-select form-select-searchable @error('supplier_id') is-invalid @enderror" data-placeholder="-- Ketik nama supplier atau kontak --" required>
                            <option value="">-- Pilih Supplier Pemasok --</option>
                            @foreach($suppliers as $sup)
                                <option value="{{ $sup->id }}" {{ old('supplier_id') == $sup->id ? 'selected' : '' }}>
                                    {{ $sup->name }} ({{ $sup->contact_person ?? 'Tanpa PIC' }})
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="mb-3">
                        <label for="notes" class="form-label fw-semibold">Catatan / Instruksi Pengiriman</label>
                        <textarea name="notes" id="notes" rows="3" class="form-control" placeholder="Contoh: Jadwal kirim, nomor resi pengiriman vendor, kondisi packaging">{{ old('notes') }}</textarea>
                    </div>

                    <div class="alert alert-info py-2 px-3 small border-0 mb-0">
                        <i class="bi bi-info-circle-fill me-1"></i>
                        <strong>Catatan Sistem:</strong> Saldo stok fisik di gudang <strong>tidak</strong> akan langsung bertambah hingga barang fisik dikonfirmasi tiba di gudang.
                    </div>
                </div>
            </div>

            <!-- Right Column: Item Table & Summary -->
            <div class="col-lg-8">
                <div class="card card-custom p-4 mb-4">
                    <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-3 pb-2 border-bottom">
                        <div>
                            <h5 class="fw-bold text-dark mb-0">
                                <i class="bi bi-box-seam me-2 text-primary"></i> Daftar Item Kopi yang Dipesan
                            </h5>
                            <small class="text-muted">Cari varian di tabel atau pilih cepat dari katalog.</small>
                        </div>
                        <div class="d-flex gap-2">
                            <button type="button" class="btn btn-sm btn-warning text-dark fw-bold shadow-sm" data-bs-toggle="modal" data-bs-target="#quickProductModalPurchase">
                                <i class="bi bi-grid-3x3-gap-fill me-1"></i> Katalog Cepat (Pilih Varian)
                            </button>
                            <button type="button" class="btn btn-sm btn-outline-success" id="btnAddRow">
                                <i class="bi bi-plus-circle me-1"></i> Tambah Baris
                            </button>
                        </div>
                    </div>

                    <div class="table-responsive mb-3">
                        <table class="table table-bordered align-middle" id="itemsTable">
                            <thead class="table-light">
                                <tr>
                                    <th style="min-width: 250px;">Varian Produk & SKU <span class="text-danger">*</span></th>
                                    <th style="width: 120px;" class="text-center">Jumlah (Pack) <span class="text-danger">*</span></th>
                                    <th style="width: 170px;" class="text-end">Harga Beli Satuan (Rp) <span class="text-danger">*</span></th>
                                    <th style="width: 170px;" class="text-end">Subtotal (Rp)</th>
                                    <th style="width: 50px;" class="text-center">#</th>
                                </tr>
                            </thead>
                            <tbody id="itemsBody">
                                <!-- Dynamic Rows Injected by JS -->
                            </tbody>
                        </table>
                    </div>

                    <!-- Summary & Costs -->
                    <div class="bg-light p-3 rounded-3 border">
                        <div class="row g-3">
                            <div class="col-md-4">
                                <label for="discount" class="form-label small fw-semibold text-muted">Potongan Diskon (Rp)</label>
                                <div class="input-group input-group-sm">
                                    <span class="input-group-text">Rp</span>
                                    <input type="number" name="discount" id="discount" class="form-control text-end" value="{{ old('discount', 0) }}" min="0" step="100">
                                </div>
                            </div>
                            <div class="col-md-4">
                                <label for="shipping_cost" class="form-label small fw-semibold text-muted">Biaya Kirim / Logistik (Rp)</label>
                                <div class="input-group input-group-sm">
                                    <span class="input-group-text">Rp</span>
                                    <input type="number" name="shipping_cost" id="shipping_cost" class="form-control text-end" value="{{ old('shipping_cost', 0) }}" min="0" step="100">
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="text-md-end pt-md-2">
                                    <small class="text-muted d-block">Subtotal Item: <span id="lblSubtotal" class="fw-bold text-dark">Rp 0</span></small>
                                    <div class="fs-5 fw-bold text-coffee mt-1">
                                        Total PO: <span id="lblTotal" class="text-success">Rp 0</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="d-flex justify-content-between align-items-center mt-4 pt-3 border-top">
                        <a href="{{ route('purchases.index') }}" class="btn btn-outline-secondary">
                            <i class="bi bi-arrow-left me-1"></i> Batal & Kembali
                        </a>
                        <button type="submit" class="btn btn-coffee px-4 shadow-sm">
                            <i class="bi bi-send-check me-1"></i> Terbitkan Purchase Order (PO)
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>

<!-- ═══════════════════════════════════════════════════════════
     MODAL KATALOG CEPAT PILIH VARIAN (PURCHASE ORDER)
     ═══════════════════════════════════════════════════════════ -->
<div class="modal fade" id="quickProductModalPurchase" tabindex="-1" aria-labelledby="quickProductModalPurchaseLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content border-0 shadow-lg">
            <div class="modal-header bg-dark text-white p-3">
                <div class="d-flex align-items-center gap-2">
                    <div class="p-2 bg-warning rounded-3 text-dark">
                        <i class="bi bi-grid-3x3-gap-fill fs-5"></i>
                    </div>
                    <div>
                        <h5 class="modal-title fw-bold text-white mb-0" id="quickProductModalPurchaseLabel">Katalog Varian Kopi &mdash; Pilih Cepat</h5>
                        <small class="text-white-50">Cari dan masukkan item kopi yang ingin dipesan ke supplier</small>
                    </div>
                </div>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-3 p-md-4 bg-light">
                <div class="card card-custom p-3 mb-3 border-0 shadow-sm">
                    <div class="input-group">
                        <span class="input-group-text bg-white border-end-0 text-muted"><i class="bi bi-search"></i></span>
                        <input type="text" id="poModalSearchInput" class="form-control border-start-0" placeholder="Ketik nama kopi, SKU, gramasi...">
                    </div>
                </div>

                <div class="row g-3" id="poModalVariantGrid">
                    <!-- Populated dynamically by JS -->
                </div>

                <div id="poModalEmptyState" class="text-center py-5 d-none">
                    <i class="bi bi-search text-muted display-4 mb-2 d-block"></i>
                    <h6 class="fw-bold text-muted">Tidak ditemukan varian kopi yang cocok</h6>
                    <p class="text-muted small">Coba kata kunci pencarian lainnya.</p>
                </div>
            </div>
            <div class="modal-footer bg-white d-flex justify-content-between align-items-center p-3">
                <span class="text-muted small">
                    <i class="bi bi-lightning-charge text-warning me-1"></i> Klik <strong>Pilih</strong> untuk menambahkan item ke PO.
                </span>
                <button type="button" class="btn btn-secondary px-4" data-bs-dismiss="modal">Selesai &amp; Tutup</button>
            </div>
        </div>
    </div>
</div>

<!-- Template for Variant Options in JSON -->
<script>
    const availableVariants = @json($variantsJson ?? []);
</script>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const itemsBody = document.getElementById('itemsBody');
    const btnAddRow = document.getElementById('btnAddRow');
    const discountInput = document.getElementById('discount');
    const shippingInput = document.getElementById('shipping_cost');
    const lblSubtotal = document.getElementById('lblSubtotal');
    const lblTotal = document.getElementById('lblTotal');

    let rowIndex = 0;

    function formatRupiah(num) {
        return 'Rp ' + Number(num).toLocaleString('id-ID');
    }

    function addRow(variantId = '', qty = 1, price = 0) {
        const tr = document.createElement('tr');
        tr.className = 'item-row';
        tr.dataset.index = rowIndex;

        let optionsHtml = '<option value="">-- Pilih Varian Kopi & SKU --</option>';
        availableVariants.forEach(v => {
            const selected = (v.id == variantId) ? 'selected' : '';
            optionsHtml += `<option value="${v.id}" data-cost="${v.cost_price}" ${selected}>${v.sku} &mdash; ${v.name} (Stok: ${v.current_stock})</option>`;
        });

        tr.innerHTML = `
            <td>
                <select name="items[${rowIndex}][product_variant_id]" class="form-select form-select-sm select-variant form-select-searchable" data-placeholder="-- Ketik nama kopi atau SKU --" required>
                    ${optionsHtml}
                </select>
            </td>
            <td>
                <input type="number" name="items[${rowIndex}][quantity]" class="form-control form-select-sm text-center input-qty" value="${qty}" min="1" required>
            </td>
            <td>
                <div class="input-group input-group-sm">
                    <span class="input-group-text">Rp</span>
                    <input type="number" name="items[${rowIndex}][cost_price]" class="form-control text-end input-price" value="${price}" min="0" step="100" required>
                </div>
            </td>
            <td class="text-end fw-semibold line-subtotal">
                ${formatRupiah(qty * price)}
            </td>
            <td class="text-center">
                <button type="button" class="btn btn-sm btn-outline-danger btn-remove-row p-1" title="Hapus Baris">
                    <i class="bi bi-trash"></i>
                </button>
            </td>
        `;

        itemsBody.appendChild(tr);

        // Attach event listeners
        const selVariant = tr.querySelector('.select-variant');
        const inQty = tr.querySelector('.input-qty');
        const inPrice = tr.querySelector('.input-price');
        const btnRemove = tr.querySelector('.btn-remove-row');

        if (typeof window.KopiSearchSelect === 'function') {
            new window.KopiSearchSelect(selVariant);
        }

        selVariant.addEventListener('change', function () {
            const opt = this.options[this.selectedIndex];
            if (opt && opt.dataset.cost) {
                inPrice.value = parseFloat(opt.dataset.cost) || 0;
            }
            recalculate();
        });

        inQty.addEventListener('input', recalculate);
        inPrice.addEventListener('input', recalculate);

        btnRemove.addEventListener('click', function () {
            if (document.querySelectorAll('.item-row').length > 1) {
                tr.remove();
                recalculate();
            } else {
                alert('Pesanan pembelian harus memiliki minimal 1 item produk.');
            }
        });

        rowIndex++;
        recalculate();
    }

    function recalculate() {
        let subtotal = 0;
        document.querySelectorAll('.item-row').forEach(tr => {
            const qty = parseFloat(tr.querySelector('.input-qty').value) || 0;
            const price = parseFloat(tr.querySelector('.input-price').value) || 0;
            const lineTotal = qty * price;
            tr.querySelector('.line-subtotal').textContent = formatRupiah(lineTotal);
            subtotal += lineTotal;
        });

        const discount = parseFloat(discountInput.value) || 0;
        const shipping = parseFloat(shippingInput.value) || 0;
        const total = Math.max(0, subtotal - discount + shipping);

        lblSubtotal.textContent = formatRupiah(subtotal);
        lblTotal.textContent = formatRupiah(total);
    }

    btnAddRow.addEventListener('click', () => addRow());
    discountInput.addEventListener('input', recalculate);
    shippingInput.addEventListener('input', recalculate);

    // Initial first row
    addRow();

    // Quick Modal Populator for Purchases
    const poModalGrid = document.getElementById('poModalVariantGrid');
    const poModalSearch = document.getElementById('poModalSearchInput');
    const poModalEmpty = document.getElementById('poModalEmptyState');

    function renderPoModalCards(filter = '') {
        if (!poModalGrid) return;
        poModalGrid.innerHTML = '';
        const q = filter.trim().toLowerCase();
        let count = 0;

        availableVariants.forEach(v => {
            const searchTerms = `${v.sku} ${v.name}`.toLowerCase();
            if (q && !searchTerms.includes(q)) return;

            const col = document.createElement('div');
            col.className = 'col-md-6 col-lg-4';
            const cost = v.cost_price || 0;

            col.innerHTML = `
                <div class="card h-100 border p-3 variant-pick-card position-relative shadow-sm">
                    <div class="d-flex justify-content-between align-items-start mb-2">
                        <span class="badge bg-secondary font-monospace">${v.sku}</span>
                        <span class="badge bg-light text-dark border">Stok Sekarang: ${v.current_stock}</span>
                    </div>
                    <h6 class="fw-bold text-dark mb-1">${v.name}</h6>
                    <div class="d-flex justify-content-between align-items-center mt-auto pt-2 border-top">
                        <div>
                            <small class="text-muted d-block" style="font-size: 0.72rem;">Estimasi Harga Beli</small>
                            <span class="fw-bold text-dark">${formatRupiah(cost)}</span>
                        </div>
                        <div class="d-flex align-items-center gap-1">
                            <input type="number" class="form-control form-control-sm text-center quick-qty-input" value="10" min="1" style="width: 60px;">
                            <button type="button" class="btn btn-sm btn-warning text-dark fw-bold btn-add-po-modal">
                                <i class="bi bi-plus-lg me-1"></i> Pilih
                            </button>
                        </div>
                    </div>
                </div>
            `;

            col.querySelector('.btn-add-po-modal').addEventListener('click', function() {
                const qty = parseInt(col.querySelector('.quick-qty-input').value || 10);
                
                // Cek apakah varian sudah ada
                let existingRow = null;
                document.querySelectorAll('#itemsBody .item-row').forEach(r => {
                    const sel = r.querySelector('.select-variant');
                    if (sel && sel.value == v.id) existingRow = r;
                });

                if (existingRow) {
                    const inQty = existingRow.querySelector('.input-qty');
                    inQty.value = parseInt(inQty.value || 0) + qty;
                    recalculate();
                } else {
                    const firstRow = document.querySelector('#itemsBody .item-row');
                    const firstSel = firstRow ? firstRow.querySelector('.select-variant') : null;
                    if (firstSel && !firstSel.value) {
                        firstSel.value = v.id;
                        if (firstSel._kopiSearchSelect) firstSel._kopiSearchSelect.syncFromSelect();
                        firstRow.querySelector('.input-qty').value = qty;
                        firstRow.querySelector('.input-price').value = cost;
                        firstSel.dispatchEvent(new Event('change'));
                    } else {
                        addRow(v.id, qty, cost);
                    }
                }

                const origHtml = this.innerHTML;
                this.className = 'btn btn-sm btn-success text-white fw-bold';
                this.innerHTML = '<i class="bi bi-check-lg me-1"></i> Ditambah';
                setTimeout(() => {
                    this.className = 'btn btn-sm btn-warning text-dark fw-bold btn-add-po-modal';
                    this.innerHTML = origHtml;
                }, 1000);
            });

            poModalGrid.appendChild(col);
            count++;
        });

        if (count === 0) {
            poModalEmpty.classList.remove('d-none');
        } else {
            poModalEmpty.classList.add('d-none');
        }
    }

    renderPoModalCards();

    if (poModalSearch) {
        poModalSearch.addEventListener('input', function() {
            renderPoModalCards(this.value);
        });
    }
});
</script>
@endpush
@endsection
