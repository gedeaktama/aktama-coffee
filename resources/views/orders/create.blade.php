@extends('layouts.app')

@section('title', 'Input Pesanan Grosir B2B - Kopi Distro')

@section('content')
<div class="container py-3">
    <!-- Breadcrumb & Title -->
    <div class="mb-4">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-1">
                <li class="breadcrumb-item"><a href="{{ route('orders.index') }}" class="text-decoration-none">Penjualan</a></li>
                <li class="breadcrumb-item active" aria-current="page">Input Order B2B</li>
            </ol>
        </nav>
        <h2 class="fw-bold text-dark mb-1">Input Pesanan Penjualan Grosir (B2B)</h2>
        <p class="text-muted mb-0">Terbitkan pesanan penjualan untuk mitra kafe, toko kelontong, atau reseller dengan kontrol batas kredit.</p>
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

    <form method="POST" action="{{ route('orders.store') }}" id="orderForm">
        @csrf

        <div class="row g-4">
            <!-- Left Column: Customer & Payment Terms -->
            <div class="col-lg-4">
                <div class="card card-custom p-4 mb-4">
                    <h5 class="fw-bold text-dark mb-3 pb-2 border-bottom">
                        <i class="bi bi-person-badge me-2 text-primary"></i> Data Pelanggan & Kredit
                    </h5>

                    <div class="mb-3">
                        <label for="order_number" class="form-label fw-semibold">Nomor Sales Order <span class="text-danger">*</span></label>
                        <input type="text" name="order_number" id="order_number" class="form-control font-monospace fw-bold @error('order_number') is-invalid @enderror" value="{{ old('order_number', $autoOrderNumber) }}" required>
                    </div>

                    <div class="mb-3">
                        <label for="order_date" class="form-label fw-semibold">Tanggal Order <span class="text-danger">*</span></label>
                        <input type="date" name="order_date" id="order_date" class="form-control @error('order_date') is-invalid @enderror" value="{{ old('order_date', date('Y-m-d')) }}" required>
                    </div>

                    <div class="mb-3">
                        <label for="customer_id" class="form-label fw-semibold">Pilih Mitra Pelanggan B2B <span class="text-danger">*</span></label>
                        <select name="customer_id" id="customer_id" class="form-select @error('customer_id') is-invalid @enderror" required>
                            <option value="">-- Pilih Pelanggan B2B --</option>
                            @foreach($customers as $c)
                                <option value="{{ $c->id }}" 
                                    data-type="{{ $c->type_label }}"
                                    data-pic="{{ $c->pic_name }}"
                                    data-phone="{{ $c->phone }}"
                                    data-address="{{ $c->shipping_address ?? $c->address }}"
                                    data-credit-limit="{{ (float)$c->credit_limit }}"
                                    data-receivables="{{ (float)$c->getCurrentReceivables() }}"
                                    data-available-credit="{{ (float)$c->getAvailableCredit() }}"
                                    data-terms="{{ $c->payment_terms_days }}"
                                    {{ old('customer_id') == $c->id ? 'selected' : '' }}>
                                    {{ $c->business_name }} ({{ $c->type_label }})
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Customer Credit Status Box -->
                    <div id="creditBox" class="p-3 bg-light rounded-3 border mb-3 d-none">
                        <div class="small fw-bold text-secondary text-uppercase mb-2">Profil Kredit Pelanggan</div>
                        <div class="d-flex justify-content-between small mb-1">
                            <span class="text-muted">Plafon Kredit:</span>
                            <strong id="dispCreditLimit" class="text-dark">Rp 0</strong>
                        </div>
                        <div class="d-flex justify-content-between small mb-1">
                            <span class="text-muted">Piutang Berjalan:</span>
                            <span id="dispReceivables" class="text-danger fw-semibold">Rp 0</span>
                        </div>
                        <div class="d-flex justify-content-between small mb-1">
                            <span class="text-muted">Sisa Plafon Kredit:</span>
                            <span id="dispAvailableCredit" class="text-success fw-bold">Rp 0</span>
                        </div>
                        <div class="d-flex justify-content-between small border-top pt-1 mt-1">
                            <span class="text-muted">Termin Tempo (TOP):</span>
                            <strong id="dispTerms" class="text-primary">0 Hari</strong>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="payment_type" class="form-label fw-semibold">Metode Pembayaran <span class="text-danger">*</span></label>
                        <select name="payment_type" id="payment_type" class="form-select" required>
                            <option value="cash" {{ old('payment_type') === 'cash' ? 'selected' : '' }}>Tunai Langsung (Cash)</option>
                            <option value="transfer" {{ old('payment_type') === 'transfer' ? 'selected' : '' }}>Transfer Bank</option>
                            <option value="credit" id="optCredit" {{ old('payment_type') === 'credit' ? 'selected' : '' }}>Tempo / Kredit (TOP)</option>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label for="recipient_name" class="form-label fw-semibold">Nama PIC Penerima</label>
                        <input type="text" name="recipient_name" id="recipient_name" class="form-control" value="{{ old('recipient_name') }}" placeholder="Nama penerima pengiriman">
                    </div>

                    <div class="mb-3">
                        <label for="recipient_phone" class="form-label fw-semibold">Kontak / WhatsApp Penerima</label>
                        <input type="text" name="recipient_phone" id="recipient_phone" class="form-control" value="{{ old('recipient_phone') }}" placeholder="Nomor telepon aktif">
                    </div>

                    <div class="mb-3">
                        <label for="shipping_address" class="form-label fw-semibold">Alamat Pengiriman</label>
                        <textarea name="shipping_address" id="shipping_address" rows="2" class="form-control" placeholder="Alamat lengkap tujuan kirim">{{ old('shipping_address') }}</textarea>
                    </div>

                    <div class="mb-3">
                        <label for="notes" class="form-label fw-semibold">Catatan Pesanan</label>
                        <textarea name="notes" id="notes" rows="2" class="form-control" placeholder="Instruksi pengemasan, jadwal kirim">{{ old('notes') }}</textarea>
                    </div>
                </div>
            </div>

            <!-- Right Column: Products Table & Summary -->
            <div class="col-lg-8">
                <div class="card card-custom p-4 mb-4">
                    <div class="d-flex justify-content-between align-items-center mb-3 pb-2 border-bottom">
                        <h5 class="fw-bold text-dark mb-0">
                            <i class="bi bi-box-seam me-2 text-primary"></i> Varian Kopi yang Dipesan
                        </h5>
                        <button type="button" class="btn btn-sm btn-outline-success" id="btnAddRow">
                            <i class="bi bi-plus-circle me-1"></i> Tambah Item Kopi
                        </button>
                    </div>

                    <div class="table-responsive mb-3">
                        <table class="table table-bordered align-middle" id="itemsTable">
                            <thead class="table-light">
                                <tr>
                                    <th style="min-width: 250px;">SKU & Varian Kopi <span class="text-danger">*</span></th>
                                    <th style="width: 120px;" class="text-center">Jumlah (Pack) <span class="text-danger">*</span></th>
                                    <th style="width: 170px;" class="text-end">Harga Jual Standar</th>
                                    <th style="width: 170px;" class="text-end">Subtotal (Rp)</th>
                                    <th style="width: 50px;" class="text-center">#</th>
                                </tr>
                            </thead>
                            <tbody id="itemsBody">
                                <!-- Dynamic rows via JS -->
                            </tbody>
                        </table>
                    </div>

                    <!-- Costs & Summary Box -->
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
                                <label for="shipping_cost" class="form-label small fw-semibold text-muted">Biaya Kirim Logistik (Rp)</label>
                                <div class="input-group input-group-sm">
                                    <span class="input-group-text">Rp</span>
                                    <input type="number" name="shipping_cost" id="shipping_cost" class="form-control text-end" value="{{ old('shipping_cost', 0) }}" min="0" step="100">
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="text-md-end pt-md-2">
                                    <small class="text-muted d-block">Subtotal Item: <span id="lblSubtotal" class="fw-bold text-dark">Rp 0</span></small>
                                    <div class="fs-5 fw-bold text-coffee mt-1">
                                        Total Order: <span id="lblTotal" class="text-success">Rp 0</span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Credit Alert Warning -->
                        <div id="creditAlert" class="alert alert-warning py-2 px-3 small border-0 mt-3 mb-0 d-none">
                            <i class="bi bi-exclamation-triangle-fill me-1"></i>
                            <span id="creditAlertText"></span>
                        </div>
                    </div>

                    <div class="d-flex justify-content-between align-items-center mt-4 pt-3 border-top">
                        <a href="{{ route('orders.index') }}" class="btn btn-outline-secondary">
                            <i class="bi bi-arrow-left me-1"></i> Batal & Kembali
                        </a>
                        <button type="submit" class="btn btn-coffee px-4 shadow-sm">
                            <i class="bi bi-check2-circle me-1"></i> Terbitkan Sales Order B2B
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>

<!-- Variant Options Data -->
<script>
    const availableVariants = @json($variantsJson ?? []);
</script>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const customerSelect = document.getElementById('customer_id');
    const creditBox = document.getElementById('creditBox');
    const dispCreditLimit = document.getElementById('dispCreditLimit');
    const dispReceivables = document.getElementById('dispReceivables');
    const dispAvailableCredit = document.getElementById('dispAvailableCredit');
    const dispTerms = document.getElementById('dispTerms');
    const paymentSelect = document.getElementById('payment_type');
    const optCredit = document.getElementById('optCredit');
    const creditAlert = document.getElementById('creditAlert');
    const creditAlertText = document.getElementById('creditAlertText');

    const inRecipientName = document.getElementById('recipient_name');
    const inRecipientPhone = document.getElementById('recipient_phone');
    const inShippingAddress = document.getElementById('shipping_address');

    const itemsBody = document.getElementById('itemsBody');
    const btnAddRow = document.getElementById('btnAddRow');
    const discountInput = document.getElementById('discount');
    const shippingInput = document.getElementById('shipping_cost');
    const lblSubtotal = document.getElementById('lblSubtotal');
    const lblTotal = document.getElementById('lblTotal');

    let currentAvailableCredit = 0;
    let rowIndex = 0;

    function formatRupiah(num) {
        return 'Rp ' + Number(num).toLocaleString('id-ID');
    }

    function updateCustomerDetails() {
        const opt = customerSelect.options[customerSelect.selectedIndex];
        if (opt && opt.value) {
            creditBox.classList.remove('d-none');
            const limit = parseFloat(opt.dataset.creditLimit) || 0;
            const rec = parseFloat(opt.dataset.receivables) || 0;
            const avail = parseFloat(opt.dataset.availableCredit) || 0;
            const terms = parseInt(opt.dataset.terms) || 0;

            currentAvailableCredit = avail;

            dispCreditLimit.textContent = formatRupiah(limit);
            dispReceivables.textContent = formatRupiah(rec);
            dispAvailableCredit.textContent = formatRupiah(avail);
            dispTerms.textContent = terms + ' Hari';

            // Fill recipient fields if empty
            if (!inRecipientName.value) inRecipientName.value = opt.dataset.pic || '';
            if (!inRecipientPhone.value) inRecipientPhone.value = opt.dataset.phone || '';
            if (!inShippingAddress.value) inShippingAddress.value = opt.dataset.address || '';

            if (terms <= 0) {
                optCredit.disabled = true;
                optCredit.text = 'Tempo / Kredit (Tidak Ada Fasilitas TOP)';
                if (paymentSelect.value === 'credit') {
                    paymentSelect.value = 'cash';
                }
            } else {
                optCredit.disabled = false;
                optCredit.text = `Tempo / Kredit (TOP ${terms} Hari)`;
            }
        } else {
            creditBox.classList.add('d-none');
            optCredit.disabled = false;
            optCredit.text = 'Tempo / Kredit (TOP)';
            currentAvailableCredit = 0;
        }

        recalculate();
    }

    function addRow(variantId = '', qty = 1) {
        const tr = document.createElement('tr');
        tr.className = 'item-row';
        tr.dataset.index = rowIndex;

        let optionsHtml = '<option value="">-- Pilih Varian Kopi & SKU --</option>';
        availableVariants.forEach(v => {
            const selected = (v.id == variantId) ? 'selected' : '';
            optionsHtml += `<option value="${v.id}" data-price="${v.price}" data-stock="${v.current_stock}" ${selected}>${v.sku} &mdash; ${v.name} (Stok: ${v.current_stock})</option>`;
        });

        tr.innerHTML = `
            <td>
                <select name="items[${rowIndex}][product_variant_id]" class="form-select form-select-sm select-variant" required>
                    ${optionsHtml}
                </select>
                <small class="stock-info text-muted d-block mt-1"></small>
            </td>
            <td>
                <input type="number" name="items[${rowIndex}][quantity]" class="form-control form-select-sm text-center input-qty" value="${qty}" min="1" required>
            </td>
            <td class="text-end fw-semibold line-price text-secondary">
                Rp 0
            </td>
            <td class="text-end fw-bold line-subtotal text-dark">
                Rp 0
            </td>
            <td class="text-center">
                <button type="button" class="btn btn-sm btn-outline-danger btn-remove-row p-1" title="Hapus Baris">
                    <i class="bi bi-trash"></i>
                </button>
            </td>
        `;

        itemsBody.appendChild(tr);

        const selVariant = tr.querySelector('.select-variant');
        const inQty = tr.querySelector('.input-qty');
        const btnRemove = tr.querySelector('.btn-remove-row');
        const lblStock = tr.querySelector('.stock-info');

        selVariant.addEventListener('change', function () {
            const opt = this.options[this.selectedIndex];
            if (opt && opt.dataset.price) {
                const stock = parseInt(opt.dataset.stock) || 0;
                lblStock.textContent = `Tersedia di gudang: ${stock} pack`;
            } else {
                lblStock.textContent = '';
            }
            recalculate();
        });

        inQty.addEventListener('input', recalculate);

        btnRemove.addEventListener('click', function () {
            if (document.querySelectorAll('.item-row').length > 1) {
                tr.remove();
                recalculate();
            } else {
                alert('Pesanan penjualan harus memiliki minimal 1 item produk.');
            }
        });

        rowIndex++;
        recalculate();
    }

    function recalculate() {
        let subtotal = 0;
        document.querySelectorAll('.item-row').forEach(tr => {
            const selVariant = tr.querySelector('.select-variant');
            const inQty = tr.querySelector('.input-qty');
            const linePriceEl = tr.querySelector('.line-price');
            const lineSubtotalEl = tr.querySelector('.line-subtotal');

            const opt = selVariant.options[selVariant.selectedIndex];
            const price = opt && opt.dataset.price ? parseFloat(opt.dataset.price) : 0;
            const qty = parseFloat(inQty.value) || 0;
            const lineTotal = qty * price;

            linePriceEl.textContent = formatRupiah(price);
            lineSubtotalEl.textContent = formatRupiah(lineTotal);
            subtotal += lineTotal;
        });

        const discount = parseFloat(discountInput.value) || 0;
        const shipping = parseFloat(shippingInput.value) || 0;
        const total = Math.max(0, subtotal - discount + shipping);

        lblSubtotal.textContent = formatRupiah(subtotal);
        lblTotal.textContent = formatRupiah(total);

        // Check credit limit warning
        if (paymentSelect.value === 'credit' && customerSelect.value) {
            if (total > currentAvailableCredit) {
                creditAlert.classList.remove('d-none');
                creditAlertText.textContent = `Peringatan: Nilai pesanan (${formatRupiah(total)}) melebihi sisa plafon kredit pelanggan (${formatRupiah(currentAvailableCredit)}). Pesanan tempo ini memerlukan otorisasi persetujuan Admin/Owner.`;
            } else {
                creditAlert.classList.add('d-none');
            }
        } else {
            creditAlert.classList.add('d-none');
        }
    }

    customerSelect.addEventListener('change', updateCustomerDetails);
    paymentSelect.addEventListener('change', recalculate);
    btnAddRow.addEventListener('click', () => addRow());
    discountInput.addEventListener('input', recalculate);
    shippingInput.addEventListener('input', recalculate);

    // Initial setup
    addRow();
    if (customerSelect.value) {
        updateCustomerDetails();
    }
});
</script>
@endpush
@endsection
