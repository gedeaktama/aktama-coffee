# PANDUAN LENGKAP SISTEM DISTRIBUSI & TRACKING ARMADA KOPI
## Modul Manajemen Ritel Modern (Alfamart, Indomaret, Kafe & Toko), Mobile Scanner Driver, Geofencing GPS & Analisis Eksekutif Real-Time

Dokumen ini merupakan buku panduan operasional resmi bagi seluruh pemangku kepentingan (*stakeholders*): **Owner**, **Sopir/Driver Armada**, **Admin Kantor**, **Staf Gudang**, dan **Staf Keuangan**.

---

## 📑 DAFTAR ISI
1. [Ringkasan Alur Kerja Distribusi](#1-ringkasan-alur-kerja-distribusi)
2. [Panduan Operasional untuk Owner (Pemilik Bisnis)](#2-panduan-operasional-untuk-owner-pemilik-bisnis)
   - [2.1 Monitoring Omset Harian Realtime](#21-monitoring-omset-harian-realtime)
   - [2.2 Analisis Laba Kotor & Margin HPP](#22-analisis-laba-kotor--margin-hpp)
   - [2.3 Multi-Level Inventory (Gudang Utama vs Titip Toko)](#23-multi-level-inventory-gudang-utama-vs-titip-toko)
   - [2.4 Analisis Toko Terlaris & Produk Terfavorit](#24-analisis-toko-terlaris--produk-terfavorit)
   - [2.5 Monitoring Toko Menunggak & Plafon Piutang](#25-monitoring-toko-menunggak--plafon-piutang)
   - [2.6 Peta Sebaran Toko & Peta Jejak Rute Armada](#26-peta-sebaran-toko--peta-jejak-rute-armada)
3. [Panduan Operasional untuk Sopir / Driver Lapangan (Mobile Web)](#3-panduan-operasional-untuk-sopir--driver-lapangan-mobile-web)
   - [3.1 Login & Tampilan Dashboard Mobile Sopir](#31-login--tampilan-dashboard-mobile-sopir)
   - [3.2 Navigasi Arah via Google Maps](#32-navigasi-arah-via-google-maps)
   - [3.3 Membuka Scanner Kamera QR Code Toko](#33-membuka-scanner-kamera-qr-code-toko)
   - [3.4 Validasi GPS Geofencing (Anti-Curang)](#34-validasi-gps-geofencing-anti-curang)
   - [3.5 Audit Sisa Stok Rak, Kalkulasi Barang Laku & Restock](#35-audit-sisa-stok-rak-kalkulasi-barang-laku--restock)
   - [3.6 Pengambilan & Upload Foto Bukti Kunjungan (POD)](#36-pengambilan--upload-foto-bukti-kunjungan-pod)
   - [3.7 Menyelesaikan Kunjungan & Berpindah Toko](#37-menyelesaikan-kunjungan--berpindah-toko)
4. [Panduan Operasional untuk Admin & Staf Kantor](#4-panduan-operasional-untuk-admin--staf-kantor)
   - [4.1 Menambahkan Toko / Cabang Baru (Alfamart, Indomaret, dll)](#41-menambahkan-toko--cabang-baru-alfamart-indomaret-dll)
   - [4.2 Mengisi Koordinat GPS (Latitude & Longitude) dari Google Maps](#42-mengisi-koordinat-gps-latitude--longitude-dari-google-maps)
   - [4.3 Mengatur Batas Minimum Peringatan Stok Rak Toko](#43-mengatur-batas-minimum-peringatan-stok-rak-toko)
   - [4.4 Mengunduh & Mencetak Stiker QR Code Toko](#44-mengunduh--mencetak-stiker-qr-code-toko)
5. [Daftar Kredensial Akun & Password](#5-daftar-kredensial-akun--password)
6. [Tanya Jawab & Troubleshooting (FAQ)](#6-tanya-jawab--troubleshooting-faq)

---

## 1. RINGKASAN ALUR KERJA DISTRIBUSI

Sistem ini dirancang khusus untuk memecahkan kendala klasik distribusi konsinyasi (*titip jual*) dan pengiriman kopi ke puluhan hingga ratusan jaringan ritel modern:

```
[ GUDANG PUSAT ] 
       │ 
       ▼ (Muat barang ke armada motor box / van)
[ SOPIR LAPANGAN ] ──(Buka Google Maps ke Toko Tujuan)──► [ TOKO RITEL ]
       │                                                         │
       ├─► 1. Tiba di lokasi toko                                 │
       ├─► 2. Buka scanner web camera di HP                      │
       ├─► 3. Scan Stiker QR Code yang terpasang di rak kopi      │
       ├─► 4. Sistem validasi koordinat GPS (Geofencing <= 250m) │
       ├─► 5. Hitung sisa stok kopi di rak (Sistem hitung laku)  │
       ├─► 6. Input jumlah restock kopi baru yang diturunkan     │
       └─► 7. Foto rak / nota serah terima (POD)                 │
                                                                 ▼
[ DASHBOARD OWNER ] ◄──(Laporan Real-Time Otomatis)───────────────┘
  • Omset harian langsung bertambah
  • Laba kotor (keuntungan) terhitung berdasarkan HPP
  • Stok gudang & stok toko ter-update
  • Jejak rute sopir muncul di peta pemantauan
```

---

## 2. PANDUAN OPERASIONAL UNTUK OWNER (PEMILIK BISNIS)

Sebagai pemilik bisnis, Anda memiliki kendali penuh dan visibilitas 360 derajat terhadap seluruh pergerakan barang, uang, dan karyawan.

### 2.1 Monitoring Omset Harian Realtime
- **Cara Akses**: Masuk ke menu **Dashboard** (`/dashboard`).
- **Data yang Ditampilkan**:
  - **Omset Hari Ini (IDR)**: Menghitung total nilai rupiah kopi yang laku terjual di semua toko yang dikunjungi hari ini.
  - **Total Kunjungan Hari Ini**: Jumlah toko Alfamart, Indomaret, dan mitra yang telah berhasil dikunjungi dan diverifikasi oleh sopir.
  - **Kunjungan Berhasil**: Persentase kunjungan yang tervalidasi di dalam zona GPS (geofence).

### 2.2 Analisis Laba Kotor & Margin HPP
- **Cara Akses**: Kartu metrik kedua di baris teratas Dashboard.
- **Kalkulasi**:
  $$\text{Laba Kotor} = \text{Total Omset} - \text{Total HPP (Harga Pokok Penjualan)}$$
  $$\text{Margin Keuntungan} = \left( \frac{\text{Laba Kotor}}{\text{Omset}} \right) \times 100\%$$
- **Manfaat**: Anda tidak hanya mengetahui omset kotor, tetapi langsung tahu berapa **uang bersih yang dihasilkan hari ini** setelah dipotong modal biji kopi mentah dan biaya kemasan.

### 2.3 Multi-Level Inventory (Gudang Utama vs Titip Toko)
- **Cara Akses**: Bagian *"Multi-Level Inventory"* di Dashboard.
- **Data yang Dipisahkan**:
  1. **Stok Gudang Pusat**: Kopi kemasan yang masih berada di rak penyimpanan kantor/gudang dan siap dikirim.
  2. **Stok Beredar (Floating di Toko)**: Total kopi yang saat ini sedang dipajang di seluruh rak Alfamart, Indomaret, dan kafe mitra.
  3. **Total Aset Persediaan**: Akumulasi seluruh kopi milik perusahaan beserta nilai nominal rupiah persediaan berdasarkan harga pokok produksi.

### 2.4 Analisis Toko Terlaris & Produk Terfavorit
- **Toko Paling Banyak Laku**: Tabel peringkat toko dengan penjualan tertinggi, menampilkan nama toko, jaringan (Alfamart/Indomaret/dll), jumlah pcs terjual, dan total omset yang disumbangkan.
- **Produk Kopi Paling Laris**: Tabel peringkat SKU (misal: *Kopi Arabika Aceh Gayo 250g* vs *Robusta Lampung 250g*) yang paling cepat habis di pasaran, lengkap dengan sisa stok pusat.

### 2.5 Monitoring Toko Menunggak & Plafon Piutang
- **Peringatan Toko Menunggak**: Tabel khusus di dashboard yang mendeteksi toko mitra yang memiliki faktur melewati jatuh tempo (*Overdue Payment Terms*).
- **Status Plafon Kredit**: Menampilkan sisa plafon belanja aman untuk masing-masing toko ritel. Jika toko menunggak, sistem memberikan tanda merah agar armada tidak menurunkan barang tambahan sebelum ada pembayaran.

### 2.6 Peta Sebaran Toko & Peta Jejak Rute Armada
Sistem menyediakan 2 jenis peta satelit berbasis OpenStreetMap/Leaflet:
1. **Peta Sebaran Toko** (`/maps/stores`):
   - Menampilkan seluruh titik toko ritel di peta kota.
   - **Warna Pin Status Toko**:
     - 🟢 **Hijau**: Stok aman di atas batas minimum.
     - 🟠 **Oranye**: Stok menipis (*Low Stock Alert*), perlu segera dijadwalkan restock.
     - 🔴 **Merah**: Toko menunggak pembayaran faktur.
     - 🔵 **Biru**: Toko sudah dikunjungi hari ini.
   - Klik pada pin untuk melihat sisa stok, omset toko, dan tombol langsung **"Petunjuk Arah Google Maps"**.
2. **Peta Jejak & Rute Sopir** (`/maps/driver-tracking`):
   - Menampilkan garis perjalanan (*route polyline*) yang dilalui armada sopir.
   - Setiap toko diberi nomor urutan kunjungan (Stop #1, Stop #2, Stop #3).
   - Menampilkan jam tiba sopir, jarak verifikasi GPS, status geofence, dan thumbnail foto serah terima di toko.

---

## 3. PANDUAN OPERASIONAL UNTUK SOPIR / DRIVER LAPANGAN (MOBILE WEB)

Sopir tidak perlu menginstal aplikasi berat dari Play Store. Cukup buka browser HP (Google Chrome / Safari) dan login.

### 3.1 Login & Tampilan Dashboard Mobile Sopir
1. Buka browser di HP dan ketik:
   `http://[IP-KOMPUTER-ANDA]:8080/kopi/public/login` (atau klik tombol **[🚚 Driver]** pada demo login).
2. Setelah login dengan akun `driver@kopi.com` dan password `password123`, Anda langsung diarahkan ke **Portal Mobile Sopir** (`/driver`).
3. Dashboard menampilkan:
   - Jumlah toko yang harus dikunjungi hari ini.
   - Daftar kartu toko per rute jalan.
   - Tombol cepat **"Buka Kamera Scanner QR"**.

### 3.2 Navigasi Arah via Google Maps
1. Pada kartu toko tujuan berikutnya, klik tombol **"Arahkan (Google Maps)"**.
2. Aplikasi Google Maps di HP akan terbuka secara otomatis dengan rute *turn-by-turn* terbaik menuju koordinat toko tersebut.

### 3.3 Membuka Scanner Kamera QR Code Toko
1. Setelah memarkir armada di depan toko ritel tujuan, klik tombol biru **"Mulai Scan Kunjungan"** di dashboard sopir.
2. Browser akan meminta izin kamera: klik **"Izinkan / Allow"**.
3. Kamera belakang HP akan aktif secara otomatis.
4. Arahkan kamera ke **Stiker QR Code** yang tertempel pada rak kopi di toko tersebut.
5. Begitu QR terbaca (dalam hitungan detik), HP akan berbunyi beep ringan dan sistem langsung membawa Anda ke halaman **Formulir Kunjungan Toko**.

> **💡 Opsi Alternatif:** Jika kamera HP sedang bermasalah atau stiker QR basah/rusak, Anda tetap dapat mengklik nama toko dari daftar kunjungan di dashboard untuk masuk ke formulir secara manual.

### 3.4 Validasi GPS Geofencing (Anti-Curang)
1. Saat halaman kunjungan terbuka, sistem otomatis mengambil koordinat GPS HP Anda (`navigator.geolocation`).
2. Sistem menghitung jarak antara posisi Anda berdiri dengan koordinat toko yang terdaftar.
3. **Hasil Validasi**:
   - Jika jarak $\le 250$ meter: Muncul tanda hijau **"GPS Valid: Anda berada di lokasi toko (Jarak: XX meter)"**.
   - Jika jarak $> 250$ meter: Muncul tanda peringatan kuning/merah bahwa sopir berada jauh dari toko. Hal ini tercatat di log audit Owner untuk mencegah kecurangan (*check-in palsu* dari rumah).

### 3.5 Audit Sisa Stok Rak, Kalkulasi Barang Laku & Restock
Di halaman kunjungan toko:
1. **Lihat Tabel Daftar Produk Kopi** yang dititipkan di toko tersebut:
   - Kolom 1: Nama Produk & Gramasi (misal: *Gayo 250g*).
   - Kolom 2: Stok Tercatat di Rak pada kunjungan sebelumnya (misal: 10 bungkus).
   - Kolom 3: **Sisa Stok Fisik di Rak**: Hitung sisa bungkus kopi yang ada di rak toko saat ini, lalu ketik angkanya (misal: sisa 3 bungkus).
   - Kolom 4: **Terjual (Otomatis)**: Sistem otomatis menghitung:
     $$\text{Laku Terjual} = 10 - 3 = 7\text{ bungkus}$$
   - Kolom 5: **Jumlah Restock**: Masukkan jumlah bungkus kopi baru yang Anda turunkan dari mobil box untuk mengisi rak kembali (misal: tambah 10 bungkus lagi).
2. Total penjualan rupiah akan langsung dihitung otomatis oleh sistem.

### 3.6 Pengambilan & Upload Foto Bukti Kunjungan (POD)
1. Di bagian bawah formulir, klik tombol **"Ambil Foto Bukti Kunjungan (POD)"**.
2. Kamera HP akan terbuka. Ambil foto rak kopi yang sudah rapi terisi atau foto bersama kepala toko/kasir Alfamart/Indomaret yang menerima barang.
3. Tuliskan nama kasir/staf toko yang menerima di kolom catatan jika diperlukan.

### 3.7 Menyelesaikan Kunjungan & Berpindah Toko
1. Klik tombol hijau **"Simpan & Selesaikan Kunjungan"**.
2. Sistem akan menyimpan data:
   - Stok rak toko ter-update.
   - Penjualan tercatat ke omset harian.
   - Titik GPS dan foto tersimpan.
3. Status toko di daftar rute Anda akan berubah menjadi centang hijau **"SELESAI"**.
4. Lanjutkan perjalanan ke toko ritel berikutnya!

---

## 4. PANDUAN OPERASIONAL UNTUK ADMIN & STAF KANTOR

Admin bertugas menyiapkan data toko, mencetak stiker QR Code, dan menjaga akurasi data master.

### 4.1 Menambahkan Toko / Cabang Baru (Alfamart, Indomaret, dll)
1. Login sebagai Admin (`admin@kopi.com`).
2. Masuk ke menu **Master Data** -> **Pelanggan & Toko** (`/customers`).
3. Klik tombol **"+ Tambah Pelanggan / Toko Baru"** (`/customers/create`).
4. Isi formulir:
   - **Tipe Pelanggan**: Pilih `Toko / Ritel Modern`.
   - **Jaringan Ritel**: Pilih salah satu (`Alfamart`, `Indomaret`, `Kafe & Kedai Kopi`, `Toko Kelontong`, `Supermarket Lokal`, atau `Lainnya`).
   - **Nama Toko / Bisnis**: Contoh: `Alfamart Sunset Road Kuta`.
   - **Nama PIC / Kepala Toko**: Contoh: `Ibu Wayan (Kepala Toko)`.
   - **No. Telepon / WhatsApp Toko**: Contoh: `081234567800`.
   - **Alamat Lengkap**: Alamat jalan toko.
   - **Termin Pembayaran**: Pilih `Konsinyasi (Tempo 14 Hari)` atau sesuai kontrak kerja sama.
   - **Plafon Kredit**: Masukkan batas nominal nilai barang yang boleh dititipkan di toko tersebut (misal: `Rp 5.000.000`).

### 4.2 Mengisi Koordinat GPS (Latitude & Longitude) dari Google Maps
Agar fitur geofencing dan navigasi sopir berfungsi akurat:
1. Buka [Google Maps](https://maps.google.com) di browser komputer Anda.
2. Cari nama atau lokasi toko ritel tersebut.
3. **Klik kanan tepat di atas titik toko** pada peta Google Maps.
4. Kotak menu akan muncul. Baris paling atas adalah angka koordinat (contoh: `-8.672500, 115.215000`).
5. **Klik pada angka tersebut** untuk menyalin (*copy*) secara otomatis.
6. Kembali ke form input toko di sistem Kopi Distro:
   - Tempelkan angka pertama (sebelum koma) ke kolom **Latitude** (contoh: `-8.672500`).
   - Tempelkan angka kedua (setelah koma) ke kolom **Longitude** (contoh: `-115.215000`).
7. Klik tombol preview peta untuk memastikan pin berada di lokasi yang tepat.

### 4.3 Mengatur Batas Minimum Peringatan Stok Rak Toko
- Pada kolom **Batas Minimum Stok Rak (Alert)**, masukkan angka batas kritis (default: `5` pcs).
- Jika stok di rak toko turun di bawah angka ini, pin toko di peta secara otomatis berubah menjadi oranye (*Low Stock*), mengingatkan staf kantor untuk menjadwalkan kunjungan sopir.

### 4.4 Mengunduh & Mencetak Stiker QR Code Toko
1. Buka halaman detail toko atau daftar pelanggan.
2. Klik tombol **"Cetak Stiker QR Toko"** (contoh URL: `/driver/print-qr/1`).
3. Halaman pratinjau stiker siap cetak akan tampil:
   - Memuat logo perusahaan Kopi Distro.
   - Kode QR beresolusi tinggi dengan token keamanan unik.
   - Nama toko dan kode cabang.
   - Petunjuk untuk sopir.
4. Tekan tombol **"Cetak Stiker Sekarang"** atau gunakan shortcut `Ctrl + P` di keyboard.
5. Gunakan kertas stiker label tahan air (ukuran standar A6 atau 10x10 cm).
6. Berikan stiker kepada sopir untuk ditempelkan di tepi rak pajangan kopi di toko ritel bersangkutan.

---

## 5. DAFTAR KREDENSIAL AKUN & PASSWORD

Semua akun pengujian di lingkungan lokal menggunakan kata sandi: **`password123`**

| Peran (Role) | Email Login | Password | Fungsi Akses |
|---|---|:---:|---|
| **Owner** | `owner@kopi.com` | `password123` | Dashboard Finansial, Omset, Laba HPP, Peta Rute, Pengawasan Menyeluruh |
| **Admin** | `admin@kopi.com` | `password123` | Master Toko, Setup GPS, Cetak Stiker QR, Master Produk & Kategori |
| **Driver 1** | `driver@kopi.com` | `password123` | Portal Mobile Sopir Armada 1, Scanner QR, Input Audit Stok & Restock |
| **Driver 2** | `driver2@kopi.com` | `password123` | Portal Mobile Sopir Armada 2 (Cadangan / Wilayah Luar Kota) |
| **Warehouse** | `warehouse@kopi.com` | `password123` | Manajemen Stok Gudang Utama, Surat Jalan, Penerimaan Supplier |
| **Sales** | `sales@kopi.com` | `password123` | Pesanan Grosir B2B, Monitoring Pelanggan Mitra |
| **Finance** | `finance@kopi.com` | `password123` | Penagihan Faktur, Catat Pelunasan, Pemulihan Plafon Kredit |

---

## 6. TANYA JAWAB & TROUBLESHOOTING (FAQ)

### Q1: Bagaimana jika browser HP sopir tidak memunculkan kamera scanner?
- **Penyebab**: Izin kamera browser belum diaktifkan atau website dibuka tanpa HTTPS di luar localhost.
- **Solusi**:
  1. Pada Google Chrome di HP, klik ikon gembok / pengaturan di sebelah kiri address bar.
  2. Pilih menu **Izin Situs (Site Permissions)** -> aktifkan **Kamera** menjadi **Izinkan (Allow)**.
  3. Muat ulang (*refresh*) halaman scanner.

### Q2: Mengapa status GPS menunjukkan "Di Luar Zona Toko"?
- **Penyebab**: GPS HP belum mengunci posisi akurat (*low accuracy*) atau sopir masih berada di luar radius toleransi 250 meter dari titik koordinat toko.
- **Solusi**: Pastikan layanan lokasi / GPS di HP sopir berada pada mode "Akurasi Tinggi" (High Accuracy), dan pastikan sopir sudah berada di area parkir atau dalam toko.

### Q3: Bagaimana cara menguji sistem jika dicoba di laptop tanpa membawa HP ke lapangan?
- Di halaman detail kunjungan (`/driver/visit/{visit}`), koordinat HP di laptop Anda akan otomatis terdeteksi. Sistem tetap memperbolehkan penyimpanan data meskipun berada di luar zona, namun akan diberi label kuning *"Di luar geofence"* sebagai penanda simulasi.

### Q4: Apakah sistem bisa diakses dari HP sopir jika server masih di XAMPP laptop?
- **Bisa!** Pastikan laptop dan HP terhubung ke jaringan Wi-Fi yang sama (atau tethering HP ke laptop):
  1. Cari tahu IP laptop Anda (buka CMD -> ketik `ipconfig` -> lihat IPv4, misal: `192.168.1.15`).
  2. Buka browser di HP sopir dan ketik: `http://192.168.1.15:8080/kopi/public/login`.
  3. Sopir sudah bisa menggunakan aplikasi secara penuh dari HP!

### Q5: Jika database ingin di-reset kembali seperti semula, apa perintahnya?
- Buka terminal / PowerShell di direktori `d:\Xampp2\htdocs\kopi`, lalu jalankan:
  ```powershell
  php artisan migrate:fresh --seed
  ```
  Sistem akan langsung kembali bersih lengkap dengan 6 toko ritel demo, akun pengguna, dan data pengujian.
