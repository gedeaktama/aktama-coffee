<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Surat Titip Barang - {{ $consignment->consignment_number }}</title>
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
            height: 80px;
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
            <h5 class="fw-bold text-danger mb-0">SURAT TITIP BARANG</h5>
            <div class="small fw-bold">SURAT JALAN KONSINYASI</div>
            <div class="font-monospace fw-bold fs-6 mt-1">{{ $consignment->consignment_number }}</div>
        </div>
    </div>

    <!-- Informasi Penitipan -->
    <div class="row g-3 mb-4">
        <div class="col-6">
            <small class="text-muted text-uppercase fw-bold d-block">Toko Mitra Penerima Titipan:</small>
            <h6 class="fw-bold mb-1">{{ $consignment->customer->business_name }}</h6>
            <div class="small text-muted">{{ $consignment->customer->shipping_address ?: $consignment->customer->address }}</div>
            <div class="small text-muted">PIC: {{ $consignment->customer->pic_name }} &bull; {{ $consignment->customer->phone }}</div>
        </div>
        <div class="col-6 text-end">
            <small class="text-muted text-uppercase fw-bold d-block">Rincian Dokumen:</small>
            <div><strong>Tanggal Titip:</strong> {{ $consignment->delivery_date->format('d/m/Y') }}</div>
            <div><strong>Sales Canvasser:</strong> {{ $consignment->salesUser->name ?? '-' }}</div>
            <div><strong>Status Titip:</strong> <span class="badge bg-secondary">KONSINYASI (TITIP JUAL)</span></div>
        </div>
    </div>

    <div class="alert alert-light border small py-2 mb-3">
        <strong>Syarat &amp; Ketentuan Konsinyasi:</strong> Barang yang tercantum di bawah ini dititipkan di rak toko mitra. Pembayaran dilakukan secara berkala saat survei kunjungan mingguan hanya untuk produk yang laku terjual.
    </div>

    <!-- Tabel Barang Titip -->
    <table class="table table-bordered align-middle mb-4">
        <thead class="table-light">
            <tr>
                <th style="width: 40px;" class="text-center">No</th>
                <th>Kode SKU</th>
                <th>Nama Produk Kopi</th>
                <th>Ukuran Kemasan</th>
                <th class="text-center" style="width: 120px;">Jumlah Titip</th>
                <th class="text-end" style="width: 140px;">Harga Jual Satuan</th>
                <th class="text-end" style="width: 150px;">Total Nilai Titipan</th>
            </tr>
        </thead>
        <tbody>
            @foreach($consignment->items as $idx => $item)
                <tr>
                    <td class="text-center">{{ $idx + 1 }}</td>
                    <td class="font-monospace fw-bold">{{ $item->variant->sku }}</td>
                    <td>{{ $item->variant->product->name }}</td>
                    <td>{{ $item->variant->package_weight_grams }}g ({{ $item->variant->grind_form }})</td>
                    <td class="text-center fw-bold fs-6">{{ $item->initial_quantity }} pack</td>
                    <td class="text-end">{{ $item->formatted_price }}</td>
                    <td class="text-end fw-bold">{{ $item->formatted_current_value }}</td>
                </tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr class="table-light">
                <th colspan="4" class="text-end">TOTAL KESELURUHAN TITIPAN:</th>
                <th class="text-center fw-bold fs-6">{{ $consignment->total_active_stock }} pack</th>
                <th></th>
                <th class="text-end fw-bold fs-6">{{ $consignment->formatted_total_stock_value }}</th>
            </tr>
        </tfoot>
    </table>

    @if($consignment->notes)
        <div class="small mb-4">
            <strong>Catatan Tambahan:</strong> {{ $consignment->notes }}
        </div>
    @endif

    <!-- Tanda Tangan 3 Pihak -->
    <div class="row text-center mt-5">
        <div class="col-4">
            <small class="text-muted d-block mb-1">Diserahkan Oleh (Gudang):</small>
            <div class="signature-box border-bottom"></div>
            <strong class="d-block mt-1">{{ auth()->user()?->name ?? 'Petugas Logistik' }}</strong>
            <small class="text-muted">(Bagian Logistik)</small>
        </div>
        <div class="col-4">
            <small class="text-muted d-block mb-1">Sales Canvasser:</small>
            <div class="signature-box border-bottom"></div>
            <strong class="d-block mt-1">{{ $consignment->salesUser->name ?? '-' }}</strong>
            <small class="text-muted">(Petugas Kunjungan)</small>
        </div>
        <div class="col-4">
            <small class="text-muted d-block mb-1">Diterima Toko Mitra:</small>
            <div class="signature-box border-bottom"></div>
            <strong class="d-block mt-1">{{ $consignment->customer->pic_name }}</strong>
            <small class="text-muted">Cap &amp; Tanda Tangan Toko</small>
        </div>
    </div>
</div>

</body>
</html>
