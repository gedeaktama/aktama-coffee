# Kopi Distro — Sistem Distribusi & Penjualan Kopi Kemasan

Aplikasi web berbasis **Laravel** yang mengintegrasikan penjualan retail online (B2C) dan sistem penjualan grosir (B2B) untuk kafe, toko kelontong, dan reseller dengan satu database dan sistem persediaan terpusat.

---

## 1. Persyaratan Sistem
- **PHP**: Versi 8.2 atau lebih tinggi (diuji pada PHP 8.2.12 Windows XAMPP).
- **Web Server**: Apache pada XAMPP lokal atau Shared Hosting (cPanel).
- **Database**: MySQL / MariaDB (Port 3306).
- **Composer**: Versi 2.x.
- **Node.js**: Versi 18+ (opsional untuk bundling asset).

---

## 2. Panduan Instalasi Lokal (Windows + XAMPP)

### Langkah 1: Kloning / Tempatkan Proyek
Letakkan folder proyek di dalam direktori htdocs XAMPP:
```bash
d:\Xampp2\htdocs\kopi
```

### Langkah 2: Konfigurasi File Lingkungan (`.env`)
Salin file `.env.example` menjadi `.env` jika belum ada:
```bash
cp .env.example .env
```
Pastikan pengaturan database di file `.env` sudah sesuai dengan XAMPP Anda:
```dotenv
APP_NAME="Distribusi Kopi"
APP_ENV=local
APP_DEBUG=true
APP_URL=http://localhost/kopi/public

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=kopi
DB_USERNAME=root
DB_PASSWORD=
```

### Langkah 3: Instalasi Dependensi
Jalankan composer install:
```bash
composer install
```
> **Catatan Windows**: Jika Anda memiliki Git for Windows yang menyertakan `usr/bin/unzip` yang bermasalah, gunakan file `run_composer.bat` yang sudah disediakan untuk instalasi tanpa gangguan.

### Langkah 4: Generate Application Key
```bash
php artisan key:generate
```

### Langkah 5: Migrasi Database & Seeding Akun Awal
Buat database bernama `kopi` di phpMyAdmin / MySQL XAMPP, kemudian jalankan:
```bash
php artisan migrate --seed
```

---

## 3. Akun Pengguna Bawaan (Hasil Seeder)

Semua akun default menggunakan password: **`password123`**

| Role | Email | Wewenang Utama |
|---|---|---|
| **Owner** | `owner@kopi.com` | Akses penuh seluruh bisnis, persetujuan batas kredit, laporan laba rugi/HPP. |
| **Admin** | `admin@kopi.com` | Pengelolaan master data produk, varian, supplier, pelanggan, dan pesanan. |
| **Sales** | `sales@kopi.com` | Mengelola pelanggan bisnis dan membuat pesanan grosir (B2B). |
| **Warehouse** | `warehouse@kopi.com` | Konfirmasi penerimaan barang supplier, stok fisik, surat jalan pengiriman. |
| **Finance** | `finance@kopi.com` | Verifikasi transfer, pencatatan faktur, buku piutang tempo, dan pelunasan. |
| **Driver** | `driver@kopi.com` | Portal Mobile Sopir: scan QR toko, validasi GPS geofence, audit sisa rak, bukti serah terima (POD). |
| **Customer** | `pelanggan@gmail.com` | Akun retail publik untuk belanja kopi kemasan pribadi. |

---

## 4. Cara Menjalankan Aplikasi & Pengujian

### A. Akses Melalui Browser
Buka browser dan arahkan ke alamat:
- **Halaman Depan Toko (Publik)**: [http://localhost:8080/kopi/public](http://localhost:8080/kopi/public)
- **Halaman Masuk (Login)**: [http://localhost:8080/kopi/public/login](http://localhost:8080/kopi/public/login)
- **Halaman Registrasi Retail**: [http://localhost:8080/kopi/public/register](http://localhost:8080/kopi/public/register)
- **Dashboard Internal Staf / Owner**: [http://localhost:8080/kopi/public/dashboard](http://localhost:8080/kopi/public/dashboard)
- **Portal Mobile Sopir & Scanner Toko**: [http://localhost:8080/kopi/public/driver](http://localhost:8080/kopi/public/driver)
- **Peta Sebaran Toko Interaktif (Alfamart, Indomaret, dll)**: [http://localhost:8080/kopi/public/maps/stores](http://localhost:8080/kopi/public/maps/stores)
- **Peta Jejak & Rute Perjalanan Sopir**: [http://localhost:8080/kopi/public/maps/driver-tracking](http://localhost:8080/kopi/public/maps/driver-tracking)

> **Fitur Praktis**: Pada halaman login, telah disediakan tombol *Quick Demo Fill* untuk memilih akun role mana saja (termasuk Sopir/Driver) hanya dengan 1 kali klik.

### B. Menjalankan Pengujian Otomatis (Testing)
Jalankan test runner PHPUnit/Artisan Test:
```bash
php artisan test
```
Seluruh 58 skenario pengujian autentikasi, master data, portal driver, peta geolokasi, dan kalkulasi bisnis akan dijalankan secara otomatis.

---

## 5. Panduan Deployment ke Shared Hosting (cPanel / Niagahoster / Hostinger)

Proyek ini telah mengadopsi standar arsitektur dan skrip deployment yang sama dengan ekosistem **Aktama Digital**:

- 🚀 **Panduan Singkat Update & Deploy**: [`LANGKAH-SINGKAT.txt`](LANGKAH-SINGKAT.txt)
- 📖 **Dokumentasi Lengkap Hosting cPanel**: [`docs/DEPLOYMENT-HOSTING.md`](docs/DEPLOYMENT-HOSTING.md)
- ⚙️ **Skrip Otomatis Linux/cPanel/SSH**: [`scripts/linux/deploy-hosting.sh`](scripts/linux/deploy-hosting.sh)
- 📄 **Template Environment Produksi**: [`.env.production.example`](.env.production.example)
- 🗄️ **Database Dump Bersih Siap Import**: [`database/kopi_clean_production.sql`](database/kopi_clean_production.sql)
- 🔗 **Penyambung Symlink Web**: [`public/storage-link-runner.php`](public/storage-link-runner.php)

---

## 6. Struktur Dokumentasi Proyek
Status tahapan pembangunan dan panduan operasional tercatat secara transparan di:
- [`docs/DAFTAR_AKUN_DAN_PASSWORD.md`](docs/DAFTAR_AKUN_DAN_PASSWORD.md) — Kredensial akun pengelola bisnis, sopir, dan daftar URL sistem
- [`docs/PANDUAN_SISTEM_DISTRIBUSI_DAN_TRACKING.md`](docs/PANDUAN_SISTEM_DISTRIBUSI_DAN_TRACKING.md) — Buku panduan lengkap operasional Owner, Driver Mobile Scanner, Geofencing & Admin Toko
- [`docs/PROJECT_STATUS.md`](docs/PROJECT_STATUS.md) — Laporan teknis milestone, modul, dan arsitektur sistem
- [`docs/DEPLOYMENT-HOSTING.md`](docs/DEPLOYMENT-HOSTING.md) — Panduan lengkap shared hosting & cPanel


