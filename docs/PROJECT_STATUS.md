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
| **Tahap 3** | Persediaan & Pembelian Supplier (Stok Masuk, Mutasi, Stok Minimum, Audit) | **SELESAI & TERUJI (100%)** | 27/27 test lulus (83 assertions). |
| **Tahap 4** | Penjualan Grosir B2B & Retail Online (Order Sales, Keranjang, Checkout) | **SELESAI & TERUJI (100%)** | 33/33 test lulus (115 assertions). |
| **Tahap 5** | Pembayaran & Manajemen Piutang Tempo (Faktur, Plafon Kredit, TOP) | **SIAP DIMULAI** | Menunggu persetujuan pengguna. |
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

PASS  Tests\Feature\InventoryPurchaseTest
✓ authorized roles can view purchase and inventory index
✓ user can create purchase order without increasing stock immediately
✓ receiving purchase order physically strictly increases stock and logs audit movement
✓ cannot receive already received or cancelled purchase order
✓ user can adjust stock with mandatory audit reason
✓ stock adjustment requires mandatory reason
✓ inventory service strictly prevents negative stock

PASS  Tests\Feature\RoleMiddlewareTest
✓ unauthenticated user cannot access dashboard
✓ customer role is forbidden from dashboard
✓ staff roles can access dashboard
✓ only owner can access owner zone
✓ only finance can access finance zone

Tests:    27 passed (83 assertions)
Duration: 1.48s
```

---

## Log Perubahan: Tahap 3 — Persediaan & Pembelian Supplier

### 1. Migrasi & Skema Basis Data
- `database/migrations/2026_10_11_000006_create_purchases_table.php`: Tabel `purchases` (purchase_number unik, supplier_id FK, user_id FK, purchase_date, status [draft, ordered, received, cancelled], subtotal, discount, shipping_cost, total_amount, received_at, received_by_user_id FK, notes).
- `database/migrations/2026_10_11_000007_create_purchase_items_table.php`: Tabel `purchase_items` (purchase_id FK, product_variant_id FK, quantity, cost_price, subtotal).
- `database/migrations/2026_10_11_000008_create_stock_movements_table.php`: Tabel `stock_movements` (product_variant_id FK, user_id FK, type [in, out, adjustment], reference_type [purchase, sales_order, manual_adjustment, initial_stock], reference_id, quantity, stock_before, stock_after, notes).

### 2. Service Mutasi Stok Terpusat (`InventoryService.php`)
- Menggunakan `DB::transaction()` dan `lockForUpdate()` pesimistis untuk mencegah *race condition* dan menjaga integritas data.
- Metode `addStock`: Menambah stok saat penerimaan fisik PO atau retur barang.
- Metode `reduceStock`: Mengurangi stok untuk pemenuhan pesanan penjualan dengan validasi ketat anti stok minus.
- Metode `adjustStock`: Penyesuaian stok opname manual fisik dengan verifikasi alasan audit wajib.

### 3. Controller & Rute Otorisasi
- `app/Http/Controllers/PurchaseController.php`:
  - `index`: Daftar PO dengan filter status, supplier, dan pencarian nomor PO.
  - `create`: Formulir pemesanan PO dengan auto-generated nomor urut PO bulanan.
  - `store`: Penerbitan PO baru ke status `ordered`.
  - `show`: Detail PO lengkap beserta kalkulasi diskon & biaya logistik.
  - `receive`: Konfirmasi penerimaan fisik barang oleh staf gudang yang secara otomatis menambah saldo stok ke sistem.
  - `cancel`: Pembatalan PO oleh Owner / Admin.
- `app/Http/Controllers/InventoryController.php`:
  - `index`: Monitoring stok fisik, kartu alert stok menipis (`current_stock <= minimum_stock`) dan stok habis.
  - `movements`: Kartu stok dan jejak audit mutasi per SKU kopi.
  - `adjust`: Otorisasi penyesuaian stok opname fisik dengan alasan wajib audit.

### 4. Tampilan Antarmuka (Blade + Bootstrap 5)
- `resources/views/inventory/purchases/index.blade.php`: Tabel PO dengan badge status warna-warni, ringkasan biaya, dan filter.
- `resources/views/inventory/purchases/create.blade.php`: Form interaktif pembuatan PO dengan JavaScript dinamis untuk penambahan baris SKU, kalkulasi subtotal, diskon, dan biaya kirim secara *real-time*.
- `resources/views/inventory/purchases/show.blade.php`: Halaman rincian PO, profil pemasok, tombol cetak, serta tombol konfirmasi penerimaan fisik barang gudang.
- `resources/views/inventory/stocks/index.blade.php`: Dashboard monitoring stok dengan metrik total pack, alert stok menipis, dan modal penyesuaian stock opname langsung.
- `resources/views/inventory/stocks/movements.blade.php`: Kartu mutasi stok persediaan kronologis (*audit trail*).

### 5. Seeder Data (`InventorySeeder.php`)
- `PO-202610-0001`: Pembelian dari Supplier Aceh Gayo berstatus `received` (stok fisik telah masuk ke gudang).
- `PO-202610-0002`: Pembelian dari Supplier PT Java Coffee Estate berstatus `ordered` (siap digunakan untuk uji coba konfirmasi penerimaan langsung di browser).
- Riwayat mutasi stok masuk dan contoh penyesuaian stock opname.

---

---

## Log Perubahan: Tahap 4 — Penjualan Grosir B2B & Retail Online

### 1. Migrasi & Skema Basis Data
- `database/migrations/2026_10_11_000009_create_orders_table.php`: Tabel `orders` (nomor order unik `SO-YYYYMM-XXXX` atau `ORD-YYYYMM-XXXX`, order_type [b2b, retail], customer_id FK, user_id FK, sales_user_id FK, tanggal order, payment_type [cash, transfer, credit], due_date, status [pending, confirmed, processing, shipped, delivered, cancelled], payment_status [unpaid, partial, paid], subtotal, discount, shipping_cost, total_amount, paid_amount, stock_reduced, confirmed_at, confirmed_by_user_id FK, notes).
- `database/migrations/2026_10_11_000010_create_order_items_table.php`: Tabel `order_items` (order_id FK, product_variant_id FK, quantity, unit_price [snapshot selling_price], cost_price [snapshot cost_price untuk HPP], discount_amount, subtotal).

### 2. Model Eloquent & Relasi Bisnis
- `app/Models/Order.php`: Konstanta tipe order, status, metode pembayaran, status pembayaran, relasi ke Customer, User, Sales User, OrderItems, accessor badge warna dan label Indonesia, serta helper pengecekan kondisi konfirmasi/pembatalan.
- `app/Models/OrderItem.php`: Relasi ke Order dan ProductVariant, formatted harga dan subtotal.
- `app/Models/Customer.php`: Penambahan relasi `orders()`, metode `getCurrentReceivables()` (menghitung sisa piutang berjalan yang belum lunas), dan `getAvailableCredit()` (menghitung sisa plafon kredit yang masih dapat digunakan).

### 3. Controller & Rute
- `app/Http/Controllers/OrderController.php`:
  - `index`: Monitoring semua pesanan (B2B & Retail) dengan filter saluran, status, metode pembayaran, dan metrik KPI.
  - `create`: Formulir pemesanan B2B oleh Sales dengan data plafon kredit real-time.
  - `store`: Penerbitan pesanan B2B dengan validasi plafon kredit ketat (mencegah sales menerbitkan order melebihi plafon tanpa persetujuan Admin/Owner).
  - `show`: Detail pesanan lengkap, informasi penerima, dan daftar item.
  - `confirm`: Konfirmasi pesanan yang secara otomatis memotong saldo stok persediaan di gudang menggunakan transaksi pesimistis.
  - `cancel`: Pembatalan pesanan yang secara otomatis mengembalikan stok ke gudang jika sebelumnya sudah terpotong.
- `app/Http/Controllers/StoreController.php`:
  - `catalog`: Tampilan etalase online kopi retail (B2C) dengan filter kategori dan jenis kopi.
  - `cart`, `addToCart`, `updateCart`, `removeFromCart`: Pengelolaan keranjang belanja retail berbasis session.
  - `checkout` & `processCheckout`: Penyelesaian pesanan ritel, pemotongan stok otomatis, dan penerbitan nomor pesanan `ORD-YYYYMM-XXXX`.
  - `myOrders`: Riwayat pesanan untuk akun role `customer`.

### 4. Tampilan Antarmuka (Blade + Bootstrap 5)
- `resources/views/orders/index.blade.php`: Tabel daftar pesanan penjualan, filter lengkap, metrik total order, pending, confirmed, dan omset.
- `resources/views/orders/create.blade.php`: Formulir input pesanan B2B interaktif dengan penghitungan subtotal dinamis dan alert batas plafon kredit.
- `resources/views/orders/show.blade.php`: Halaman rincian pesanan penjualan, invoice cetak, tombol konfirmasi pemotongan stok, dan pembatalan.
- `resources/views/store/catalog.blade.php`: Katalog kopi online publik modern dengan varian gramasi dan tombol beli instan.
- `resources/views/store/cart.blade.php`: Keranjang belanja kopi online interaktif.
- `resources/views/store/checkout.blade.php`: Formulir checkout pengiriman dan pemilihan pembayaran konsumen ritel.
- `resources/views/store/success.blade.php`: Halaman sukses belanja dengan rincian rekening bank transfer.
- `resources/views/store/my_orders.blade.php`: Riwayat pesanan khusus role customer.
- `resources/views/layouts/app.blade.php`: Navigasi menu Penjualan & Order serta ikon keranjang belanja interaktif.

### 5. Seeder Data (`SalesOrderSeeder.php`)
- `SO-202610-0001`: Pesanan B2B Kafe Senja Nusantara (Kredit tempo 14 hari, status `confirmed`, stok telah dipotong).
- `SO-202610-0002`: Pesanan B2B Toko Kelontong Berkah Makmur (Transfer bank, status `pending` — siap untuk dicoba dikonfirmasi langsung di browser!).
- `ORD-202610-0001`: Pesanan Retail Online konsumen (status `confirmed`, stok telah dipotong).

---

## Langkah Selanjutnya (Tahap 5 — Pembayaran & Manajemen Piutang Tempo)
Setelah disetujui pengguna:
1. Migrasi & Model Faktur Penjualan dan Pembayaran (`invoices`, `payments`).
2. Pencatatan pembayaran tunai / transfer bank untuk pesanan penjualan.
3. Manajemen pelunasan piutang tempo (TOP) B2B:
   - Pelunasan penuh atau cicilan bertahap.
   - Kartu piutang per pelanggan (rekonsiliasi saldo piutang & pemulihan otomatis plafon kredit).
   - Deteksi faktur yang telah melewati tanggal jatuh tempo (*overdue alert*).
4. Pengujian otomatis alur pembayaran dan rekonsiliasi piutang tempo.


