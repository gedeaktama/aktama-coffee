<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Customer;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\Supplier;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class MasterDataSeeder extends Seeder
{
    /**
     * Run the database seeds for official coffee catalog.
     */
    public function run(): void
    {
        // 1. Kategori Produk Resmi
        $catCampur = Category::updateOrCreate(
            ['slug' => 'kopi-bubuk-campur'],
            [
                'name' => 'Kopi Bubuk Campur',
                'description' => 'Kopi campur tradisional perpaduan biji kopi pilihan dan serelia dengan aroma khas dan gurih mantap.',
                'is_active' => true,
            ]
        );

        $catRobusta = Category::updateOrCreate(
            ['slug' => 'kopi-bubuk-robusta'],
            [
                'name' => 'Kopi Bubuk Robusta',
                'description' => '100% Kopi bubuk Robusta murni dari dataran tinggi Sulawesi Tengah dengan cita rasa nikmat dan aroma harum.',
                'is_active' => true,
            ]
        );

        $catGulaAren = Category::updateOrCreate(
            ['slug' => 'kopi-gula-aren'],
            [
                'name' => 'Kopi Gula Aren',
                'description' => 'Paduan kopi Robusta dan manis legit alami gula aren asli Sulawesi dalam kemasan sachet praktis.',
                'is_active' => true,
            ]
        );

        // 2. Produk Resmi & Varian Berdasarkan Katalog
        // ----------------------------------------------------
        // Produk 1: Kopi ATAPMERAH 135g
        // ----------------------------------------------------
        $prodAtapmerah135 = Product::updateOrCreate(
            ['slug' => 'kopi-atapmerah-135g'],
            [
                'category_id' => $catCampur->id,
                'name' => 'Kopi ATAPMERAH 135g — Kopi Bubuk Campur',
                'brand' => 'ATAPMERAH',
                'coffee_type' => 'Blend',
                'form' => 'ground',
                'net_weight' => '135 gram',
                'producer' => 'PT Kopi Bintang Harapan',
                'origin' => 'Palu, Sulawesi Tengah, Indonesia',
                'composition' => '60% kopi bubuk dan serelia',
                'serving_suggestion' => 'Masukkan kopi ke dalam cangkir, tambahkan gula atau krimer sesuai selera, tuangkan 200 ml air mendidih, lalu aduk hingga rata.',
                'storage_instruction' => 'Simpan di tempat yang kering dan sejuk.',
                'description' => 'Nikmati kopi ATAPMERAH, kopi campur produksi PT Kopi Bintang Harapan, Palu, Sulawesi Tengah. Dikemas dalam kemasan 135 gram untuk menemani waktu santai maupun aktivitas sehari-hari.',
                'image' => 'images/products/01_atapmerah_135g_depan_edit.png',
                'gallery' => [
                    'images/products/02_atapmerah_135g_belakang_edit.png',
                    'images/products/03_atapmerah_135g_belakang_detail_edit.png',
                    'images/products/08_atapmerah_135g_belakang_instruksi_edit.png',
                ],
                'is_active' => true,
            ]
        );

        $atapmerah135Variants = [
            [
                'sku' => 'SKU-AM-135G',
                'name' => 'Kopi ATAPMERAH 135g (Bungkus)',
                'weight' => 135,
                'grind' => 'Bubuk Halus',
                'cost' => 5000,
                'price' => 7500,
                'price_toko' => 7300,
                'price_kafe' => 7000,
                'tier' => ['tier_10_59' => 7300, 'tier_60_99' => 7250, 'tier_100_up' => 7000],
                'stock' => 150,
                'min' => 20,
            ],
            [
                'sku' => 'SKU-AM-135G-PAK5',
                'name' => 'Kopi ATAPMERAH 135g (Pak Isi 5 Bungkus)',
                'weight' => 675,
                'grind' => 'Bubuk Halus',
                'cost' => 24000,
                'price' => 37500,
                'price_toko' => 36500,
                'price_kafe' => 35000,
                'tier' => ['tier_10_59' => 36500, 'tier_60_99' => 36250, 'tier_100_up' => 35000],
                'stock' => 60,
                'min' => 10,
            ],
        ];

        foreach ($atapmerah135Variants as $v) {
            ProductVariant::updateOrCreate(
                ['sku' => $v['sku']],
                [
                    'product_id' => $prodAtapmerah135->id,
                    'variant_name' => $v['name'],
                    'package_weight_grams' => $v['weight'],
                    'grind_form' => $v['grind'],
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
        // Produk 2: Kopi Bubuk Robusta Bintang Harapan 10g
        // ----------------------------------------------------
        $prodRobusta10 = Product::updateOrCreate(
            ['slug' => 'kopi-bubuk-robusta-bintang-harapan-10g'],
            [
                'category_id' => $catRobusta->id,
                'name' => 'Kopi Bubuk Robusta Bintang Harapan 10g',
                'brand' => 'Bintang Harapan',
                'coffee_type' => 'Robusta',
                'form' => 'ground',
                'net_weight' => '10 gram',
                'producer' => 'Bintang Harapan, Palu, Indonesia',
                'origin' => 'Palu, Sulawesi Tengah, Indonesia',
                'composition' => '100% kopi bubuk Robusta',
                'serving_suggestion' => 'Masukkan satu sachet ke dalam cangkir, tuangkan air panas sesuai petunjuk pada kemasan, tambahkan gula sesuai selera bila diinginkan, lalu aduk.',
                'storage_instruction' => 'Simpan di tempat yang kering dan sejuk.',
                'description' => 'Kopi Bubuk Robusta Bintang Harapan hadir dalam sachet praktis 10 gram. Pada kemasan, produk ini diperkenalkan sebagai kopi dari dataran tinggi Sulawesi Tengah dengan aroma khas dan cita rasa gurih serta nikmat.',
                'image' => 'images/products/04_bintang_harapan_robusta_10g_depan_edit.png',
                'gallery' => [
                    'images/products/05_bintang_harapan_robusta_belakang_edit.png',
                ],
                'is_active' => true,
            ]
        );

        $robusta10Variants = [
            [
                'sku' => 'SKU-BH-10G-SCT',
                'name' => 'Robusta Bintang Harapan 10g (Sachet)',
                'weight' => 10,
                'grind' => 'Bubuk Halus',
                'cost' => 600,
                'price' => 1200,
                'price_toko' => 900,
                'price_kafe' => 850,
                'stock' => 300,
                'min' => 50,
            ],
            [
                'sku' => 'SKU-BH-10G-RCG',
                'name' => 'Robusta Bintang Harapan 10g (Renceng 10 Sachet)',
                'weight' => 100,
                'grind' => 'Bubuk Halus',
                'cost' => 6000,
                'price' => 12000,
                'price_toko' => 9000,
                'price_kafe' => 8500,
                'stock' => 120,
                'min' => 20,
            ],
        ];

        foreach ($robusta10Variants as $v) {
            ProductVariant::updateOrCreate(
                ['sku' => $v['sku']],
                [
                    'product_id' => $prodRobusta10->id,
                    'variant_name' => $v['name'],
                    'package_weight_grams' => $v['weight'],
                    'grind_form' => $v['grind'],
                    'cost_price' => $v['cost'],
                    'selling_price' => $v['price'],
                    'price_toko' => $v['price_toko'],
                    'price_kafe' => $v['price_kafe'],
                    'tier_pricing' => null,
                    'current_stock' => $v['stock'],
                    'minimum_stock' => $v['min'],
                    'is_active' => true,
                ]
            );
        }

        // ----------------------------------------------------
        // Produk 3: Kopi Gula Aren Bintang Sulawesi 20g
        // ----------------------------------------------------
        $prodGulaAren20 = Product::updateOrCreate(
            ['slug' => 'kopi-gula-aren-bintang-sulawesi-20g'],
            [
                'category_id' => $catGulaAren->id,
                'name' => 'Kopi Gula Aren Bintang Sulawesi 20g',
                'brand' => 'Bintang Sulawesi',
                'coffee_type' => 'Blend',
                'form' => 'ground',
                'net_weight' => '20 gram',
                'producer' => 'Bintang Sulawesi, Palu, Sulawesi Tengah, Indonesia',
                'origin' => 'Palu, Sulawesi Tengah, Indonesia',
                'composition' => 'Gula aren dan kopi bubuk murni',
                'serving_suggestion' => 'Tuangkan satu sachet ke dalam cangkir, tambahkan 140 ml air mendidih, lalu aduk hingga larut dan tercampur rata.',
                'storage_instruction' => 'Simpan di tempat yang kering dan sejuk.',
                'description' => 'Kopi Gula Aren Bintang Sulawesi memadukan kopi Robusta dan gula aren dalam satu sachet praktis. Pilihan untuk menikmati sajian kopi dengan sentuhan manis gula aren di rumah, kantor, atau saat bepergian.',
                'image' => 'images/products/09_bintang_sulawesi_gula_aren_20g_depan_foto_asli.jpg',
                'gallery' => [
                    'images/products/06_bintang_sulawesi_gula_aren_20g_belakang_edit.png',
                ],
                'is_active' => true,
            ]
        );

        $gulaAren20Variants = [
            [
                'sku' => 'SKU-BS-20G-SCT',
                'name' => 'Kopi Gula Aren 20g (Sachet)',
                'weight' => 20,
                'grind' => 'Bubuk Kopi Aren',
                'cost' => 700,
                'price' => 1500,
                'price_toko' => 1100,
                'price_kafe' => 1000,
                'stock' => 250,
                'min' => 40,
            ],
            [
                'sku' => 'SKU-BS-20G-RCG',
                'name' => 'Kopi Gula Aren 20g (Pak Isi 15 Sachet)',
                'weight' => 300,
                'grind' => 'Bubuk Kopi Aren',
                'cost' => 10000,
                'price' => 15000,
                'price_toko' => 14625,
                'price_kafe' => 13500,
                'tier' => ['tier_10_59' => 14625, 'tier_60_99' => 14250, 'tier_100_up' => 13500],
                'stock' => 90,
                'min' => 15,
            ],
        ];

        foreach ($gulaAren20Variants as $v) {
            ProductVariant::updateOrCreate(
                ['sku' => $v['sku']],
                [
                    'product_id' => $prodGulaAren20->id,
                    'variant_name' => $v['name'],
                    'package_weight_grams' => $v['weight'],
                    'grind_form' => $v['grind'],
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
        // Produk 4: Kopi ATAPMERAH 35g
        // ----------------------------------------------------
        $prodAtapmerah35 = Product::updateOrCreate(
            ['slug' => 'kopi-bubuk-atapmerah-35g'],
            [
                'category_id' => $catCampur->id,
                'name' => 'Kopi Bubuk ATAPMERAH 35g — Kopi Campur',
                'brand' => 'ATAPMERAH',
                'coffee_type' => 'Blend',
                'form' => 'ground',
                'net_weight' => '35 gram',
                'producer' => 'PT Kopi Bintang Harapan, Palu, Sulawesi Tengah, Indonesia',
                'origin' => 'Palu, Sulawesi Tengah, Indonesia',
                'composition' => '60% kopi bubuk dan serelia pilihan',
                'serving_suggestion' => 'Masukkan kopi ke dalam cangkir, tambahkan gula atau krimer sesuai selera, tuangkan 200 ml air mendidih, lalu aduk hingga rata.',
                'storage_instruction' => 'Simpan di tempat yang kering dan sejuk.',
                'description' => 'Kopi ATAPMERAH hadir dalam kemasan 35 gram yang praktis dan ekonomis untuk dinikmati sehari-hari. Produk kopi campur ini diproduksi oleh PT Kopi Bintang Harapan di Palu, Sulawesi Tengah, Indonesia.',
                'image' => 'images/products/07_atapmerah_35g_depan_edit.png',
                'gallery' => [],
                'is_active' => true,
            ]
        );

        $atapmerah35Variants = [
            [
                'sku' => 'SKU-AM-35G-BKS',
                'name' => 'Kopi ATAPMERAH 35g (Bungkus)',
                'weight' => 35,
                'grind' => 'Bubuk Halus',
                'cost' => 1200,
                'price' => 2500,
                'price_toko' => 2000,
                'price_kafe' => 1900,
                'stock' => 200,
                'min' => 30,
            ],
            [
                'sku' => 'SKU-AM-35G-PAK10',
                'name' => 'Kopi ATAPMERAH 35g (Pak Isi 10 Bungkus)',
                'weight' => 350,
                'grind' => 'Bubuk Halus',
                'cost' => 13000,
                'price' => 20000,
                'price_toko' => 19500,
                'price_kafe' => 19000,
                'tier' => ['tier_10_59' => 19500, 'tier_60_99' => 19250, 'tier_100_up' => 19000],
                'stock' => 80,
                'min' => 15,
            ],
        ];

        foreach ($atapmerah35Variants as $v) {
            ProductVariant::updateOrCreate(
                ['sku' => $v['sku']],
                [
                    'product_id' => $prodAtapmerah35->id,
                    'variant_name' => $v['name'],
                    'package_weight_grams' => $v['weight'],
                    'grind_form' => $v['grind'],
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

        // 3. Supplier Pemasok Resmi
        Supplier::updateOrCreate(
            ['code' => 'SUP-KBH-01'],
            [
                'name' => 'PT Kopi Bintang Harapan',
                'pic_name' => 'Bintang Harapan Palu',
                'phone' => '081245678901',
                'email' => 'produksi@bintangharapan.co.id',
                'address' => 'Palu, Sulawesi Tengah, Indonesia',
                'is_active' => true,
            ]
        );

        Supplier::updateOrCreate(
            ['code' => 'SUP-BS-02'],
            [
                'name' => 'Bintang Sulawesi',
                'pic_name' => 'Sentra Kopi Sulawesi',
                'phone' => '081398765432',
                'email' => 'distribusi@bintangsulawesi.co.id',
                'address' => 'Palu, Sulawesi Tengah, Indonesia',
                'is_active' => true,
            ]
        );

        // 4. Pelanggan Mitra Grosir B2B, Toko, Kafe & Retail
        $salesUser = User::where('role', User::ROLE_SALES)->first();

        // Akun Login Pengujian Khusus Mitra Kafe
        $kafeUser = User::updateOrCreate(
            ['email' => 'cafe@kopi.com'],
            [
                'name' => 'Mitra Kafe Celebes',
                'phone' => '081388776655',
                'role' => User::ROLE_CUSTOMER,
                'is_active' => true,
                'password' => Hash::make('password123'),
            ]
        );

        Customer::updateOrCreate(
            ['code' => 'CUST-KAFE-001'],
            [
                'customer_type' => Customer::TYPE_KAFE,
                'business_name' => 'Warkop & Kafe Celebes Palu',
                'pic_name' => 'Ahmad Rinaldi',
                'phone' => '081388776655',
                'email' => 'cafe@kopi.com',
                'address' => 'Jl. Sam Ratulangi No. 18, Palu, Sulawesi Tengah',
                'shipping_address' => 'Jl. Sam Ratulangi No. 18, Palu, Sulawesi Tengah',
                'sales_user_id' => $salesUser?->id,
                'user_id' => $kafeUser->id,
                'credit_limit' => 5000000.00,
                'payment_terms_days' => 14,
                'notes' => 'Mitra warkop lokal langganan kopi campur dan robusta.',
                'is_active' => true,
            ]
        );

        // Akun Login Pengujian Khusus Mitra Toko
        $tokoUser = User::updateOrCreate(
            ['email' => 'toko@kopi.com'],
            [
                'name' => 'Mitra Toko Berkah',
                'phone' => '081299887711',
                'role' => User::ROLE_CUSTOMER,
                'is_active' => true,
                'password' => Hash::make('password123'),
            ]
        );

        Customer::updateOrCreate(
            ['code' => 'CUST-TOKO-001'],
            [
                'customer_type' => Customer::TYPE_TOKO,
                'business_name' => 'Toko Kelontong Berkah Palu',
                'pic_name' => 'H. Mansyur',
                'phone' => '081299887711',
                'email' => 'toko@kopi.com',
                'address' => 'Pasar Tradisional Manonda Kios No. 12, Palu',
                'shipping_address' => 'Pasar Tradisional Manonda Kios No. 12, Palu',
                'sales_user_id' => $salesUser?->id,
                'user_id' => $tokoUser->id,
                'credit_limit' => 3000000.00,
                'payment_terms_days' => 7,
                'notes' => 'Distribusi kopi renceng dan kemasan sachet 10g & 35g.',
                'is_active' => true,
            ]
        );

        Customer::updateOrCreate(
            ['code' => 'CUST-RSL-001'],
            [
                'customer_type' => Customer::TYPE_RESELLER,
                'business_name' => 'Reseller Kopi Sulawesi (Ibu Nur)',
                'pic_name' => 'Nurhalizah',
                'phone' => '081344556677',
                'email' => 'nur.reseller@gmail.com',
                'address' => 'Jl. Towua No. 45, Palu, Sulawesi Tengah',
                'shipping_address' => 'Jl. Towua No. 45, Palu, Sulawesi Tengah',
                'sales_user_id' => $salesUser?->id,
                'credit_limit' => 2500000.00,
                'payment_terms_days' => 14,
                'notes' => 'Reseller aktif penjualan online dan toko oleh-oleh khas.',
                'is_active' => true,
            ]
        );

        $retailUser = User::where('email', 'pelanggan@gmail.com')->first();
        Customer::updateOrCreate(
            ['code' => 'CUST-RET-001'],
            [
                'customer_type' => Customer::TYPE_RETAIL,
                'business_name' => 'Pelanggan Retail Contoh',
                'pic_name' => 'Budi Santoso',
                'phone' => '081234567895',
                'email' => 'pelanggan@gmail.com',
                'address' => 'Jl. Melati No. 10, Palu, Sulawesi Tengah',
                'shipping_address' => 'Jl. Melati No. 10, Palu, Sulawesi Tengah',
                'sales_user_id' => null,
                'user_id' => $retailUser?->id,
                'credit_limit' => 0.00,
                'payment_terms_days' => 0,
                'notes' => 'Konsumen retail mandiri belanja online.',
                'is_active' => true,
            ]
        );
    }
}
