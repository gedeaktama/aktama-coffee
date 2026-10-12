# DOKUMENTASI AKUN & PASSWORD PENGGUNA (USER CREDENTIALS)
## Sistem Penjualan & Distribusi Kopi Kemasan (Kopi Distro)

Dokumen ini memuat seluruh kredensial akun pengguna bawaan (seeder) yang digunakan untuk masuk ke dalam sistem aplikasi secara lokal di lingkungan XAMPP Windows.

---

## 🔑 KATA SANDI (PASSWORD) UTAMA
> **Password untuk SELURUH akun pengguna bawaan adalah:**
> # `password123`

---

## 📋 DAFTAR LENGKAP AKUN PENGGUNA BERDASARKAN HAK AKSES (ROLE)

| No | Role (Peran) | Nama Lengkap | Email / Username | Password | Nomor HP | Deskripsi Hak Akses & Modul Utama |
|:--:|:---|:---|:---|:---:|:---|:---|
| 1 | **Owner** | Pemilik Bisnis (Owner) | `owner@kopi.com` | `password123` | `081234567890` | **Akses Penuh Seluruh Sistem**: Dashboard Eksekutif, Laporan Laba Kotor & Analisis HPP, Valuasi Persediaan, Monitoring Piutang Tempo, Persetujuan Transaksi Grosir & Gudang. |
| 2 | **Admin** | Administrator Toko | `admin@kopi.com` | `password123` | `081234567891` | **Manajemen Operasional & Master Data**: Kelola Kategori, Produk & SKU Gramasi, Data Supplier/Pemasok, Master Pelanggan B2B, Monitoring Seluruh Transaksi & Laporan. |
| 3 | **Sales** | Sales Representative | `sales@kopi.com` | `password123` | `081234567892` | **Penjualan Grosir B2B**: Input Pesanan Grosir B2B (`/sales/orders/create`), Cek Status Plafon Kredit & Batas Piutang Toko/Kafe Mitra, Riwayat Pesanan Penjualan. |
| 4 | **Warehouse** | Staf Gudang (Warehouse) | `warehouse@kopi.com` | `password123` | `081234567893` | **Manajemen Stok & Logistik**: Input Purchase Order (PO) Supplier, Penerimaan Fisik Barang, Kartu Mutasi Stok Real-Time, Audit Stock Opname, Penerbitan & Cetak Surat Jalan Resmi (`SJ-YYYYMM-XXXX`). |
| 5 | **Finance** | Staf Keuangan (Finance) | `finance@kopi.com` | `password123` | `081234567894` | **Keuangan & Manajemen Piutang**: Penerbitan Faktur Penjualan (Invoicing `INV-YYYYMM-XXXX`), Pencatatan Pembayaran Cicilan/Lunas, Pemulihan Otomatis Plafon Kredit B2B, Kartu Piutang Pelanggan (Customer Ledger), Cetak Kuitansi Resmi. |

> **Catatan Akun Pelanggan (Customer / Mitra):**
> Pelanggan (Konsumen Retail, Mitra Toko/Warung, dan Mitra Kafe/Kedai) mendaftar secara mandiri melalui halaman pendaftaran online `/register`. Sistem akan otomatis menetapkan kategori harga diskon sesuai tier akun yang didaftarkan.

---

## 🌐 INFORMASI AKSES APLIKASI LOKAL (XAMPP)

- **URL Beranda Utama**: [http://localhost:8080/kopi/public/](http://localhost:8080/kopi/public/)
- **URL Halaman Login**: [http://localhost:8080/kopi/public/login](http://localhost:8080/kopi/public/login)
- **URL Etalase Katalog Kopi**: [http://localhost:8080/kopi/public/shop](http://localhost:8080/kopi/public/shop)
- **URL Dashboard Manajemen**: [http://localhost:8080/kopi/public/dashboard](http://localhost:8080/kopi/public/dashboard)

---

## 🛠️ CARA RESET / SEED ULANG AKUN
Jika akun diubah atau dihapus saat pengujian dan ingin dikembalikan ke kondisi default, jalankan perintah berikut di terminal:

```powershell
php artisan db:seed --class=UserSeeder
```
Perintah ini akan secara otomatis memperbarui atau membuat ulang ke-6 akun di atas dengan password `password123`.
