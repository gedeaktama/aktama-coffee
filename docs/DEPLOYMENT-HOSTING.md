# PANDUAN DEPLOYMENT SHARED HOSTING (cPanel / Niagahoster / Hostinger)
## Sistem Manajemen & Distribusi Penjualan Kopi (Aktama Coffee / Kopi Distro)

Dokumen ini merupakan panduan teknis resmi langkah-demi-langkah untuk mempublikasikan website **Aktama Coffee** ke layanan **Shared Hosting** berbasis cPanel, DirectAdmin, atau hPanel (Niagahoster, Hostinger, DomaiNesia, Rumahweb, dsb.) dengan standar arsitektur keamanan yang sama seperti proyek utama **Aktama Digital**.

---

## 1. KEUNGGULAN ARSITEKTUR KEAMANAN DARI MALWARE & JUDOL

Aplikasi **Aktama Coffee** dibangun dengan framework **Laravel 11 & PHP 8.2+**:
1. **Pemisahan Inti Sistem**: File sensitif (`.env`, `routes`, `app`, `database`, `vendor`, `storage/logs`) terlindungi di luar direktori akses publik web (`public_html`).
2. **Ketiadaan Plugin Pihak Ketiga Rentan**: Menggunakan sistem kustom terstruktur native tanpa plugin CMS instan yang sering menjadi pintu masuk backdoor judol.
3. **Proteksi Bawaan**: CSRF Token otomatis pada seluruh formulir pembelian & keranjang belanja, Rate Limiter login, SQL Parameterization (Eloquent ORM), dan HTTP Security Headers (`X-Frame-Options`, `X-Content-Type-Options`).

---

## 2. PILIHAN STRUKTUR DIREKTORI DI CPANEL

### METODE A: Pemisahan Folder (SANGAT DIREKOMENDASIKAN - STANDAR INDUSTRI)
Metode ini adalah standar keamanan tertinggi di mana source code inti diletakkan sejajar dengan `public_html`, bukan di dalamnya:

```text
/home/username_cpanel/
├── kopi-source/              <-- SEMUA file Laravel diletakkan di sini (KECUALI isi folder public)
│   ├── app/
│   ├── bootstrap/
│   ├── config/
│   ├── database/
│   ├── resources/
│   ├── routes/
│   ├── scripts/
│   ├── storage/
│   ├── vendor/
│   ├── .env
│   └── artisan
└── public_html/              <-- HANYA berisi file dari dalam folder 'public' Laravel
    ├── images/               <-- Foto resmi produk & banner roastery
    ├── storage/              <-- Symlink ke kopi-source/storage/app/public
    ├── .htaccess             <-- File htaccess dari folder public
    ├── favicon.ico
    └── index.php             <-- File index.php (disesuaikan path-nya)
```

#### Modifikasi File `public_html/index.php`:
Buka file `public_html/index.php` di File Manager cPanel, sesuaikan baris autoloader dan bootstrap menjadi:
```php
<?php
use Illuminate\Http\Request;

define('LARAVEL_START', microtime(true));

// 1. Arahkan maintenance driver ke direktori kopi-source
if (file_exists($maintenance = __DIR__.'/../kopi-source/storage/framework/maintenance.php')) {
    require $maintenance;
}

// 2. Arahkan vendor autoloader ke kopi-source
require __DIR__.'/../kopi-source/vendor/autoload.php';

// 3. Jalankan aplikasi Laravel dari kopi-source
$app = require_once __DIR__.'/../kopi-source/bootstrap/app.php';

$app->handleRequest(Request::capture());
```

---

### METODE B: Domain Mengarah Langsung ke Subfolder `public` (Bila cPanel Mendukung)
Jika Anda menggunakan **Subdomain** (misal: `kopi.aktama.com`) atau **Addon Domain**:
1. Masuk menu **Domains** di cPanel.
2. Ubah kolom **Document Root** untuk domain/subdomain Anda menjadi:
   `kopi-source/public`
3. Dengan demikian, Anda dapat mengunggah seluruh folder proyek apa adanya tanpa perlu mengubah baris kode `index.php`.

---

### METODE C: Seluruh Proyek di Dalam `public_html` (Menggunakan Root `.htaccess`)
Jika paket hosting Anda tidak mengizinkan akses di luar `public_html` dan tidak dapat mengubah Document Root:
1. Unggah seluruh proyek ke `public_html`.
2. Proyek telah kami lengkapi dengan file root `.htaccess` yang otomatis memblokir akses HTTP ke `.env`, `.git`, `composer.*`, `storage/logs/`, `artisan`, dan meneruskan traffic publik ke subdirektori `/public/`.

---

## 3. LANGKAH-LANGKAH DEPLOYMENT DARI AWAL

### Langkah 1: Siapkan Database MySQL di cPanel
1. Login ke cPanel hosting Anda (misal: `https://domain-anda.com:2083`).
2. Masuk ke menu **MySQL Database Wizard**.
3. **Step 1**: Buat database baru, contoh: `u107454400_kopi`.
4. **Step 2**: Buat user database baru, contoh: `u107454400_kopiuser` dengan kata sandi acak yang kuat.
5. **Step 3**: Berikan centang pada **ALL PRIVILEGES** untuk menghubungkan user ke database.
6. Simpan nama database, username, dan password untuk konfigurasi `.env`.

### Langkah 2: Import Database Bersih (Ready-to-Use)
Proyek telah dilengkapi file dump database resmi yang bersih dari data dummy:
1. Buka menu **phpMyAdmin** di cPanel.
2. Klik nama database yang baru Anda buat (`u107454400_kopi`).
3. Pilih tab **Import** pada menu atas.
4. Klik **Choose File / Pilih File**, lalu pilih file:
   `database/kopi_clean_production.sql`
5. Klik tombol **Go / Kirim** di bagian bawah.
6. Seluruh tabel, master data kopi resmi, dan akun pengelola akan terimpor dalam hitungan detik.

### Langkah 3: Upload File Aplikasi ke Hosting

#### Opsi 1: Menggunakan Git Clone & SSH (Paling Cepat & Rapi - Sama seperti Aktama)
Jika hosting Anda memiliki akses SSH / Terminal:
1. Login SSH ke server:
   ```bash
   ssh -p 65002 u107454400@147.93.80.47
   ```
2. Clone repository ke folder tujuan:
   ```bash
   git clone https://github.com/gedeaktama/aktama-coffee.git kopi-source
   ```
3. Masuk ke direktori:
   ```bash
   cd kopi-source
   ```

#### Opsi 2: Menggunakan File Manager cPanel (Tanpa SSH)
1. Di komputer lokal Anda, kompres folder `kopi` menjadi file `kopi-deploy.zip`.
   *(PENTING: Jangan menyertakan folder `node_modules` dan `.git` untuk menghemat kuota upload).*
2. Buka **File Manager** cPanel, upload `kopi-deploy.zip`.
3. Klik kanan file zip, pilih **Extract**.

### Langkah 4: Konfigurasi File `.env` Produksi
1. Di File Manager cPanel, pastikan opsi **Show Hidden Files (dotfiles)** aktif pada tombol Settings di pojok kanan atas.
2. Salin file `.env.production.example` menjadi `.env`.
3. Edit file `.env` dan sesuaikan parameter berikut:
   ```ini
   APP_NAME="Aktama Coffee"
   APP_ENV=production
   APP_KEY=base64:6Y0TXI746m4t6ZkYF2l0KIMrUvv/WgPARk+uK+RPciA=
   APP_DEBUG=false
   APP_URL=https://kopi.aktama.com

   DB_CONNECTION=mysql
   DB_HOST=127.0.0.1
   DB_PORT=3306
   DB_DATABASE=u107454400_kopi
   DB_USERNAME=u107454400_kopiuser
   DB_PASSWORD=PasswordDatabaseAnda123!

   SESSION_DRIVER=file
   CACHE_DRIVER=file
   SESSION_SECURE_COOKIE=true
   ```
   > **PENTING**: Pastikan `APP_DEBUG=false` pada server hosting agar tidak menampilkan rincian error sensitif jika terjadi kendala.

### Langkah 5: Pembuatan Symlink Storage Publik
Agar foto produk dan dokumen dapat diakses melalui browser:

**Cara A (Via Terminal cPanel / SSH):**
```bash
php artisan storage:link
```

**Cara B (Via Browser Web - Tanpa SSH):**
1. Buka browser ke alamat: `https://domain-anda.com/storage-link-runner.php`
2. Tunggu hingga muncul pesan hijau "BERHASIL: Symlink berhasil dibuat".
3. **SEGERA HAPUS file `storage-link-runner.php`** dari File Manager demi keamanan.

### Langkah 6: Optimasi Performa Tinggi (Artisan Cache)
Jalankan perintah ini di Terminal cPanel untuk performa loading secepat kilat:
```bash
php artisan optimize:clear
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

### Langkah 7: Pasang Sertifikat SSL Gratis (HTTPS)
1. Di cPanel, buka menu **SSL/TLS Status**.
2. Centang domain/subdomain website Anda.
3. Klik tombol **Run AutoSSL**.
4. Di menu **Domains**, aktifkan tombol toggle **Force HTTPS Redirect**.

---

## 4. SKRIP OTOMATIS DEPLOYMENT VIA SSH

Jika hosting Anda memiliki akses terminal atau SSH, Anda cukup menjalankan skrip otomatis yang telah kami sediakan:

```bash
# 1. Masuk ke folder aplikasi di hosting
cd /home/u107454400/domains/aktama.com/public_html/kopi-source

# 2. Jalankan skrip deploy
bash scripts/linux/deploy-hosting.sh
```

Skrip ini secara otomatis akan:
- Menarik kode terbaru dari GitHub (`git pull origin main`).
- Menjalankan composer install produksi tanpa dev dependency.
- Menjalankan migrasi database otomatis jika ada tabel baru.
- Membangun cache performa (`config:cache`, `route:cache`, `view:cache`).
- Menyinkronkan file publik ke web root dan memperbarui symlink storage.
- Mengatur permission folder `storage/` dan `bootstrap/cache/`.

---

## 5. KREDENSIAL DEFAULT LOGIN OPERASIONAL PENGELOLA

Setelah database di-import, Anda dapat langsung masuk ke dashboard administrasi melalui:
- **URL Login**: `https://kopi.aktama.com/login`
- **Password Seluruh Akun**: `password123`

| Peran (Role) | Email Login | Password | Akses & Wewenang |
| :--- | :--- | :---: | :--- |
| **Owner (Pemilik)** | `owner@kopi.com` | `password123` | Akses penuh eksekutif, analisis omzet, monitoring piutang tempo mitra. |
| **Administrator** | `admin@kopi.com` | `password123` | Manajemen produk resmi, kategori, stok, toggle Mode Pengembang. |
| **Sales** | `sales@kopi.com` | `password123` | Input sales order grosir mitra kafe & toko dengan cek plafon kredit. |
| **Warehouse** | `warehouse@kopi.com` | `password123` | Penerimaan stok supplier, surat jalan pengiriman gudang. |
| **Finance** | `finance@kopi.com` | `password123` | Faktur penjualan (invoice), penerimaan pembayaran transfer/cash. |

> **Catatan Pendaftaran Mitra & Pelanggan**:
> Pelanggan retail umum, mitra toko/warung kelontong, dan mitra kafe mendaftar mandiri melalui menu **Daftar Akun** (`/register`). Sistem otomatis memberikan harga khusus toko atau harga kafe sesuai kategori akun.

---

## 6. TABEL TROUBLESHOOTING KENDALA UMUM

| Gejala Masalah | Penyebab Utama | Solusi |
| :--- | :--- | :--- |
| **500 Internal Server Error** | Folder `storage/` atau `bootstrap/cache/` tidak memiliki izin tulis | Ubah permission folder `storage/` dan `bootstrap/cache/` menjadi `775` (atau `755`) via File Manager cPanel. |
| **Gambar Produk 404 Tidak Muncul** | Symlink `storage` belum dibuat atau path belum mengarah ke target yang benar | Jalankan Langkah 5 (Opsi A via Terminal atau Opsi B via `storage-link-runner.php`). Pastikan file foto kemasan ada di `public/images/products/`. |
| **Halaman Error: "Vite Manifest Not Found"** | File asset Vite belum ter-build | Aplikasi menggunakan styling Bootstrap CDN dan Vanilla CSS independen, sehingga tidak bergantung pada Vite compile di server. |
| **Access Denied / Database Connection Refused** | Nama DB, user, atau password di file `.env` keliru | Periksa kembali nama DB di cPanel (pastikan menggunakan prefix akun hosting, contoh: `u107454400_kopi`). |
| **Halaman Terus Redirect ke HTTP (Bukan HTTPS)** | Pengaturan `APP_URL` di `.env` masih menggunakan `http://` | Buka file `.env`, ubah `APP_URL=https://kopi.aktama.com` dan pastikan SSL AutoSSL cPanel sudah aktif. |
| **Pembelian Dinonaktifkan / Muncul Pesan Mode Pengembang** | Fitur Mode Pengembang aktif di database | Masuk ke Admin Dashboard -> Menu Pengaturan Toko -> Matikan Mode Pengembang (OFF), atau setel `developer_mode` ke `0` di tabel `app_settings`. |
