<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kuitansi Setoran Konsinyasi - {{ $audit->audit_number }}</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 13px;
            color: #111;
            background: #fff;
        }
        .invoice-box {
            max-width: 800px;
            margin: auto;
            padding: 30px;
            border: 1px solid #eee;
        }
        @media print {
            .no-print { display: none !important; }
            .invoice-box { border: none; padding: 0; }
        }
        .signature-box {
            height: 75px;
        }
    </style>
</head>
<body>

<div class="no-print text-center py-3 bg-light border-bottom mb-4">
    <button onclick="window.print()" class="btn btn-warning fw-bold px-4">
        <i class="bi bi-printer"></i> Cetak Dokumen (Print)
    </button>
    <button onclick="window.close()" class="btn btn-outline-secondary px-3 ms-2">Tutup</button>
</div>

<div class="invoice-box">
    <!-- Header Kop Surat -->
    <div class="d-flex justify-content-between align-items-start border-bottom pb-3 mb-4">
        <div>
            <h4 class="fw-bold text-dark mb-0">PT KOPI BINTANG HARAPAN</h4>
            <div class="text-muted small">Roastery &amp; Distribusi Kopi Asli Sulawesi Tengah</div>
            <div class="text-muted small">Palu, Sulawesi Tengah, Indonesia &bull; Telp: 0812-4567-8901</div>
        </div>
        <div class="text-end">
            <h5 class="fw-bold text-success mb-0">BUKTI SETORAN KONSINYASI</h5>
            <div class="small fw-bold">NOTA SURVEI PRODUK LAKU</div>
            <div class="font-monospace fw-bold fs-6 mt-1">{{ $audit->audit_number }}</div>
        </div>
    </div>

    <!-- Informasi Toko & Bukti Setor -->
    <div class="row g-3 mb-4">
        <div class="col-6">
            <small class="text-muted text-uppercase fw-bold d-block">Toko Mitra Konsinyasi:</small>
            <h6 class="fw-bold mb-1">{{ $audit->customer->business_name }}</h6>
            <div class="small text-muted">{{ $audit->customer->shipping_address ?: $audit->customer->address }}</div>
            <div class="small text-muted">PIC: {{ $audit->customer->pic_name }} &bull; {{ $audit->customer->phone }}</div>
        </div>
        <div class="col-6 text-end">
            <small class="text-muted text-uppercase fw-bold d-block">Rincian Kunjungan:</small>
            <div><strong>Tanggal Survei:</strong> {{ $audit->audit_date->format('d/m/Y') }}</div>
            <div><strong>Sales Canvasser:</strong> {{ $audit->user->name ?? '-' }}</div>
            <div><strong>Metode Bayar:</strong> <span class="badge bg-light text-dark border text-uppercase">{{ $audit->payment_method }}</span></div>
            <div><strong>Status Pembayaran:</strong> <span class="badge bg-success">LUNAS DISETOR</span></div>
        </div>
    </div>

    <!-- Tabel Rincian Produk yang Laku -->
    <table class="table table-bordered align-middle mb-4">
        <thead class="table-light">
            <tr>
                <th style="width: 35px;" class="text-center">No</th>
                <th>Kode SKU</th>
                <th>Nama Produk Kopi</th>
                <th class="text-center">Stok Awal</th>
                <th class="text-center">Sisa Rak</th>
                <th class="text-center bg-light">Laku Terjual</th>
                <th class="text-end">Harga</th>
                <th class="text-end">Subtotal Disetor</th>
            </tr>
        </thead>
        <tbody>
            @foreach($audit->items as $idx => $item)
                <tr>
                    <td class="text-center">{{ $idx + 1 }}</td>
                    <td class="font-monospace fw-bold">{{ $item->variant->sku }}</td>
                    <td>{{ $item->variant->product->name }}</td>
                    <td class="text-center">{{ $item->stock_before }}</td>
                    <td class="text-center">{{ $item->physical_remaining }}</td>
                    <td class="text-center fw-bold text-success">{{ $item->quantity_sold }} pack</td>
                    <td class="text-end">{{ $item->formatted_price }}</td>
                    <td class="text-end fw-bold">{{ $item->formatted_subtotal }}</td>
                </tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr class="table-light">
                <th colspan="5" class="text-end">TOTAL PRODUK LAKU &amp; UANG DISETOR:</th>
                <th class="text-center fw-bold fs-6 text-success">{{ $audit->total_sold_qty }} pack</th>
                <th></th>
                <th class="text-end fw-bold fs-6">{{ $audit->formatted_total_sold_amount }}</th>
            </tr>
        </tfoot>
    </table>

    <div class="alert alert-light border small py-2 mb-4">
        <strong>Status Stok Selanjutnya di Rak Toko:</strong> Setelah survei ini (termasuk tambahan restock baru +{{ $audit->total_restock_qty }} pack), total stok yang tersimpan di rak toko mitra adalah <strong>{{ $audit->items->sum('stock_after') }} pack</strong>.
    </div>

    <!-- Tanda Tangan Pembayaran -->
    <div class="row text-center mt-5">
        <div class="col-6">
            <small class="text-muted d-block mb-1">Yang Menyerahkan (Toko Mitra):</small>
            <div class="signature-box border-bottom"></div>
            <strong class="d-block mt-1">{{ $audit->customer->pic_name }}</strong>
            <small class="text-muted">{{ $audit->customer->business_name }}</small>
        </div>
        <div class="col-6">
            <small class="text-muted d-block mb-1">Penerima Setoran (Sales Canvasser):</small>
            <div class="signature-box border-bottom"></div>
            <strong class="d-block mt-1">{{ $audit->user->name ?? '-' }}</strong>
            <small class="text-muted">PT Kopi Bintang Harapan</small>
        </div>
    </div>
</div>

</body>
</html>
