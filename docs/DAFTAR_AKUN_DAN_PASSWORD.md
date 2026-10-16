# DOKUMENTASI LENGKAP AKUN & PASSWORD PENGGUNA (USER CREDENTIALS)
## Sistem Distribusi & Penjualan Kopi Kemasan (Kopi Distro)

Dokumen ini memuat seluruh kredensial akun pengguna bawaan (*default seeder*), hak akses (*role*), dan tautan langsung untuk masuk ke sistem Kopi Distro di lingkungan lokal XAMPP maupun server hosting.

---

## 🔑 KATA SANDI (PASSWORD) UTAMA
> **Password untuk SELURUH akun pengguna bawaan sistem adalah:**
> # `password123`

*(Kecuali akun retail baru yang Anda daftarkan sendiri melalui form registrasi publik).*

---

## 📋 DAFTAR LENGKAP AKUN PENGGUNA BERDASARKAN HAK AKSES (ROLE)

| No | Role (Peran) | Nama Petugas / Akun | Email / Username | Password | Nomor HP | Deskripsi Hak Akses & Fitur Utama |
|:--:|:---|:---|:---|:---:|:---|:---|
| 1 | **Owner** | Pemilik Bisnis (Owner) | `owner@kopi.com` | `password123` | `081234567890` | **Akses Penuh Seluruh Sistem**: Executive Command Center, Omset Harian Realtime, Laba Kotor HPP (Margin %), Multi-level Inventory (Gudang Pusat vs Titip Toko), Toko Terlaris, Produk Terlaris, Alert Toko Menunggak, Peta Sebaran Toko & Peta Jejak Rute Armada Sopir. |
| 2 | **Admin** | Administrator Toko & Sistem | `admin@kopi.com` | `password123` | `081234567891` | **Manajemen Operasional & Master Data**: Kelola Kategori, Produk & SKU Gramasi, Master Toko Retail (Alfamart, Indomaret, Kafe), Pengaturan Koordinat GPS & Geofence, Download & Cetak Stiker QR Code Toko, Supplier, dan Monitoring Transaksi. |
| 3 | **Driver 1** | Budi Santoso (Armada 1 - Motor Box) | `driver@kopi.com` | `password123` | `081299887701` | **Portal Mobile Sopir / Kurir**: Scan QR Code Toko via Kamera HP, Validasi GPS Geofencing, Audit Sisa Stok Rak, Pencatatan Produk Laku & Restock Otomatis, Upload Bukti Foto Kunjungan (POD), Riwayat Kunjungan Toko, Tombol Rute Google Maps. |
| 4 | **Driver 2** | Joko Supriyadi (Armada 2 - Blind Van) | `driver2@kopi.com` | `password123` | `081299887702` | **Portal Mobile Sopir Cadangan / Wilayah 2**: Tugas sama dengan Driver 1 untuk melayani distribusi area luar kota / pesanan volume besar. |
| 5 | **Sales** | Sales Representative | `sales@kopi.com` | `password123` | `081234567892` | **Penjualan Grosir B2B**: Pendaftaran Pelanggan Bisnis, Input Pesanan Grosir B2B (`/sales/orders/create`), Cek Status Plafon Kredit & Batas Piutang Toko/Kafe Mitra, Riwayat Pesanan Penjualan. |
| 6 | **Warehouse** | Staf Gudang (Warehouse) | `warehouse@kopi.com` | `password123` | `081234567893` | **Manajemen Stok & Logistik Gudang Pusat**: Input Purchase Order (PO) Supplier, Penerimaan Fisik Barang, Kartu Mutasi Stok Real-Time, Audit Stock Opname, Penerbitan & Cetak Surat Jalan Resmi (`SJ-YYYYMM-XXXX`). |
| 7 | **Finance** | Staf Keuangan (Finance) | `finance@kopi.com` | `password123` | `081234567894` | **Keuangan & Manajemen Piutang**: Penerbitan Faktur Penjualan (Invoicing `INV-YYYYMM-XXXX`), Pencatatan Pembayaran Cicilan/Lunas, Pemulihan Otomatis Plafon Kredit B2B, Kartu Piutang Pelanggan (Customer Ledger), Cetak Kuitansi Resmi. |
| 8 | **Customer** | Pelanggan Retail Demo | `pelanggan@gmail.com` | `password123` | `081987654321` | **Belanja Online (B2C)**: Akun belanja personal di etalase web kopi kemasan, keranjang belanja, checkout, dan riwayat pesanan online. |

---

## ⚡ FITUR QUICK DEMO FILL (LOGIN 1-KLIK)

Untuk kemudahan pengujian, pada halaman Login (`/login`) telah disediakan tombol **"Quick Demo Login"**:
1. Buka [http://localhost:8080/kopi/public/login](http://localhost:8080/kopi/public/login)
2. Klik salah satu tombol role: **[👑 Owner]**, **[🚚 Driver (Sopir)]**, **[📦 Gudang]**, **[💼 Sales]**, atau **[💰 Finance]**.
3. Email dan kata sandi `password123` akan terisi otomatis, Anda tinggal menekan tombol **Masuk ke Sistem**.

> **Pengalihan Otomatis untuk Sopir:**
> Jika Anda login menggunakan akun `driver@kopi.com` atau `driver2@kopi.com`, sistem akan secara otomatis mengarahkan Anda langsung ke **Portal Khusus Sopir** (`/driver`), bukan ke dashboard kantor.

---

## 🌐 DAFTAR URL PENTING SISTEM (LINGKUNGAN XAMPP LOKAL)

Berikut adalah alamat langsung untuk mengakses berbagai modul:

| Halaman / Fitur | Alamat URL | Hak Akses |
|---|---|---|
| **Beranda Toko Publik** | [http://localhost:8080/kopi/public/](http://localhost:8080/kopi/public/) | Publik |
| **Katalog Belanja Kopi** | [http://localhost:8080/kopi/public/shop](http://localhost:8080/kopi/public/shop) | Publik / Retail |
| **Halaman Login** | [http://localhost:8080/kopi/public/login](http://localhost:8080/kopi/public/login) | Semua Role |
| **Dashboard Eksekutif Owner** | [http://localhost:8080/kopi/public/dashboard](http://localhost:8080/kopi/public/dashboard) | Owner, Admin, Staf |
| **Portal Mobile Sopir / Driver** | [http://localhost:8080/kopi/public/driver](http://localhost:8080/kopi/public/driver) | Driver, Owner, Admin |
| **Scanner Kamera QR Toko** | [http://localhost:8080/kopi/public/driver/scan](http://localhost:8080/kopi/public/driver/scan) | Driver, Owner, Admin |
| **Riwayat Kunjungan Driver** | [http://localhost:8080/kopi/public/driver/visits](http://localhost:8080/kopi/public/driver/visits) | Driver, Owner, Admin |
| **Peta Interaktif Sebaran Toko Retail** | [http://localhost:8080/kopi/public/maps/stores](http://localhost:8080/kopi/public/maps/stores) | Owner, Admin, Sales, Driver |
| **Peta Pelacakan Rute & Jejak Driver** | [http://localhost:8080/kopi/public/maps/driver-tracking](http://localhost:8080/kopi/public/maps/driver-tracking) | Owner, Admin |
| **Master Pelanggan & Toko Retail** | [http://localhost:8080/kopi/public/customers](http://localhost:8080/kopi/public/customers) | Admin, Owner, Sales |
| **Cetak Stiker QR Toko (Contoh)** | [http://localhost:8080/kopi/public/driver/print-qr/1](http://localhost:8080/kopi/public/driver/print-qr/1) | Admin, Owner |

---

## 🛠️ CARA RESET ULANG DATABASE & KATA SANDI

Jika ada data akun yang tidak sengaja terhapus atau kata sandi diubah saat pengujian, jalankan perintah Artisan berikut di Terminal / PowerShell pada folder `d:\Xampp2\htdocs\kopi`:

### Opsi A: Reset Hanya Akun Pengguna
```powershell
php artisan db:seed --class=UserSeeder
```
*Perintah ini akan membuat kembali seluruh akun di atas dengan password `password123` tanpa menghapus data transaksi lainnya.*

### Opsi B: Reset Penuh Seluruh Sistem & Sebaran Toko
```powershell
php artisan migrate:fresh --seed
```
*Perintah ini akan menghapus dan membangun ulang seluruh tabel, mengisi data produk kopi, akun pengguna, 6 cabang toko retail (Alfamart, Indomaret, Kafe, Toko), simulasi stok konsinyasi, dan riwayat rute sopir.*
