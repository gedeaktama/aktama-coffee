# Status Proyek Sistem Distribusi & Penjualan Kopi

Dokumen ini melacak status terkini pembangunan sistem, file yang telah dibuat, hasil pengujian, dan panduan untuk melanjutkan ke tahap berikutnya.

---

## Ringkasan Proyek
- **Nama Aplikasi**: Kopi Distro (Sistem Distribusi dan Penjualan Kopi Kemasan)
- **Framework**: Laravel 11.57.0 (PHP 8.2.12 CLI, MySQL/MariaDB XAMPP Port 3306)
- **Direktori Kerja**: `d:\Xampp2\htdocs\kopi`
- **Database**: `kopi` di MySQL XAMPP
- **Frontend Stack**: Blade + Bootstrap 5.3 + JavaScript minimalis
- **URL Lokal**: `http://localhost:8080/kopi/public`

---

## Status Tahapan

| Tahap | Deskripsi Modul | Status | Keterangan |
|---|---|---|---|
| **Tahap 1** | Fondasi, Database, Autentikasi 6 Role, Middleware & Testing | **SELESAI & TERUJI (100%)** | 13/13 test lulus (37 assertions). |
| **Tahap 2** | Master Data (Kategori, Produk & Varian/SKU, Supplier, Pelanggan Bisnis) | **SELESAI & TERUJI (100%)** | 20/20 test lulus (59 assertions). |
| **Tahap 3** | Persediaan & Pembelian Supplier (Stok Masuk, Mutasi, Stok Minimum) | **SIAP DIMULAI** | Menunggu persetujuan pengguna. |
| **Tahap 4** | Penjualan Grosir B2B & Retail Online (Order Sales, Keranjang, Checkout) | Terjadwal | Akan dikerjakan setelah Tahap 3. |
| **Tahap 5** | Pembayaran & Manajemen Piutang Tempo (Faktur, Plafon Kredit, TOP) | Terjadwal | Akan dikerjakan setelah Tahap 4. |
| **Tahap 6** | Pengiriman (Surat Jalan), Dashboard Owner & Laporan HPP / Laba Kotor | Terjadwal | Akan dikerjakan setelah Tahap 5. |
| **Tahap 7** | Pengujian Menyeluruh, Hardening Keamanan & Panduan Shared Hosting | Terjadwal | Tahap akhir. |

---

## Log Perubahan: Tahap 2 — Master Data

### 1. Migrasi & Skema Basis Data yang Dibuat
- `database/migrations/2026_10_11_000001_create_categories_table.php`: Tabel `categories` (nama, slug unik, deskripsi, is_active).
- `database/migrations/2026_10_11_000002_create_products_table.php`: Tabel `products` (category_id, nama, slug unik, brand, coffee_type [Arabika, Robusta, Blend], form [bean, ground, both], image, is_active).
- `database/migrations/2026_10_11_000003_create_product_variants_table.php`: Tabel `product_variants` (product_id, sku unik & terindeks, variant_name, package_weight_grams [100, 250, 500, 1000], grind_form, barcode, cost_price [DECIMAL 15,2], selling_price [DECIMAL 15,2 - satu harga jual standar per SKU], current_stock, minimum_stock, is_active).
- `database/migrations/2026_10_11_000004_create_suppliers_table.php`: Tabel `suppliers` (code unik, name, pic_name, phone, email, address, is_active).
- `database/migrations/2026_10_11_000005_create_customers_table.php`: Tabel `customers` (code unik, customer_type [kafe, toko, reseller, retail], business_name, pic_name, phone, email, address, shipping_address, sales_user_id FK, user_id FK, credit_limit [DECIMAL 15,2], payment_terms_days [INT: 0=tunai, 7, 14, 30 hari], is_active).

### 2. Model Eloquent & Relasi
- `app/Models/Category.php`: Relasi `hasMany(Product::class)`, auto slug generator.
- `app/Models/Product.php`: Relasi `belongsTo(Category::class)`, `hasMany(ProductVariant::class)`.
- `app/Models/ProductVariant.php`: Relasi `belongsTo(Product::class)`, accessor `formatted_price`, `formatted_cost`, helper `isLowStock()`.
- `app/Models/Supplier.php`: Manajemen supplier kopi kemasan.
- `app/Models/Customer.php`: Relasi `belongsTo(User::class, 'sales_user_id')`, helper `isB2B()`, `hasCreditFacility()`, `formatted_credit_limit`, `type_label`.

### 3. Controller & Rute Otorisasi Role
- `app/Http/Controllers/CategoryController.php`: CRUD Kategori (Owner & Admin).
- `app/Http/Controllers/ProductController.php`: CRUD Produk & Varian (Owner, Admin mengelola; Sales, Warehouse, Finance dapat melihat).
- `app/Http/Controllers/SupplierController.php`: CRUD Supplier (Owner, Admin mengelola; Warehouse dapat melihat).
- `app/Http/Controllers/CustomerController.php`: CRUD Pelanggan Bisnis (Owner, Admin, Sales mengelola dengan pembatasan: Sales dilarang memberikan kredit tanpa persetujuan Admin/Owner).

### 4. Tampilan Antarmuka (Blade + Bootstrap 5)
- `resources/views/master/categories/index.blade.php`: Tabel kategori dengan modal tambah & edit.
- `resources/views/master/products/index.blade.php`: Daftar produk dengan filter pencarian, kategori, jenis kopi, dan ringkasan SKU.
- `resources/views/master/products/create.blade.php`: Formulir pembuatan produk baru beserta varian kemasan & SKU awal.
- `resources/views/master/products/show.blade.php`: Halaman detail produk dan manajemen lengkap varian SKU.
- `resources/views/master/products/edit.blade.php`: Formulir edit data produk.
- `resources/views/master/suppliers/index.blade.php`: Tabel supplier pemasok dengan modal tambah & edit.
- `resources/views/master/customers/index.blade.php`: Tabel pelanggan B2B & retail dengan tab filter tipe (Kafe, Toko, Reseller, Retail), plafon kredit, dan termin tempo.
- `resources/views/master/customers/create.blade.php`: Formulir pendaftaran pelanggan bisnis baru.
- `resources/views/master/customers/show.blade.php`: Halaman profil pelanggan bisnis (PIC, plafon kredit, termin tempo, alamat kirim, sales PIC).
- `resources/views/master/customers/edit.blade.php`: Formulir edit data pelanggan bisnis.
- `resources/views/layouts/app.blade.php`: Penambahan menu navigasi dropdown Master Data.
- `resources/views/dashboard/index.blade.php`: Penambahan kartu metrik ringkasan master data (Produk, SKU, Supplier, Pelanggan B2B) dan update roadmap.

### 5. Seeder Data Awal (`MasterDataSeeder.php`)
- **3 Kategori**: Kopi Arabika Single Origin, Kopi Robusta Premium, Kopi House Blend & Espresso.
- **3 Produk Unggulan & 10 Varian SKU**:
  - Kopi Arabika Aceh Gayo: kemasan 100g, 250g, 500g, 1kg (SKU-AG-100G s/d SKU-AG-1KG).
  - Kopi Arabika Toraja Sapan: kemasan 250g, 500g, 1kg (SKU-AT-250G s/d SKU-AT-1KG).
  - Kopi Robusta Lampung Gold: kemasan 250g, 500g, 1kg (SKU-RL-250G s/d SKU-RL-1KG).
- **2 Supplier**: PT Sumatra Mandiri Kopi (Medan), CV Java Prima Rostery (Temanggung).
- **4 Pelanggan**:
  - Kafe Senja Nusantara (`CUST-KAFE-001`, Plafon Rp 5.000.000, Tempo 14 Hari).
  - Toko Kelontong Berkah Makmur (`CUST-TOKO-001`, Plafon Rp 3.000.000, Tempo 7 Hari).
  - Reseller Kopi Mantap (`CUST-RSL-001`, Plafon Rp 2.500.000, Tempo 14 Hari).
  - Pelanggan Retail Contoh (`CUST-RET-001`, Tunai, Rp 0).

---

## Hasil Pengujian Otomatis Terkini

```text
PASS  Tests\Unit\ExampleTest
✓ that true is true

PASS  Tests\Feature\AuthTest
✓ login screen can be rendered
✓ users can authenticate using the login screen
✓ users can not authenticate with invalid password
✓ inactive users can not authenticate
✓ public registration strictly assigns customer role
✓ user can logout

PASS  Tests\Feature\ExampleTest
✓ the application returns a successful response

PASS  Tests\Feature\MasterDataTest
✓ admin can view and create category
✓ admin can create product with initial sku variant
✓ sku must be unique across variants
✓ admin can create supplier
✓ admin and sales can create b2b customer
✓ sales cannot self grant unauthorized credit limit
✓ customer role is strictly forbidden from master data

PASS  Tests\Feature\RoleMiddlewareTest
✓ unauthenticated user cannot access dashboard
✓ customer role is forbidden from dashboard
✓ staff roles can access dashboard
✓ only owner can access owner zone
✓ only finance can access finance zone

Tests:    20 passed (59 assertions)
Duration: 1.68s
```

---

## Langkah Selanjutnya (Tahap 3 — Persediaan dan Pembelian)
Setelah mendapat persetujuan:
1. Migrasi dan Model:
   - Pembelian dari Supplier (`purchases`)
   - Item Pembelian (`purchase_items`)
   - Mutasi & Riwayat Stok Persediaan (`stock_movements`)
2. Alur Transaksi Pembelian:
   - Pembuatan PO Pembelian kepada supplier
   - Konfirmasi penerimaan fisik barang (Stok hanya bertambah setelah konfirmasi penerimaan fisik sesuai aturan Section 8)
   - Kartu mutasi stok per SKU yang dapat diaudit
   - Penyesuaian stok (*Stock Adjustment*) dengan alasan dan otorisasi
   - Peringatan stok minimum
3. Antarmuka Dashboard Gudang & Admin
4. Pengujian Otomatis Transaksi Pembelian & Mutasi Stok.
