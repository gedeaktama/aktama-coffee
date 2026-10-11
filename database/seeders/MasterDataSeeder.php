<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Customer;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\Supplier;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class MasterDataSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 1. Kategori Kopi
        $catArabika = Category::updateOrCreate(
            ['slug' => 'kopi-arabika-single-origin'],
            [
                'name' => 'Kopi Arabika Single Origin',
                'description' => 'Kopi Arabika pilihan dengan profil rasa unik dari berbagai perkebunan Nusantara.',
                'is_active' => true,
            ]
        );

        $catRobusta = Category::updateOrCreate(
            ['slug' => 'kopi-robusta-premium'],
            [
                'name' => 'Kopi Robusta Premium',
                'description' => 'Kopi Robusta berkualitas dengan rasa tebal (bold) dan crema melimpah.',
                'is_active' => true,
            ]
        );

        $catBlend = Category::updateOrCreate(
            ['slug' => 'kopi-house-blend'],
            [
                'name' => 'Kopi House Blend & Espresso',
                'description' => 'Campuran seimbang Arabika & Robusta untuk kebutuhan kafe dan mesin espresso.',
                'is_active' => true,
            ]
        );

        // 2. Produk & Varian (Satu harga jual standar per SKU)
        // Produk A: Arabika Gayo
        $prodGayo = Product::updateOrCreate(
            ['slug' => 'kopi-arabika-aceh-gayo'],
            [
                'category_id' => $catArabika->id,
                'name' => 'Kopi Arabika Aceh Gayo',
                'brand' => 'Kopi Distro Premium',
                'coffee_type' => 'Arabika',
                'form' => 'both',
                'description' => 'Kopi Arabika dataran tinggi Gayo Aceh dengan keasaman segar berpadu aroma rempah dan cokelat.',
                'is_active' => true,
            ]
        );

        $gayoVariants = [
            ['sku' => 'SKU-AG-100G', 'name' => 'Arabika Aceh Gayo 100g (Biji/Bubuk)', 'weight' => 100, 'cost' => 28000, 'price' => 40000, 'stock' => 60, 'min' => 10],
            ['sku' => 'SKU-AG-250G', 'name' => 'Arabika Aceh Gayo 250g (Biji/Bubuk)', 'weight' => 250, 'cost' => 65000, 'price' => 90000, 'stock' => 85, 'min' => 15],
            ['sku' => 'SKU-AG-500G', 'name' => 'Arabika Aceh Gayo 500g (Biji/Bubuk)', 'weight' => 500, 'cost' => 120000, 'price' => 170000, 'stock' => 45, 'min' => 10],
            ['sku' => 'SKU-AG-1KG',  'name' => 'Arabika Aceh Gayo 1kg (Biji/Bubuk)',  'weight' => 1000, 'cost' => 220000, 'price' => 310000, 'stock' => 30, 'min' => 5],
        ];

        foreach ($gayoVariants as $v) {
            ProductVariant::updateOrCreate(
                ['sku' => $v['sku']],
                [
                    'product_id' => $prodGayo->id,
                    'variant_name' => $v['name'],
                    'package_weight_grams' => $v['weight'],
                    'grind_form' => 'Biji Kopi',
                    'cost_price' => $v['cost'],
                    'selling_price' => $v['price'],
                    'current_stock' => $v['stock'],
                    'minimum_stock' => $v['min'],
                    'is_active' => true,
                ]
            );
        }

        // Produk B: Arabika Toraja
        $prodToraja = Product::updateOrCreate(
            ['slug' => 'kopi-arabika-toraja-sapan'],
            [
                'category_id' => $catArabika->id,
                'name' => 'Kopi Arabika Toraja Sapan',
                'brand' => 'Kopi Distro Premium',
                'coffee_type' => 'Arabika',
                'form' => 'both',
                'description' => 'Kopi Arabika khas Sulawesi dengan body seimbang dan hint rasa herbal manis.',
                'is_active' => true,
            ]
        );

        $torajaVariants = [
            ['sku' => 'SKU-AT-250G', 'name' => 'Arabika Toraja Sapan 250g', 'weight' => 250, 'cost' => 70000, 'price' => 95000, 'stock' => 50, 'min' => 10],
            ['sku' => 'SKU-AT-500G', 'name' => 'Arabika Toraja Sapan 500g', 'weight' => 500, 'cost' => 130000, 'price' => 180000, 'stock' => 35, 'min' => 10],
            ['sku' => 'SKU-AT-1KG',  'name' => 'Arabika Toraja Sapan 1kg',  'weight' => 1000, 'cost' => 240000, 'price' => 330000, 'stock' => 25, 'min' => 5],
        ];

        foreach ($torajaVariants as $v) {
            ProductVariant::updateOrCreate(
                ['sku' => $v['sku']],
                [
                    'product_id' => $prodToraja->id,
                    'variant_name' => $v['name'],
                    'package_weight_grams' => $v['weight'],
                    'grind_form' => 'Biji Kopi',
                    'cost_price' => $v['cost'],
                    'selling_price' => $v['price'],
                    'current_stock' => $v['stock'],
                    'minimum_stock' => $v['min'],
                    'is_active' => true,
                ]
            );
        }

        // Produk C: Robusta Lampung
        $prodLampung = Product::updateOrCreate(
            ['slug' => 'kopi-robusta-lampung-gold'],
            [
                'category_id' => $catRobusta->id,
                'name' => 'Kopi Robusta Lampung Gold',
                'brand' => 'Kopi Distro Heritage',
                'coffee_type' => 'Robusta',
                'form' => 'both',
                'description' => 'Robusta pilihan petani Lampung dengan kepahitan nikmat, body tebal, dan aroma cokelat gelap.',
                'is_active' => true,
            ]
        );

        $lampungVariants = [
            ['sku' => 'SKU-RL-250G', 'name' => 'Robusta Lampung Gold 250g', 'weight' => 250, 'cost' => 32000, 'price' => 48000, 'stock' => 120, 'min' => 20],
            ['sku' => 'SKU-RL-500G', 'name' => 'Robusta Lampung Gold 500g', 'weight' => 500, 'cost' => 60000, 'price' => 88000, 'stock' => 90, 'min' => 20],
            ['sku' => 'SKU-RL-1KG',  'name' => 'Robusta Lampung Gold 1kg',  'weight' => 1000, 'cost' => 110000, 'price' => 160000, 'stock' => 60, 'min' => 10],
        ];

        foreach ($lampungVariants as $v) {
            ProductVariant::updateOrCreate(
                ['sku' => $v['sku']],
                [
                    'product_id' => $prodLampung->id,
                    'variant_name' => $v['name'],
                    'package_weight_grams' => $v['weight'],
                    'grind_form' => 'Bubuk Halus',
                    'cost_price' => $v['cost'],
                    'selling_price' => $v['price'],
                    'current_stock' => $v['stock'],
                    'minimum_stock' => $v['min'],
                    'is_active' => true,
                ]
            );
        }

        // Produk D: Arabika Flores Bajawa Honey
        $prodFlores = Product::updateOrCreate(
            ['slug' => 'kopi-arabika-flores-bajawa-honey'],
            [
                'category_id' => $catArabika->id,
                'name' => 'Kopi Arabika Flores Bajawa Honey',
                'brand' => 'Kopi Distro Specialty',
                'coffee_type' => 'Arabika',
                'form' => 'both',
                'description' => 'Kopi Arabika proses honey dari dataran tinggi Bajawa Flores dengan aroma floral semerbak dan rasa manis madu alami yang lembut.',
                'is_active' => true,
            ]
        );

        $floresVariants = [
            ['sku' => 'SKU-FB-250G', 'name' => 'Arabika Flores Bajawa Honey 250g', 'weight' => 250, 'cost' => 68000, 'price' => 92000, 'stock' => 70, 'min' => 15],
            ['sku' => 'SKU-FB-500G', 'name' => 'Arabika Flores Bajawa Honey 500g', 'weight' => 500, 'cost' => 125000, 'price' => 175000, 'stock' => 40, 'min' => 10],
            ['sku' => 'SKU-FB-1KG',  'name' => 'Arabika Flores Bajawa Honey 1kg',  'weight' => 1000, 'cost' => 235000, 'price' => 320000, 'stock' => 20, 'min' => 5],
        ];

        foreach ($floresVariants as $v) {
            ProductVariant::updateOrCreate(
                ['sku' => $v['sku']],
                [
                    'product_id' => $prodFlores->id,
                    'variant_name' => $v['name'],
                    'package_weight_grams' => $v['weight'],
                    'grind_form' => 'Biji Kopi',
                    'cost_price' => $v['cost'],
                    'selling_price' => $v['price'],
                    'current_stock' => $v['stock'],
                    'minimum_stock' => $v['min'],
                    'is_active' => true,
                ]
            );
        }

        // 3. Supplier Pemasok
        Supplier::updateOrCreate(
            ['code' => 'SUP-001'],
            [
                'name' => 'PT Sumatra Mandiri Kopi',
                'pic_name' => 'Hendra Wijaya',
                'phone' => '081188223344',
                'email' => 'supplier.sumatra@kopimandiri.co.id',
                'address' => 'Jl. Pelabuhan Kopi No. 12, Medan, Sumatera Utara',
                'is_active' => true,
            ]
        );

        Supplier::updateOrCreate(
            ['code' => 'SUP-002'],
            [
                'name' => 'CV Java Prima Rostery',
                'pic_name' => 'Slamet Riyadi',
                'phone' => '081299334455',
                'email' => 'javaprima@rostery.id',
                'address' => 'Kawasan Agro Kopi No. 8, Temanggung, Jawa Tengah',
                'is_active' => true,
            ]
        );

        // 4. Pelanggan Grosir B2B (Kafe, Toko Kelontong, Reseller)
        $salesUser = User::where('role', User::ROLE_SALES)->first();

        Customer::updateOrCreate(
            ['code' => 'CUST-KAFE-001'],
            [
                'customer_type' => Customer::TYPE_KAFE,
                'business_name' => 'Kafe Senja Nusantara',
                'pic_name' => 'Rian Hidayat',
                'phone' => '081388776655',
                'email' => 'senja.kafe@gmail.com',
                'address' => 'Jl. Merdeka No. 45, Jakarta Selatan',
                'shipping_address' => 'Jl. Merdeka No. 45, Jakarta Selatan (Gudang Belakang)',
                'sales_user_id' => $salesUser?->id,
                'credit_limit' => 5000000.00, // Plafon kredit Rp 5.000.000
                'payment_terms_days' => 14, // Tempo 14 hari
                'notes' => 'Langganan kopi espresso blend rutin 2 minggu sekali.',
                'is_active' => true,
            ]
        );

        Customer::updateOrCreate(
            ['code' => 'CUST-TOKO-001'],
            [
                'customer_type' => Customer::TYPE_TOKO,
                'business_name' => 'Toko Kelontong Berkah Makmur',
                'pic_name' => 'H. Syamsul Arifin',
                'phone' => '081299887711',
                'email' => 'tokoberkah@yahoo.com',
                'address' => 'Pasar Induk Kios No. B-12, Bekasi',
                'shipping_address' => 'Pasar Induk Kios No. B-12, Bekasi',
                'sales_user_id' => $salesUser?->id,
                'credit_limit' => 3000000.00, // Plafon kredit Rp 3.000.000
                'payment_terms_days' => 7, // Tempo 7 hari
                'notes' => 'Fokus penjualan kemasan ekonomis 100g dan 250g.',
                'is_active' => true,
            ]
        );

        Customer::updateOrCreate(
            ['code' => 'CUST-RSL-001'],
            [
                'customer_type' => Customer::TYPE_RESELLER,
                'business_name' => 'Reseller Kopi Mantap (Dewi)',
                'pic_name' => 'Dewi Anggraeni',
                'phone' => '081344556677',
                'email' => 'dewi.kopi@reseller.com',
                'address' => 'Komplek Griya Indah Blok C-5, Tangerang',
                'shipping_address' => 'Komplek Griya Indah Blok C-5, Tangerang',
                'sales_user_id' => $salesUser?->id,
                'credit_limit' => 2500000.00, // Plafon kredit Rp 2.500.000
                'payment_terms_days' => 14, // Tempo 14 hari
                'notes' => 'Reseller aktif penjualan online & komunitas kopi.',
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
                'address' => 'Jl. Melati No. 10, Jakarta Barat',
                'shipping_address' => 'Jl. Melati No. 10, Jakarta Barat',
                'sales_user_id' => null,
                'user_id' => $retailUser?->id,
                'credit_limit' => 0.00, // Retail: Tanpa tempo (Tunai/Transfer)
                'payment_terms_days' => 0,
                'notes' => 'Konsumen retail mandiri.',
                'is_active' => true,
            ]
        );
    }
}
