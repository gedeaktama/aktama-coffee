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
                        <label for="customer_id" class="form-label fw-semibold">Pilih Toko / Warung <span class="text-danger">*</span></label>
                        <select name="customer_id" id="customer_id" class="form-select @error('customer_id') is-invalid @enderror" required>
                            <option value="">-- Pilih Toko Mitra --</option>
                            @foreach($customers as $c)
                                <option value="{{ $c->id }}" {{ old('customer_id') == $c->id ? 'selected' : '' }}>
                                    {{ $c->business_name }} ({{ $c->pic_name }})
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="mb-3">
                        <label for="delivery_date" class="form-label fw-semibold">Tanggal Titip Barang <span class="text-danger">*</span></label>
                        <input type="date" name="delivery_date" id="delivery_date" class="form-control" value="{{ old('delivery_date', date('Y-m-d')) }}" required>
                    </div>

                    <div class="mb-3">
                        <label for="sales_user_id" class="form-label fw-semibold">Sales Penanggung Jawab (PIC)</label>
                        <select name="sales_user_id" id="sales_user_id" class="form-select">
                            <option value="">-- Pilih Sales Canvasser --</option>
                            @foreach($salesUsers as $s)
                                <option value="{{ $s->id }}" {{ old('sales_user_id', auth()->id()) == $s->id ? 'selected' : '' }}>
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
                    <div class="d-flex justify-content-between align-items-center mb-3 pb-2 border-bottom">
                        <h5 class="fw-bold text-dark mb-0">
                            <i class="bi bi-boxes me-2 text-warning"></i> Daftar Produk Kopi yang Dititipkan
                        </h5>
                        <button type="button" class="btn btn-sm btn-outline-warning text-dark fw-bold" onclick="addRow()">
                            <i class="bi bi-plus-lg me-1"></i> Tambah Varian Kopi
                        </button>
                    </div>

                    <div class="table-responsive mb-3">
                        <table class="table align-middle" id="itemsTable">
                            <thead class="table-light">
                                <tr>
                                    <th>Pilih Varian Kopi &amp; SKU</th>
                                    <th style="width: 130px;" class="text-center">Stok Gudang</th>
                                    <th style="width: 140px;" class="text-center">Jumlah Titip <span class="text-danger">*</span></th>
                                    <th style="width: 150px;" class="text-end">Harga Standar</th>
                                    <th style="width: 50px;" class="text-center">#</th>
                                </tr>
                            </thead>
                            <tbody id="itemsBody">
                                <!-- Baris Pertama Default -->
                                <tr class="item-row">
                                    <td>
                                        <select name="items[0][variant_id]" class="form-select variant-select" onchange="updateVariantDetails(this)" required>
                                            <option value="">-- Pilih Varian Produk --</option>
                                            @foreach($products as $prod)
                                                <optgroup label="{{ $prod->name }} ({{ $prod->brand }})">
                                                    @foreach($prod->variants as $v)
                                                        <option value="{{ $v->id }}" 
                                                            data-stock="{{ $v->current_stock }}" 
                                                            data-price="{{ $v->selling_price }}" 
                                                            data-sku="{{ $v->sku }}">
                                                            {{ $v->variant_name }} [{{ $v->sku }}] — Rp {{ number_format($v->selling_price, 0, ',', '.') }}
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
                                        <button type="button" class="btn btn-sm btn-outline-danger" onclick="removeRow(this)" disabled>
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
@endsection

@push('scripts')
<script>
    let rowIndex = 1;

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
            qtyInput.max = stock;
        } else {
            row.querySelector('.stock-badge').textContent = '0 pack';
            row.querySelector('.stock-badge').className = 'badge bg-light text-dark border stock-badge';
            row.querySelector('.item-price-input').value = 0;
            row.querySelector('.item-price-display').textContent = 'Rp 0';
        }

        calcTotal();
    }

    function addRow() {
        const body = document.getElementById('itemsBody');
        const firstRow = body.querySelector('.item-row');
        const newRow = firstRow.cloneNode(true);

        // Reset inputs
        newRow.querySelector('.variant-select').name = `items[${rowIndex}][variant_id]`;
        newRow.querySelector('.variant-select').selectedIndex = 0;
        
        const qtyInput = newRow.querySelector('.item-qty');
        qtyInput.name = `items[${rowIndex}][quantity]`;
        qtyInput.value = 10;
        qtyInput.removeAttribute('max');

        const priceInput = newRow.querySelector('.item-price-input');
        priceInput.name = `items[${rowIndex}][price]`;
        priceInput.value = 0;

        newRow.querySelector('.item-price-display').textContent = 'Rp 0';
        newRow.querySelector('.stock-badge').textContent = '0 pack';
        newRow.querySelector('.stock-badge').className = 'badge bg-light text-dark border stock-badge';

        const deleteBtn = newRow.querySelector('button');
        deleteBtn.removeAttribute('disabled');

        body.appendChild(newRow);
        rowIndex++;

        updateDeleteButtons();
        calcTotal();
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
</script>
@endpush
