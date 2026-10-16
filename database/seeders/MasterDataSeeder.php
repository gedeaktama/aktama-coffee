<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Customer;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\Supplier;
use App\Models\User;
use Illuminate\Database\Seeder;

class MasterDataSeeder extends Seeder
{
    /**
     * Seed katalog produk resmi PT Kopi Bintang Harapan berdasarkan Surat Daftar Harga Resmi Perusahaan (September 2026).
     */
    public function run(): void
    {
        // 1. Kategori Produk Resmi
        $catRobusta = Category::updateOrCreate(
            ['slug' => 'kopi-bubuk-robusta'],
            [
                'name' => 'Kopi Bubuk Robusta',
                'description' => '100% Kopi bubuk Robusta murni dari dataran tinggi Sulawesi Tengah dengan aroma khas dan cita rasa nikmat.',
                'is_active' => true,
            ]
        );

        $catArabika = Category::updateOrCreate(
            ['slug' => 'kopi-bubuk-arabika'],
            [
                'name' => 'Kopi Bubuk Arabika',
                'description' => '100% Kopi bubuk Arabika pegunungan Sulawesi dengan karakter aroma harum, fruity, keasaman lembut, dan body bersih elegan.',
                'is_active' => true,
            ]
        );

        // 2. Supplier Resmi Perusahaan
        Supplier::updateOrCreate(
            ['code' => 'SUP-KBH-01'],
            [
                'name' => 'PT Kopi Bintang Harapan',
                'pic_name' => 'Hartono Limadhy (Pimpinan)',
                'phone' => '0451-422739',
                'email' => 'produksi@bintangharapan.co.id',
                'address' => 'Jl. R.E. Martadinata / Trans Sulawesi KM.5, Palu - Sulteng',
                'is_active' => true,
            ]
        );

        // ----------------------------------------------------
        // 3. PRODUK 1: Kopi Bubuk Robusta Bintang Harapan
        // ----------------------------------------------------
        $prodRobusta = Product::updateOrCreate(
            ['slug' => 'kopi-bubuk-robusta-bintang-harapan'],
            [
                'category_id' => $catRobusta->id,
                'name' => 'Kopi Bubuk Robusta Bintang Harapan',
                'brand' => 'Bintang Harapan',
                'coffee_type' => 'Robusta',
                'form' => 'ground',
                'net_weight' => '50g, 125g, 250g, 400g',
                'producer' => 'PT Kopi Bintang Harapan, Palu, Sulawesi Tengah',
                'origin' => 'Palu, Sulawesi Tengah, Indonesia',
                'composition' => '100% Kopi Bubuk Robusta Murni Pilihan',
                'serving_suggestion' => 'Masukkan 1-2 sendok makan kopi bubuk Robusta ke dalam cangkir, tambahkan gula sesuai selera, seduh dengan 150-200 ml air mendidih (95°C), aduk rata dan nikmati aroma khasnya.',
                'storage_instruction' => 'Simpan dalam wadah kedap udara di tempat yang sejuk dan kering, terhindar dari sinar matahari langsung.',
                'description' => 'Kopi Bubuk Robusta murni produksi PT Kopi Bintang Harapan, Palu, Sulawesi Tengah. Dibuat dari biji kopi Robusta pilihan terbaik dataran tinggi Sulawesi dengan sangrai presisi untuk menghasilkan aroma wangi yang kuat, rasa gurih mantap, dan crema tebal.',
                'image' => 'images/products/04_bintang_harapan_robusta_10g_depan_edit.png',
                'gallery' => [
                    'images/products/05_bintang_harapan_robusta_belakang_edit.png',
                ],
                'is_active' => true,
            ]
        );

        $robustaVariants = [
            // Netto 50 Gr
            [
                'sku' => 'SKU-BH-ROB-50G',
                'name' => 'Kopi Robusta Bintang Harapan 50g (Bungkus)',
                'weight' => 50,
                'grind' => 'Bubuk Halus',
                'barcode' => '8994418101065',
                'cost' => 3800.00,
                'price' => 7000.00,
                'price_toko' => 5675.68,
                'price_kafe' => 5500.00,
                'tier' => [
                    'packaging_dims' => '8,5 x 16 cm',
                    'carton_volume' => '25 Bks / Pak Karton',
                    'carton_dims' => '30 x 15,5 cm',
                    'price_toko_exclude_ppn' => 5675.68,
                    'price_toko_disc_5pct' => 5391.90,
                    'price_bal_carton' => 141891.89,
                ],
                'stock' => 200,
                'min' => 25,
            ],
            [
                'sku' => 'SKU-BH-ROB-50G-BAL',
                'name' => 'Kopi Robusta Bintang Harapan 50g (Pak/Bal Isi 25 Bks)',
                'weight' => 1250,
                'grind' => 'Bubuk Halus',
                'barcode' => '8994418101065-BAL',
                'cost' => 95000.00,
                'price' => 175000.00,
                'price_toko' => 141891.89,
                'price_kafe' => 137500.00,
                'tier' => [
                    'isi_per_bal' => 25,
                    'carton_dims' => '30 x 15,5 cm',
                    'price_toko_exclude_ppn' => 141891.89,
                    'price_toko_disc_5pct' => 134797.30,
                ],
                'stock' => 50,
                'min' => 5,
            ],
            // Netto 125 Gr
            [
                'sku' => 'SKU-BH-ROB-125G',
                'name' => 'Kopi Robusta Bintang Harapan 125g (Bungkus)',
                'weight' => 125,
                'grind' => 'Bubuk Halus',
                'barcode' => '8994418101713',
                'cost' => 9500.00,
                'price' => 17500.00,
                'price_toko' => 14189.00,
                'price_kafe' => 13500.00,
                'tier' => [
                    'packaging_dims' => '20 x 15 cm',
                    'carton_volume' => '50 Bks / Pak Karton',
                    'carton_dims' => '48 x 40 cm',
                    'price_toko_exclude_ppn' => 14189.00,
                    'price_toko_disc_5pct' => 13479.55,
                    'price_bal_carton' => 709450.00,
                ],
                'stock' => 200,
                'min' => 25,
            ],
            [
                'sku' => 'SKU-BH-ROB-125G-BAL',
                'name' => 'Kopi Robusta Bintang Harapan 125g (Pak/Bal Isi 50 Bks)',
                'weight' => 6250,
                'grind' => 'Bubuk Halus',
                'barcode' => '8994418101713-BAL',
                'cost' => 475000.00,
                'price' => 875000.00,
                'price_toko' => 709450.00,
                'price_kafe' => 675000.00,
                'tier' => [
                    'isi_per_bal' => 50,
                    'carton_dims' => '48 x 40 cm',
                    'price_toko_exclude_ppn' => 709450.00,
                    'price_toko_disc_5pct' => 673977.50,
                ],
                'stock' => 40,
                'min' => 5,
            ],
            // Netto 250 Gr
            [
                'sku' => 'SKU-BH-ROB-250G',
                'name' => 'Kopi Robusta Bintang Harapan 250g (Bungkus)',
                'weight' => 250,
                'grind' => 'Bubuk Halus',
                'barcode' => '8994418101102',
                'cost' => 19000.00,
                'price' => 35000.00,
                'price_toko' => 28378.00,
                'price_kafe' => 27000.00,
                'tier' => [
                    'packaging_dims' => '22 x 14 cm',
                    'carton_volume' => '50 Bks / Pak Karton',
                    'carton_dims' => '58 x 40 cm',
                    'price_toko_exclude_ppn' => 28378.00,
                    'price_toko_disc_5pct' => 26959.10,
                    'price_bal_carton' => 1418900.00,
                ],
                'stock' => 150,
                'min' => 20,
            ],
            [
                'sku' => 'SKU-BH-ROB-250G-BAL',
                'name' => 'Kopi Robusta Bintang Harapan 250g (Pak/Bal Isi 50 Bks)',
                'weight' => 12500,
                'grind' => 'Bubuk Halus',
                'barcode' => '8994418101102-BAL',
                'cost' => 950000.00,
                'price' => 1750000.00,
                'price_toko' => 1418900.00,
                'price_kafe' => 1350000.00,
                'tier' => [
                    'isi_per_bal' => 50,
                    'carton_dims' => '58 x 40 cm',
                    'price_toko_exclude_ppn' => 1418900.00,
                    'price_toko_disc_5pct' => 1347955.00,
                ],
                'stock' => 30,
                'min' => 5,
            ],
            // Netto 400 Gr
            [
                'sku' => 'SKU-BH-ROB-400G',
                'name' => 'Kopi Robusta Bintang Harapan 400g (Bungkus)',
                'weight' => 400,
                'grind' => 'Bubuk Halus',
                'barcode' => '8994418101119',
                'cost' => 29000.00,
                'price' => 53000.00,
                'price_toko' => 43243.00,
                'price_kafe' => 41000.00,
                'tier' => [
                    'packaging_dims' => '28 x 14 cm',
                    'carton_volume' => '30 Bks / Pak Karton',
                    'carton_dims' => '40 x 42 cm',
                    'price_toko_exclude_ppn' => 43243.00,
                    'price_toko_disc_5pct' => 41080.85,
                    'price_bal_carton' => 1297290.00,
                ],
                'stock' => 120,
                'min' => 15,
            ],
            [
                'sku' => 'SKU-BH-ROB-400G-BAL',
                'name' => 'Kopi Robusta Bintang Harapan 400g (Pak/Bal Isi 30 Bks)',
                'weight' => 12000,
                'grind' => 'Bubuk Halus',
                'barcode' => '8994418101119-BAL',
                'cost' => 870000.00,
                'price' => 1590000.00,
                'price_toko' => 1297290.00,
                'price_kafe' => 1230000.00,
                'tier' => [
                    'isi_per_bal' => 30,
                    'carton_dims' => '40 x 42 cm',
                    'price_toko_exclude_ppn' => 1297290.00,
                    'price_toko_disc_5pct' => 1232425.50,
                ],
                'stock' => 25,
                'min' => 5,
            ],
        ];

        foreach ($robustaVariants as $v) {
            ProductVariant::updateOrCreate(
                ['sku' => $v['sku']],
                [
                    'product_id' => $prodRobusta->id,
                    'variant_name' => $v['name'],
                    'package_weight_grams' => $v['weight'],
                    'grind_form' => $v['grind'],
                    'barcode' => $v['barcode'],
                    'cost_price' => $v['cost'],
                    'selling_price' => $v['price'],
                    'price_toko' => $v['price_toko'],
                    'price_kafe' => $v['price_kafe'],
                    'tier_pricing' => $v['tier'] ?? null,
                    'current_stock' => $v['stock'],
                    'minimum_stock' => $v['min'],
                    'is_active' => true,
                ]
            );
        }

        // ----------------------------------------------------
        // 4. PRODUK 2: Kopi Bubuk Arabika Bintang Harapan
        // ----------------------------------------------------
        $prodArabika = Product::updateOrCreate(
            ['slug' => 'kopi-bubuk-arabika-bintang-harapan'],
            [
                'category_id' => $catArabika->id,
                'name' => 'Kopi Bubuk Arabika Bintang Harapan',
                'brand' => 'Bintang Harapan',
                'coffee_type' => 'Arabika',
                'form' => 'ground',
                'net_weight' => '125g, 250g, 400g',
                'producer' => 'PT Kopi Bintang Harapan, Palu, Sulawesi Tengah',
                'origin' => 'Palu, Sulawesi Tengah, Indonesia',
                'composition' => '100% Kopi Bubuk Arabika Murni Pilihan',
                'serving_suggestion' => 'Seduh 10-12 gram kopi bubuk Arabika dengan air mendidih bersuhu 90-93°C sebanyak 150 ml. Nikmati aroma bunga dan buah segar serta aftertaste manis bersih tanpa ampas pahit.',
                'storage_instruction' => 'Simpan di tempat yang sejuk, kering, dan terhindar dari panas berlebih.',
                'description' => 'Kopi Bubuk Arabika murni produksi PT Kopi Bintang Harapan, Palu, Sulawesi Tengah. Dipetik dari perkebunan dataran tinggi Sulawesi Tengah, diolah dengan proses washed basah menghasilkan profil rasa floral, fruity, keasaman lembut yang segar, dan body bersih berkelas.',
                'image' => 'images/products/01_atapmerah_135g_depan_edit.png',
                'gallery' => [
                    'images/products/02_atapmerah_135g_belakang_edit.png',
                ],
                'is_active' => true,
            ]
        );

        $arabikaVariants = [
            // Netto 125 Gr
            [
                'sku' => 'SKU-BH-ARA-125G',
                'name' => 'Kopi Arabika Bintang Harapan 125g (Bungkus)',
                'weight' => 125,
                'grind' => 'Bubuk Halus',
                'barcode' => '8994418101720',
                'cost' => 11000.00,
                'price' => 21000.00,
                'price_toko' => 16554.00,
                'price_kafe' => 15500.00,
                'tier' => [
                    'packaging_dims' => '20 x 15 cm',
                    'carton_volume' => '50 Bks / Pak Karton',
                    'carton_dims' => '48 x 40 cm',
                    'price_toko_exclude_ppn' => 16554.00,
                    'price_toko_disc_5pct' => 15726.30,
                    'price_bal_carton' => 827700.00,
                ],
                'stock' => 150,
                'min' => 20,
            ],
            [
                'sku' => 'SKU-BH-ARA-125G-BAL',
                'name' => 'Kopi Arabika Bintang Harapan 125g (Pak/Bal Isi 50 Bks)',
                'weight' => 6250,
                'grind' => 'Bubuk Halus',
                'barcode' => '8994418101720-BAL',
                'cost' => 550000.00,
                'price' => 1050000.00,
                'price_toko' => 827700.00,
                'price_kafe' => 775000.00,
                'tier' => [
                    'isi_per_bal' => 50,
                    'carton_dims' => '48 x 40 cm',
                    'price_toko_exclude_ppn' => 827700.00,
                    'price_toko_disc_5pct' => 786315.00,
                ],
                'stock' => 30,
                'min' => 5,
            ],
            // Netto 250 Gr
            [
                'sku' => 'SKU-BH-ARA-250G',
                'name' => 'Kopi Arabika Bintang Harapan 250g (Bungkus)',
                'weight' => 250,
                'grind' => 'Bubuk Halus',
                'barcode' => '8994418101348',
                'cost' => 22000.00,
                'price' => 42000.00,
                'price_toko' => 33108.00,
                'price_kafe' => 31000.00,
                'tier' => [
                    'packaging_dims' => '22 x 14 cm',
                    'carton_volume' => '50 Bks / Pak Karton',
                    'carton_dims' => '58 x 40 cm',
                    'price_toko_exclude_ppn' => 33108.00,
                    'price_toko_disc_5pct' => 31452.60,
                    'price_bal_carton' => 1655400.00,
                ],
                'stock' => 120,
                'min' => 15,
            ],
            [
                'sku' => 'SKU-BH-ARA-250G-BAL',
                'name' => 'Kopi Arabika Bintang Harapan 250g (Pak/Bal Isi 50 Bks)',
                'weight' => 12500,
                'grind' => 'Bubuk Halus',
                'barcode' => '8994418101348-BAL',
                'cost' => 1100000.00,
                'price' => 2100000.00,
                'price_toko' => 1655400.00,
                'price_kafe' => 1550000.00,
                'tier' => [
                    'isi_per_bal' => 50,
                    'carton_dims' => '58 x 40 cm',
                    'price_toko_exclude_ppn' => 1655400.00,
                    'price_toko_disc_5pct' => 1572630.00,
                ],
                'stock' => 25,
                'min' => 5,
            ],
            // Netto 400 Gr
            [
                'sku' => 'SKU-BH-ARA-400G',
                'name' => 'Kopi Arabika Bintang Harapan 400g (Bungkus)',
                'weight' => 400,
                'grind' => 'Bubuk Halus',
                'barcode' => '8994418101355',
                'cost' => 34000.00,
                'price' => 63000.00,
                'price_toko' => 50450.00,
                'price_kafe' => 47000.00,
                'tier' => [
                    'packaging_dims' => '28 x 14 cm',
                    'carton_volume' => '30 Bks / Pak Karton',
                    'carton_dims' => '40 x 42 cm',
                    'price_toko_exclude_ppn' => 50450.00,
                    'price_toko_disc_5pct' => 47927.50,
                    'price_bal_carton' => 1513500.00,
                ],
                'stock' => 100,
                'min' => 10,
            ],
            [
                'sku' => 'SKU-BH-ARA-400G-BAL',
                'name' => 'Kopi Arabika Bintang Harapan 400g (Pak/Bal Isi 30 Bks)',
                'weight' => 12000,
                'grind' => 'Bubuk Halus',
                'barcode' => '8994418101355-BAL',
                'cost' => 1020000.00,
                'price' => 1890000.00,
                'price_toko' => 1513500.00,
                'price_kafe' => 1410000.00,
                'tier' => [
                    'isi_per_bal' => 30,
                    'carton_dims' => '40 x 42 cm',
                    'price_toko_exclude_ppn' => 1513500.00,
                    'price_toko_disc_5pct' => 1437825.00,
                ],
                'stock' => 20,
                'min' => 5,
            ],
        ];

        foreach ($arabikaVariants as $v) {
            ProductVariant::updateOrCreate(
                ['sku' => $v['sku']],
                [
                    'product_id' => $prodArabika->id,
                    'variant_name' => $v['name'],
                    'package_weight_grams' => $v['weight'],
                    'grind_form' => $v['grind'],
                    'barcode' => $v['barcode'],
                    'cost_price' => $v['cost'],
                    'selling_price' => $v['price'],
                    'price_toko' => $v['price_toko'],
                    'price_kafe' => $v['price_kafe'],
                    'tier_pricing' => $v['tier'] ?? null,
                    'current_stock' => $v['stock'],
                    'minimum_stock' => $v['min'],
                    'is_active' => true,
                ]
            );
        }

        // 5. Customer Mitra Ritel Resmi: PT Midi Utama Indonesia Tbk (Alfamidi)
        $sales = User::where('role', User::ROLE_SALES)->first() ?? User::first();

        Customer::updateOrCreate(
            ['code' => 'CUST-MIDI-001'],
            [
                'customer_type' => Customer::TYPE_KONSINYASI,
                'store_chain' => Customer::STORE_CHAIN_ALFAMIDI,
                'business_name' => 'Alfamidi Super Sam Ratulangi Palu',
                'pic_name' => 'Bambang Irawan (Store Manager)',
                'phone' => '081242334455',
                'email' => 'midi.samratulangi@alfamidiku.com',
                'address' => 'Jl. Sam Ratulangi No. 88, Palu, Sulawesi Tengah',
                'shipping_address' => 'Jl. Sam Ratulangi No. 88, Palu, Sulawesi Tengah',
                'latitude' => -0.892400,
                'longitude' => 119.873200,
                'qr_token' => 'QR-MIDI-SR-001',
                'sales_user_id' => $sales ? $sales->id : null,
                'credit_limit' => 15000000,
                'payment_terms_days' => 30,
                'min_stock_alert' => 10,
                'is_active' => true,
            ]
        );

        Customer::updateOrCreate(
            ['code' => 'CUST-MIDI-002'],
            [
                'customer_type' => Customer::TYPE_KONSINYASI,
                'store_chain' => Customer::STORE_CHAIN_ALFAMIDI,
                'business_name' => 'Alfamidi Diponegoro Palu',
                'pic_name' => 'Dewi Sartika (Kepala Toko)',
                'phone' => '081242334456',
                'email' => 'midi.diponegoro@alfamidiku.com',
                'address' => 'Jl. Diponegoro No. 45, Palu, Sulawesi Tengah',
                'shipping_address' => 'Jl. Diponegoro No. 45, Palu, Sulawesi Tengah',
                'latitude' => -0.897800,
                'longitude' => 119.865400,
                'qr_token' => 'QR-MIDI-DP-002',
                'sales_user_id' => $sales ? $sales->id : null,
                'credit_limit' => 12000000,
                'payment_terms_days' => 30,
                'min_stock_alert' => 10,
                'is_active' => true,
            ]
        );

        // Tambahkan toko Alfamart mitra lokal untuk kompatibilitas pencarian
        Customer::updateOrCreate(
            ['code' => 'CUST-ALFA-001'],
            [
                'customer_type' => Customer::TYPE_KONSINYASI,
                'store_chain' => Customer::STORE_CHAIN_ALFAMART,
                'business_name' => 'Alfamart Sam Ratulangi Palu',
                'pic_name' => 'Hendra Setiawan',
                'phone' => '081234111222',
                'email' => 'alfa.palu@retail.id',
                'address' => 'Jl. Sam Ratulangi No. 120, Palu, Sulawesi Tengah',
                'shipping_address' => 'Jl. Sam Ratulangi No. 120, Palu, Sulawesi Tengah',
                'latitude' => -0.895000,
                'longitude' => 119.875000,
                'qr_token' => 'QR-ALFA-PL-001',
                'sales_user_id' => $sales ? $sales->id : null,
                'credit_limit' => 10000000,
                'payment_terms_days' => 14,
                'min_stock_alert' => 10,
                'is_active' => true,
            ]
        );
    }
}
