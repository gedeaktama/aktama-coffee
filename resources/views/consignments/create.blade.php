@extends('layouts.app')

@section('title', 'Kirim Titipan Konsinyasi Baru — Kopi Distro')

@section('content')
<div class="container py-3">
    <!-- Breadcrumb & Header -->
    <div class="mb-4">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-1">
                <li class="breadcrumb-item"><a href="{{ route('dashboard') }}" class="text-decoration-none">Dashboard</a></li>
                <li class="breadcrumb-item"><a href="{{ route('consignments.index') }}" class="text-decoration-none">Konsinyasi</a></li>
                <li class="breadcrumb-item active">Kirim Titipan Baru</li>
            </ol>
        </nav>
        <h3 class="fw-bold mb-0 text-dark">
            <i class="bi bi-box-arrow-right text-warning me-2"></i> Kirim Titip Kopi ke Toko Mitra
        </h3>
        <p class="text-muted small mb-0">Barang akan dipotong dari stok gudang utama dan dicatat sebagai persediaan titipan di rak toko mitra.</p>
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

    <form method="POST" action="{{ route('consignments.store') }}" id="formCreateConsignment">
        @csrf

        <div class="row g-4">
            <!-- Informasi Header Penitipan -->
            <div class="col-lg-4">
                <div class="card card-custom p-4 shadow-sm mb-4">
                    <h5 class="fw-bold text-dark mb-3 pb-2 border-bottom">
                        <i class="bi bi-shop me-2 text-warning"></i> Data Toko Mitra
                    </h5>

                    <div class="mb-3">
                        <label for="customer_id" class="form-label fw-semibold">Pilih Toko / Warung Mitra <span class="text-danger">*</span></label>
                        <select name="customer_id" id="customer_id" class="form-select form-select-searchable @error('customer_id') is-invalid @enderror" data-placeholder="-- Ketik nama toko, PIC, atau alamat --" required>
                            <option value="">-- Pilih Toko Mitra --</option>
                            @foreach($customers as $c)
                                <option value="{{ $c->id }}" 
                                    data-pic="{{ $c->pic_name }}"
                                    data-phone="{{ $c->phone }}"
                                    data-address="{{ $c->shipping_address ?? $c->address }}"
                                    data-type="{{ $c->type_label }}"
                                    {{ old('customer_id') == $c->id ? 'selected' : '' }}>
                                    {{ $c->business_name }} ({{ $c->pic_name }})
                                </option>
                            @endforeach
                        </select>
                        <small class="text-muted" style="font-size: 0.72rem;">Ketik nama warung, cafe, atau nama pemilik untuk mencari seketika.</small>

                        <!-- Live Store Profile Card -->
                        <div id="customerInfoCard" class="card bg-warning bg-opacity-10 border border-warning rounded-3 p-3 mt-2 d-none">
                            <div class="d-flex justify-content-between align-items-start mb-1">
                                <h6 class="fw-bold text-dark mb-0" id="cardStoreName">Nama Toko</h6>
                                <span class="badge bg-warning text-dark" id="cardStoreType">Konsinyasi</span>
                            </div>
                            <div class="small text-secondary mb-1">
                                <i class="bi bi-person-fill text-warning me-1"></i> PIC: <strong id="cardPicName" class="text-dark">-</strong>
                                &bull; <i class="bi bi-telephone-fill text-warning me-1 ms-1"></i> <span id="cardPhone" class="text-dark">-</span>
                                <a href="#" id="cardWaBtn" target="_blank" class="badge bg-success text-white text-decoration-none ms-1" title="Chat WhatsApp">
                                    <i class="bi bi-whatsapp"></i> Chat
                                </a>
                            </div>
                            <div class="small text-secondary">
                                <i class="bi bi-geo-alt-fill text-warning me-1"></i> <span id="cardAddress">-</span>
                            </div>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="delivery_date" class="form-label fw-semibold">Tanggal Titip Barang <span class="text-danger">*</span></label>
                        <input type="date" name="delivery_date" id="delivery_date" class="form-control" value="{{ old('delivery_date', date('Y-m-d')) }}" required>
                    </div>

                    <div class="mb-3">
                        <label for="sales_user_id" class="form-label fw-semibold">Sales Penanggung Jawab (PIC)</label>
                        <select name="sales_user_id" id="sales_user_id" class="form-select form-select-searchable" data-placeholder="-- Cari Sales Canvasser / PIC --">
                            <option value="">-- Pilih Sales Canvasser --</option>
                            @foreach($salesUsers as $s)
                                <option value="{{ $s->id }}" data-role="{{ ucfirst($s->role) }}" {{ old('sales_user_id', auth()->id()) == $s->id ? 'selected' : '' }}>
                                    {{ $s->name }} ({{ ucfirst($s->role) }})
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="mb-3">
                        <label for="notes" class="form-label fw-semibold">Catatan / Posisi Rak Toko</label>
                        <textarea name="notes" id="notes" rows="3" class="form-control" placeholder="Contoh: Dititip di rak kasir depan, etalase kaca baris 2">{{ old('notes') }}</textarea>
                    </div>

                    <div class="alert alert-info py-2 px-3 small mb-0">
                        <i class="bi bi-info-circle me-1"></i> Toko <strong>belum ditagih uang</strong> saat barang dikirim. Tagihan hanya dihitung saat survei mingguan berdasarkan produk yang laku.
                    </div>
                </div>
            </div>

            <!-- Tabel Produk Kopi yang Dititipkan -->
            <div class="col-lg-8">
                <div class="card card-custom p-4 shadow-sm mb-4">
                    <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-3 pb-2 border-bottom">
                        <div>
                            <h5 class="fw-bold text-dark mb-0">
                                <i class="bi bi-boxes me-2 text-warning"></i> Daftar Produk Kopi yang Dititipkan
                            </h5>
                            <small class="text-muted">Cari varian di tabel atau gunakan Katalog Cepat untuk memilih banyak varian sekaligus.</small>
                        </div>
                        <div class="d-flex gap-2">
                            <button type="button" class="btn btn-sm btn-warning text-dark fw-bold shadow-sm" data-bs-toggle="modal" data-bs-target="#quickProductModal">
                                <i class="bi bi-grid-3x3-gap-fill me-1"></i> Katalog Cepat (Pilih Varian)
                            </button>
                            <button type="button" class="btn btn-sm btn-outline-secondary" onclick="addRow()">
                                <i class="bi bi-plus-lg me-1"></i> Tambah Baris
                            </button>
                        </div>
                    </div>

                    <div class="table-responsive mb-3" style="min-height: 250px;">
                        <table class="table align-middle" id="itemsTable">
                            <thead class="table-light">
                                <tr>
                                    <th style="min-width: 280px;">Pilih Varian Kopi &amp; SKU</th>
                                    <th style="width: 120px;" class="text-center">Stok Gudang</th>
                                    <th style="width: 130px;" class="text-center">Jumlah Titip <span class="text-danger">*</span></th>
                                    <th style="width: 150px;" class="text-end">Harga Standar</th>
                                    <th style="width: 50px;" class="text-center">#</th>
                                </tr>
                            </thead>
                            <tbody id="itemsBody">
                                <!-- Baris Pertama Default -->
                                <tr class="item-row" data-row="0">
                                    <td>
                                        <select name="items[0][variant_id]" class="form-select variant-select form-select-searchable" data-placeholder="-- Ketik atau cari varian kopi & SKU --" onchange="updateVariantDetails(this)" required>
                                            <option value="">-- Pilih Varian Produk --</option>
                                            @foreach($products as $prod)
                                                <optgroup label="{{ $prod->name }} ({{ $prod->brand }})">
                                                    @foreach($prod->variants as $v)
                                                        <option value="{{ $v->id }}" 
                                                            data-stock="{{ $v->current_stock }}" 
                                                            data-price="{{ $v->selling_price }}" 
                                                            data-sku="{{ $v->sku }}">
                                                            {{ $v->variant_name }} [{{ $v->sku }}] &mdash; Rp {{ number_format($v->selling_price, 0, ',', '.') }}
                                                        </option>
                                                    @endforeach
                                                </optgroup>
                                            @endforeach
                                        </select>
                                    </td>
                                    <td class="text-center">
                                        <span class="badge bg-light text-dark border stock-badge">0 pack</span>
                                    </td>
                                    <td>
                                        <input type="number" name="items[0][quantity]" class="form-control text-center item-qty" value="10" min="1" required>
                                    </td>
                                    <td class="text-end">
                                        <input type="hidden" name="items[0][price]" class="item-price-input" value="0">
                                        <span class="fw-bold text-dark item-price-display">Rp 0</span>
                                    </td>
                                    <td class="text-center">
                                        <button type="button" class="btn btn-sm btn-outline-danger" onclick="removeRow(this)" disabled title="Hapus Baris">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <div class="p-3 bg-light rounded-3 d-flex justify-content-between align-items-center">
                        <span class="text-muted small">Total Estimasi Nilai Titip Barang:</span>
                        <h4 class="fw-bold text-dark mb-0" id="totalEstimatedValue">Rp 0</h4>
                    </div>

                    <div class="d-flex justify-content-end gap-2 mt-4 pt-3 border-top">
                        <a href="{{ route('consignments.index') }}" class="btn btn-outline-secondary px-4">Batal</a>
                        <button type="submit" class="btn btn-warning btn-lg fw-bold text-dark shadow-sm px-5">
                            <i class="bi bi-check2-circle me-1"></i> Simpan &amp; Potong Stok Gudang
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>

<!-- ═══════════════════════════════════════════════════════════
     MODAL KATALOG CEPAT PILIH VARIAN KOPI
     ═══════════════════════════════════════════════════════════ -->
<div class="modal fade" id="quickProductModal" tabindex="-1" aria-labelledby="quickProductModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content border-0 shadow-lg">
            <div class="modal-header bg-dark text-white p-3">
                <div class="d-flex align-items-center gap-2">
                    <div class="p-2 bg-warning rounded-3 text-dark">
                        <i class="bi bi-grid-3x3-gap-fill fs-5"></i>
                    </div>
                    <div>
                        <h5 class="modal-title fw-bold text-white mb-0" id="quickProductModalLabel">Katalog Varian Kopi &mdash; Pilih Cepat</h5>
                        <small class="text-white-50">Cari, atur jumlah, dan masukkan beberapa varian kopi sekaligus ke dalam titipan konsinyasi</small>
                    </div>
                </div>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-3 p-md-4 bg-light">
                <!-- Search & Filters -->
                <div class="card card-custom p-3 mb-3 border-0 shadow-sm">
                    <div class="row g-2 align-items-center">
                        <div class="col-md-7">
                            <div class="input-group">
                                <span class="input-group-text bg-white border-end-0 text-muted"><i class="bi bi-search"></i></span>
                                <input type="text" id="modalSearchInput" class="form-control border-start-0" placeholder="Ketik nama kopi, SKU, gramasi (contoh: Gayo, 250g, Biji)...">
                            </div>
                        </div>
                        <div class="col-md-5">
                            <div class="d-flex flex-wrap gap-1" id="modalFilterChips">
                                <button type="button" class="btn btn-sm btn-dark filter-chip active" data-filter="all">Semua Varian</button>
                                <button type="button" class="btn btn-sm btn-outline-secondary filter-chip" data-filter="biji">Biji Kopi</button>
                                <button type="button" class="btn btn-sm btn-outline-secondary filter-chip" data-filter="bubuk">Bubuk</button>
                                <button type="button" class="btn btn-sm btn-outline-secondary filter-chip" data-filter="arabica">Arabica</button>
                                <button type="button" class="btn btn-sm btn-outline-secondary filter-chip" data-filter="robusta">Robusta</button>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Variant Cards Grid -->
                <div class="row g-3" id="modalVariantGrid">
                    @foreach($products as $prod)
                        @foreach($prod->variants as $v)
                            <div class="col-md-6 col-lg-4 modal-variant-card"
                                data-id="{{ $v->id }}"
                                data-name="{{ $prod->name }} - {{ $v->variant_name }}"
                                data-sku="{{ $v->sku }}"
                                data-stock="{{ $v->current_stock }}"
                                data-price="{{ $v->selling_price }}"
                                data-search="{{ strtolower($prod->name . ' ' . $v->variant_name . ' ' . $v->sku . ' ' . $v->package_weight_grams . ' ' . $v->grind_form . ' ' . $prod->coffee_type . ' ' . $prod->form) }}"
                                data-form="{{ strtolower($prod->form ?? '') }}"
                                data-type="{{ strtolower($prod->coffee_type ?? '') }}"
                                data-grind="{{ strtolower($v->grind_form ?? '') }}">
                                <div class="card h-100 border p-3 variant-pick-card position-relative shadow-sm">
                                    <div class="d-flex justify-content-between align-items-start mb-2">
                                        <span class="badge bg-secondary font-monospace">{{ $v->sku }}</span>
                                        @if($v->current_stock > 10)
                                            <span class="badge bg-success bg-opacity-10 text-success border border-success">
                                                <i class="bi bi-check-circle me-1"></i> Stok {{ $v->current_stock }} pack
                                            </span>
                                        @elseif($v->current_stock > 0)
                                            <span class="badge bg-warning bg-opacity-10 text-warning-emphasis border border-warning">
                                                <i class="bi bi-exclamation-circle me-1"></i> Sisa {{ $v->current_stock }} pack
                                            </span>
                                        @else
                                            <span class="badge bg-danger bg-opacity-10 text-danger border border-danger">
                                                <i class="bi bi-x-circle me-1"></i> Stok Habis
                                            </span>
                                        @endif
                                    </div>
                                    <h6 class="fw-bold text-dark mb-1">{{ $prod->name }}</h6>
                                    <p class="text-muted small mb-3">
                                        {{ $v->variant_name }} &bull; {{ $v->package_weight_grams }}g &bull; {{ ucfirst($v->grind_form ?? 'Biji') }}
                                    </p>
                                    <div class="d-flex justify-content-between align-items-center mt-auto pt-2 border-top">
                                        <div>
                                            <small class="text-muted d-block" style="font-size: 0.72rem;">Harga Titip</small>
                                            <span class="fw-bold text-dark">Rp {{ number_format($v->selling_price, 0, ',', '.') }}</span>
                                        </div>
                                        <div class="d-flex align-items-center gap-1">
                                            <input type="number" class="form-control form-control-sm text-center quick-qty-input" value="10" min="1" max="{{ $v->current_stock > 0 ? $v->current_stock : 10 }}" style="width: 65px;" title="Jumlah Titip">
                                            <button type="button" class="btn btn-sm btn-warning text-dark fw-bold btn-add-from-modal" title="Tambahkan varian ini">
                                                <i class="bi bi-plus-lg me-1"></i> Pilih
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    @endforeach
                </div>

                <div id="modalEmptyState" class="text-center py-5 d-none">
                    <i class="bi bi-search text-muted display-4 mb-2 d-block"></i>
                    <h6 class="fw-bold text-muted">Tidak ditemukan varian kopi yang cocok</h6>
                    <p class="text-muted small">Coba gunakan kata kunci lain seperti "250g", "Gayo", "Biji", atau "Bubuk".</p>
                </div>
            </div>
            <div class="modal-footer bg-white d-flex justify-content-between align-items-center p-3">
                <span class="text-muted small">
                    <i class="bi bi-lightning-charge text-warning me-1"></i> Klik <strong>Pilih</strong> pada kartu kopi untuk langsung memasukkan ke daftar.
                </span>
                <button type="button" class="btn btn-secondary px-4" data-bs-dismiss="modal">Selesai &amp; Tutup</button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    let rowIndex = 1;

    // Template options HTML untuk pembuatan baris baru
    const baseVariantOptionsHtml = `
        <option value="">-- Pilih Varian Produk --</option>
        @foreach($products as $prod)
            <optgroup label="{{ $prod->name }} ({{ $prod->brand }})">
                @foreach($prod->variants as $v)
                    <option value="{{ $v->id }}" 
                        data-stock="{{ $v->current_stock }}" 
                        data-price="{{ $v->selling_price }}" 
                        data-sku="{{ $v->sku }}">
                        {{ $v->variant_name }} [{{ $v->sku }}] &mdash; Rp {{ number_format($v->selling_price, 0, ',', '.') }}
                    </option>
                @endforeach
            </optgroup>
        @endforeach
    `;

    function updateVariantDetails(selectEl) {
        const row = selectEl.closest('.item-row');
        const selectedOpt = selectEl.options[selectEl.selectedIndex];
        
        if (selectedOpt && selectedOpt.value) {
            const stock = parseInt(selectedOpt.getAttribute('data-stock') || 0);
            const price = parseFloat(selectedOpt.getAttribute('data-price') || 0);

            const stockBadge = row.querySelector('.stock-badge');
            stockBadge.textContent = stock + ' pack';
            if (stock <= 0) {
                stockBadge.className = 'badge bg-danger stock-badge';
            } else {
                stockBadge.className = 'badge bg-success bg-opacity-10 text-success border border-success stock-badge';
            }

            row.querySelector('.item-price-input').value = price;
            row.querySelector('.item-price-display').textContent = 'Rp ' + price.toLocaleString('id-ID');

            const qtyInput = row.querySelector('.item-qty');
            qtyInput.max = stock > 0 ? stock : 10;
        } else {
            row.querySelector('.stock-badge').textContent = '0 pack';
            row.querySelector('.stock-badge').className = 'badge bg-light text-dark border stock-badge';
            row.querySelector('.item-price-input').value = 0;
            row.querySelector('.item-price-display').textContent = 'Rp 0';
        }

        calcTotal();
    }

    function addRow(variantId = '', initialQty = 10) {
        const body = document.getElementById('itemsBody');
        const tr = document.createElement('tr');
        tr.className = 'item-row';
        tr.dataset.row = rowIndex;

        tr.innerHTML = `
            <td>
                <select name="items[${rowIndex}][variant_id]" class="form-select variant-select form-select-searchable" data-placeholder="-- Ketik atau cari varian kopi & SKU --" onchange="updateVariantDetails(this)" required>
                    ${baseVariantOptionsHtml}
                </select>
            </td>
            <td class="text-center">
                <span class="badge bg-light text-dark border stock-badge">0 pack</span>
            </td>
            <td>
                <input type="number" name="items[${rowIndex}][quantity]" class="form-control text-center item-qty" value="${initialQty}" min="1" required>
            </td>
            <td class="text-end">
                <input type="hidden" name="items[${rowIndex}][price]" class="item-price-input" value="0">
                <span class="fw-bold text-dark item-price-display">Rp 0</span>
            </td>
            <td class="text-center">
                <button type="button" class="btn btn-sm btn-outline-danger" onclick="removeRow(this)" title="Hapus Baris">
                    <i class="bi bi-trash"></i>
                </button>
            </td>
        `;

        body.appendChild(tr);

        const sel = tr.querySelector('.variant-select');
        if (variantId) {
            sel.value = variantId;
        }

        // Initialize searchable select on the new element
        if (typeof window.KopiSearchSelect === 'function') {
            new window.KopiSearchSelect(sel);
        }

        updateVariantDetails(sel);
        rowIndex++;
        updateDeleteButtons();
        calcTotal();
        return tr;
    }

    function removeRow(btn) {
        btn.closest('.item-row').remove();
        updateDeleteButtons();
        calcTotal();
    }

    function updateDeleteButtons() {
        const rows = document.querySelectorAll('.item-row');
        rows.forEach(r => {
            const btn = r.querySelector('button');
            if (rows.length === 1) {
                btn.setAttribute('disabled', 'disabled');
            } else {
                btn.removeAttribute('disabled');
            }
        });
    }

    function calcTotal() {
        let total = 0;
        document.querySelectorAll('.item-row').forEach(r => {
            const qty = parseInt(r.querySelector('.item-qty').value || 0);
            const price = parseFloat(r.querySelector('.item-price-input').value || 0);
            total += (qty * price);
        });

        document.getElementById('totalEstimatedValue').textContent = 'Rp ' + total.toLocaleString('id-ID');
    }

    document.addEventListener('input', function(e) {
        if (e.target && e.target.classList.contains('item-qty')) {
            calcTotal();
        }
    });

    // Customer Live Preview Card
    const customerSelect = document.getElementById('customer_id');
    const customerCard = document.getElementById('customerInfoCard');

    function updateCustomerPreview() {
        if (!customerSelect || !customerCard) return;
        const opt = customerSelect.options[customerSelect.selectedIndex];
        if (opt && opt.value) {
            document.getElementById('cardStoreName').textContent = opt.text.split('(')[0].trim();
            document.getElementById('cardStoreType').textContent = opt.getAttribute('data-type') || 'Konsinyasi';
            document.getElementById('cardPicName').textContent = opt.getAttribute('data-pic') || '-';
            
            const phone = opt.getAttribute('data-phone') || '-';
            document.getElementById('cardPhone').textContent = phone;
            
            const waBtn = document.getElementById('cardWaBtn');
            if (phone && phone !== '-') {
                let cleanPhone = phone.replace(/[^0-9]/g, '');
                if (cleanPhone.startsWith('0')) cleanPhone = '62' + cleanPhone.substring(1);
                waBtn.href = `https://wa.me/${cleanPhone}`;
                waBtn.style.display = 'inline-block';
            } else {
                waBtn.style.display = 'none';
            }

            document.getElementById('cardAddress').textContent = opt.getAttribute('data-address') || 'Alamat belum diatur';
            customerCard.classList.remove('d-none');
        } else {
            customerCard.classList.add('d-none');
        }
    }

    if (customerSelect) {
        customerSelect.addEventListener('change', updateCustomerPreview);
        updateCustomerPreview();
    }

    // Modal Quick Catalog Logic
    document.addEventListener('DOMContentLoaded', function() {
        const modalSearchInput = document.getElementById('modalSearchInput');
        const filterChips = document.querySelectorAll('#modalFilterChips .filter-chip');
        const variantCards = document.querySelectorAll('.modal-variant-card');
        const emptyState = document.getElementById('modalEmptyState');

        let currentFilter = 'all';
        let currentSearch = '';

        function filterModalVariants() {
            let visibleCount = 0;
            variantCards.forEach(card => {
                const searchText = card.getAttribute('data-search') || '';
                const form = card.getAttribute('data-form') || '';
                const type = card.getAttribute('data-type') || '';
                const grind = card.getAttribute('data-grind') || '';

                let matchesFilter = true;
                if (currentFilter === 'biji') {
                    matchesFilter = form.includes('bean') || form.includes('biji') || grind.includes('biji');
                } else if (currentFilter === 'bubuk') {
                    matchesFilter = form.includes('powder') || form.includes('bubuk') || grind.includes('halus') || grind.includes('kasar');
                } else if (currentFilter === 'arabica') {
                    matchesFilter = type.includes('arabica');
                } else if (currentFilter === 'robusta') {
                    matchesFilter = type.includes('robusta');
                }

                const matchesSearch = !currentSearch || searchText.includes(currentSearch);

                if (matchesFilter && matchesSearch) {
                    card.classList.remove('d-none');
                    visibleCount++;
                } else {
                    card.classList.add('d-none');
                }
            });

            if (visibleCount === 0) {
                emptyState.classList.remove('d-none');
            } else {
                emptyState.classList.add('d-none');
            }
        }

        if (modalSearchInput) {
            modalSearchInput.addEventListener('input', function() {
                currentSearch = this.value.trim().toLowerCase();
                filterModalVariants();
            });
        }

        filterChips.forEach(chip => {
            chip.addEventListener('click', function() {
                filterChips.forEach(c => {
                    c.classList.remove('btn-dark', 'active');
                    c.classList.add('btn-outline-secondary');
                });
                this.classList.remove('btn-outline-secondary');
                this.classList.add('btn-dark', 'active');
                currentFilter = this.getAttribute('data-filter');
                filterModalVariants();
            });
        });

        // Add from modal handler
        document.querySelectorAll('.btn-add-from-modal').forEach(btn => {
            btn.addEventListener('click', function() {
                const card = this.closest('.modal-variant-card');
                const variantId = card.getAttribute('data-id');
                const qtyInput = card.querySelector('.quick-qty-input');
                const qty = parseInt(qtyInput.value || 10);

                // Cek apakah varian sudah ada di tabel
                let existingRow = null;
                document.querySelectorAll('#itemsBody .item-row').forEach(row => {
                    const sel = row.querySelector('.variant-select');
                    if (sel && sel.value === variantId) {
                        existingRow = row;
                    }
                });

                if (existingRow) {
                    // Tambahkan qty ke baris yang sudah ada
                    const curQtyInput = existingRow.querySelector('.item-qty');
                    curQtyInput.value = parseInt(curQtyInput.value || 0) + qty;
                    calcTotal();
                } else {
                    // Cek apakah baris pertama masih kosong (belum pilih varian)
                    const firstRow = document.querySelector('#itemsBody .item-row');
                    const firstSelect = firstRow ? firstRow.querySelector('.variant-select') : null;
                    if (firstSelect && !firstSelect.value) {
                        firstSelect.value = variantId;
                        if (firstSelect._kopiSearchSelect) {
                            firstSelect._kopiSearchSelect.syncFromSelect();
                        }
                        firstRow.querySelector('.item-qty').value = qty;
                        updateVariantDetails(firstSelect);
                    } else {
                        addRow(variantId, qty);
                    }
                }

                // Visual button feedback
                const origHtml = this.innerHTML;
                this.className = 'btn btn-sm btn-success text-white fw-bold';
                this.innerHTML = '<i class="bi bi-check-lg me-1"></i> Ditambah';
                setTimeout(() => {
                    this.className = 'btn btn-sm btn-warning text-dark fw-bold btn-add-from-modal';
                    this.innerHTML = origHtml;
                }, 1000);
            });
        });
    });
</script>
@endpush
