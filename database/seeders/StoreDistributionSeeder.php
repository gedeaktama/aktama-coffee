<?php

namespace Database\Seeders;

use App\Models\Consignment;
use App\Models\ConsignmentAudit;
use App\Models\ConsignmentAuditItem;
use App\Models\ConsignmentItem;
use App\Models\Customer;
use App\Models\Invoice;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\ProductVariant;
use App\Models\StoreVisit;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class StoreDistributionSeeder extends Seeder
{
    public function run(): void
    {
        $salesUser = User::where('role', User::ROLE_SALES)->first() ?? User::first();
        if (!$salesUser) {
            $salesUser = User::create([
                'name' => 'Sales Representative',
                'email' => 'sales@kopi.com',
                'phone' => '081234567892',
                'role' => User::ROLE_SALES,
                'is_active' => true,
                'password' => bcrypt('password123'),
            ]);
        }

        $driverUser = User::where('role', User::ROLE_DRIVER)->first();
        if (!$driverUser) {
            $driverUser = User::create([
                'name' => 'Budi Santoso (Driver Armada 1)',
                'email' => 'driver@kopi.com',
                'phone' => '081234567895',
                'role' => User::ROLE_DRIVER,
                'is_active' => true,
                'password' => bcrypt('password123'),
            ]);
        }

        $variants = ProductVariant::where('is_active', true)->get();

        if ($variants->isEmpty()) {
            return;
        }

        // 1. DATA TOKO RETAIL BERANTAI (ALFAMART, INDOMARET, KELONTONG, KAFE)
        $storesData = [
            [
                'code' => 'CUST-ALFA-001',
                'customer_type' => Customer::TYPE_KONSINYASI,
                'store_chain' => Customer::STORE_CHAIN_ALFAMART,
                'business_name' => 'Alfamart Hayam Wuruk Denpasar',
                'pic_name' => 'Made Hendra (Kepala Toko)',
                'phone' => '081234111222',
                'email' => 'alfa.hayamwuruk@retail.id',
                'address' => 'Jl. Hayam Wuruk No. 88, Denpasar Timur',
                'shipping_address' => 'Jl. Hayam Wuruk No. 88, Denpasar Timur, Bali',
                'latitude' => -8.653400,
                'longitude' => 115.228500,
                'qr_token' => 'QR-ALFA-HW-001',
                'sales_user_id' => $salesUser->id,
                'credit_limit' => 5000000,
                'payment_terms_days' => 14,
                'min_stock_alert' => 10,
                'is_active' => true,
            ],
            [
                'code' => 'CUST-ALFA-002',
                'customer_type' => Customer::TYPE_KONSINYASI,
                'store_chain' => Customer::STORE_CHAIN_ALFAMART,
                'business_name' => 'Alfamart Teuku Umar Barat',
                'pic_name' => 'Ketut Sukarma (Store Manager)',
                'phone' => '081234333444',
                'email' => 'alfa.teukuumar@retail.id',
                'address' => 'Jl. Teuku Umar Barat No. 120, Denpasar Barat',
                'shipping_address' => 'Jl. Teuku Umar Barat No. 120, Denpasar Barat, Bali',
                'latitude' => -8.674500,
                'longitude' => 115.207800,
                'qr_token' => 'QR-ALFA-TU-002',
                'sales_user_id' => $salesUser->id,
                'credit_limit' => 6000000,
                'payment_terms_days' => 14,
                'min_stock_alert' => 8,
                'is_active' => true,
            ],
            [
                'code' => 'CUST-INDO-001',
                'customer_type' => Customer::TYPE_KONSINYASI,
                'store_chain' => Customer::STORE_CHAIN_INDOMARET,
                'business_name' => 'Indomaret Sudirman Central',
                'pic_name' => 'Agus Pratama (Kepala Toko)',
                'phone' => '081234555666',
                'email' => 'indo.sudirman@retail.id',
                'address' => 'Jl. PB Sudirman No. 45, Denpasar',
                'shipping_address' => 'Jl. PB Sudirman No. 45, Denpasar, Bali',
                'latitude' => -8.662100,
                'longitude' => 115.219800,
                'qr_token' => 'QR-INDO-SDR-001',
                'sales_user_id' => $salesUser->id,
                'credit_limit' => 8000000,
                'payment_terms_days' => 14,
                'min_stock_alert' => 12,
                'is_active' => true,
            ],
            [
                'code' => 'CUST-INDO-002',
                'customer_type' => Customer::TYPE_KONSINYASI,
                'store_chain' => Customer::STORE_CHAIN_INDOMARET,
                'business_name' => 'Indomaret Gatot Subroto Timur',
                'pic_name' => 'Wayan Juniarta (Supervisor)',
                'phone' => '081234777888',
                'email' => 'indo.gatsu@retail.id',
                'address' => 'Jl. Gatot Subroto Timur No. 210, Denpasar',
                'shipping_address' => 'Jl. Gatot Subroto Timur No. 210, Denpasar, Bali',
                'latitude' => -8.638900,
                'longitude' => 115.215400,
                'qr_token' => 'QR-INDO-GTS-002',
                'sales_user_id' => $salesUser->id,
                'credit_limit' => 5000000,
                'payment_terms_days' => 14,
                'min_stock_alert' => 6,
                'is_active' => true,
            ],
            [
                'code' => 'CUST-TOKO-001',
                'customer_type' => Customer::TYPE_TOKO,
                'store_chain' => Customer::STORE_CHAIN_KELONTONG,
                'business_name' => 'Toko Kelontong Berkah Jaya',
                'pic_name' => 'Ibu Rahayu',
                'phone' => '081234999000',
                'email' => 'toko.berkahjaya@gmail.com',
                'address' => 'Jl. Tukad Yeh Aya No. 34, Panjer, Denpasar',
                'shipping_address' => 'Jl. Tukad Yeh Aya No. 34, Panjer, Denpasar',
                'latitude' => -8.679200,
                'longitude' => 115.234100,
                'qr_token' => 'QR-TOKO-BRK-001',
                'sales_user_id' => $salesUser->id,
                'credit_limit' => 3000000,
                'payment_terms_days' => 7,
                'min_stock_alert' => 4,
                'is_active' => true,
            ],
            [
                'code' => 'CUST-KAFE-001',
                'customer_type' => Customer::TYPE_KAFE,
                'store_chain' => Customer::STORE_CHAIN_KAFE,
                'business_name' => 'Kafe Kopi Senja Sunset',
                'pic_name' => 'Bli Arya (Owner Kafe)',
                'phone' => '081234222333',
                'email' => 'kopisenja.bali@gmail.com',
                'address' => 'Jl. Sunset Road No. 89, Kuta, Badung',
                'shipping_address' => 'Jl. Sunset Road No. 89, Kuta, Badung, Bali',
                'latitude' => -8.705200,
                'longitude' => 115.178900,
                'qr_token' => 'QR-KAFE-SNJ-001',
                'sales_user_id' => $salesUser->id,
                'credit_limit' => 7000000,
                'payment_terms_days' => 30,
                'min_stock_alert' => 5,
                'is_active' => true,
            ],
        ];

        $createdStores = [];
        foreach ($storesData as $storeInfo) {
            $createdStores[] = Customer::updateOrCreate(
                ['code' => $storeInfo['code']],
                $storeInfo
            );
        }

        // 2. STOK KONSINYASI AKTIF PADA MASING-MASING TOKO
        foreach ($createdStores as $index => $store) {
            $consignment = Consignment::firstOrCreate(
                [
                    'customer_id' => $store->id,
                    'consignment_number' => 'KS-INIT-' . str_pad($store->id, 4, '0', STR_PAD_LEFT),
                ],
                [
                    'sales_user_id' => $salesUser->id,
                    'created_by_user_id' => $salesUser->id,
                    'delivery_date' => Carbon::now()->subDays(10),
                    'status' => Consignment::STATUS_ACTIVE,
                    'total_items_count' => 0,
                    'notes' => 'Penempatan rak display awal kopi kemasan ' . $store->business_name,
                ]
            );

            $totalItems = 0;
            foreach ($variants as $varIndex => $variant) {
                // Beri stok variasi antara 10 sampai 35 pack per varian
                $initQty = 15 + (($index + $varIndex) * 5) % 25;
                $soldQty = max(2, (int)($initQty * 0.35)); // 35% sudah laku
                $currQty = $initQty - $soldQty;

                ConsignmentItem::updateOrCreate(
                    [
                        'consignment_id' => $consignment->id,
                        'product_variant_id' => $variant->id,
                    ],
                    [
                        'initial_quantity' => $initQty,
                        'current_quantity' => $currQty,
                        'sold_quantity' => $soldQty,
                        'returned_quantity' => 0,
                        'selling_price' => $variant->selling_price,
                        'notes' => 'Display Rak Utama',
                    ]
                );
                $totalItems += $currQty;
            }

            $consignment->update(['total_items_count' => $totalItems]);
        }

        // 3. SEED STORE VISITS (SIMULASI KUNJUNGAN SOPIR HARI INI)
        $today = Carbon::today();
        
        // Toko 1: Alfamart Hayam Wuruk (Selesai Kunjungan jam 09:15)
        $store1 = $createdStores[0];
        StoreVisit::updateOrCreate(
            ['visit_number' => 'SV-' . $today->format('Ymd') . '-0001'],
            [
                'user_id' => $driverUser->id,
                'customer_id' => $store1->id,
                'visit_date' => $today->toDateString(),
                'check_in_at' => $today->copy()->setTime(9, 15, 0),
                'check_out_at' => $today->copy()->setTime(9, 45, 0),
                'check_in_lat' => -8.653412,
                'check_in_lng' => 115.228518,
                'distance_meters' => 18,
                'is_geofence_valid' => true,
                'visit_purpose' => StoreVisit::PURPOSE_AUDIT_DELIVERY,
                'status' => StoreVisit::STATUS_COMPLETED,
                'recipient_name' => 'Made Hendra',
                'recipient_phone' => '081234111222',
                'summary_sold_qty' => 8,
                'summary_sold_amount' => 85000,
                'summary_restock_qty' => 10,
                'notes' => 'Display rak tertata rapi di dekat kasir. 8 pack laku terjual. Tambah restock 10 pack baru.',
            ]
        );

        // Toko 2: Indomaret Sudirman Central (Selesai Kunjungan jam 10:30)
        $store3 = $createdStores[2];
        StoreVisit::updateOrCreate(
            ['visit_number' => 'SV-' . $today->format('Ymd') . '-0002'],
            [
                'user_id' => $driverUser->id,
                'customer_id' => $store3->id,
                'visit_date' => $today->toDateString(),
                'check_in_at' => $today->copy()->setTime(10, 30, 0),
                'check_out_at' => $today->copy()->setTime(11, 0, 0),
                'check_in_lat' => -8.662130,
                'check_in_lng' => 115.219840,
                'distance_meters' => 22,
                'is_geofence_valid' => true,
                'visit_purpose' => StoreVisit::PURPOSE_AUDIT_DELIVERY,
                'status' => StoreVisit::STATUS_COMPLETED,
                'recipient_name' => 'Agus Pratama',
                'recipient_phone' => '081234555666',
                'summary_sold_qty' => 12,
                'summary_sold_amount' => 142000,
                'summary_restock_qty' => 15,
                'notes' => 'Kopi Atapmerah sangat laku. Laku 12 pack. Restock 15 pack.',
            ]
        );

        // Toko 3: Alfamart Teuku Umar (Sedang Dikunjungi Sekarang / In Progress)
        $store2 = $createdStores[1];
        StoreVisit::updateOrCreate(
            ['visit_number' => 'SV-' . $today->format('Ymd') . '-0003'],
            [
                'user_id' => $driverUser->id,
                'customer_id' => $store2->id,
                'visit_date' => $today->toDateString(),
                'check_in_at' => Carbon::now()->subMinutes(15),
                'check_out_at' => null,
                'check_in_lat' => -8.674520,
                'check_in_lng' => 115.207815,
                'distance_meters' => 12,
                'is_geofence_valid' => true,
                'visit_purpose' => StoreVisit::PURPOSE_AUDIT,
                'status' => StoreVisit::STATUS_IN_PROGRESS,
                'recipient_name' => 'Ketut Sukarma',
                'recipient_phone' => '081234333444',
                'notes' => 'Sopir sedang menghitung stok fisik di rak kopi.',
            ]
        );

        // 4. SIMULASI TOKO MENUNGGAK (FAKTUR JATUH TEMPO) UNTUK ALERT OVERDUE
        // Kafe Kopi Senja memiliki 1 pesanan tempo yang sudah melewati jatuh tempo 12 hari
        $kafeCustomer = $createdStores[5];
        $overdueOrder = Order::firstOrCreate(
            ['order_number' => 'ORD-TEMPO-OVERDUE-01'],
            [
                'customer_id' => $kafeCustomer->id,
                'user_id' => $salesUser->id,
                'order_date' => Carbon::now()->subDays(35),
                'order_type' => Order::TYPE_B2B,
                'payment_type' => Order::PAYMENT_CREDIT,
                'payment_status' => Order::PAYMENT_STATUS_UNPAID,
                'status' => Order::STATUS_CONFIRMED,
                'subtotal' => 1850000,
                'discount' => 0,
                'shipping_cost' => 0,
                'total_amount' => 1850000,
                'paid_amount' => 0,
                'shipping_address' => $kafeCustomer->shipping_address,
                'notes' => 'Pesanan B2B Grosir Kopi Kafe - Tempo 14 Hari',
            ]
        );

        Invoice::firstOrCreate(
            ['invoice_number' => 'INV-OVERDUE-001'],
            [
                'order_id' => $overdueOrder->id,
                'customer_id' => $kafeCustomer->id,
                'user_id' => $salesUser->id,
                'invoice_date' => Carbon::now()->subDays(35),
                'due_date' => Carbon::now()->subDays(12), // LEWAT JATUH TEMPO 12 HARI!
                'total_amount' => 1850000,
                'paid_amount' => 0,
                'remaining_amount' => 1850000,
                'status' => Invoice::STATUS_UNPAID,
                'notes' => 'Menunggak 12 hari. Perlu follow up segera oleh sales.',
            ]
        );
    }
}
