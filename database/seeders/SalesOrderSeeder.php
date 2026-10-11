<?php

namespace Database\Seeders;

use App\Models\Customer;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\ProductVariant;
use App\Models\StockMovement;
use App\Models\User;
use App\Services\InventoryService;
use Illuminate\Database\Seeder;

class SalesOrderSeeder extends Seeder
{
    public function run(): void
    {
        $sales = User::where('role', User::ROLE_SALES)->first();
        $admin = User::where('role', User::ROLE_ADMIN)->first();
        $customerUser = User::where('role', User::ROLE_CUSTOMER)->first();

        $kafe = Customer::where('customer_type', Customer::TYPE_KAFE)->first();
        $toko = Customer::where('customer_type', Customer::TYPE_TOKO)->first();

        $v1 = ProductVariant::where('sku', 'like', '%AG-250G%')->first() ?? ProductVariant::first();
        $v2 = ProductVariant::where('sku', 'like', '%RL-250G%')->first() ?? ProductVariant::skip(1)->first() ?? $v1;

        if (!$sales || !$kafe || !$v1) {
            return;
        }

        // Avoid duplicate seeding
        if (Order::where('order_number', 'SO-202610-0001')->exists()) {
            return;
        }

        // 1. Pesanan B2B Dikonfirmasi (SO-202610-0001) - Kafe Senja Nusantara
        $qty1 = 15;
        $price1 = (float)$v1->selling_price;
        $subtotal1 = $qty1 * $price1;
        $discount1 = 25000;
        $shipping1 = 20000;
        $total1 = $subtotal1 - $discount1 + $shipping1;

        $order1 = Order::create([
            'order_number' => 'SO-202610-0001',
            'order_type' => Order::TYPE_B2B,
            'customer_id' => $kafe->id,
            'user_id' => $sales->id,
            'sales_user_id' => $kafe->sales_user_id ?? $sales->id,
            'order_date' => now()->subDays(2)->toDateString(),
            'payment_type' => Order::PAYMENT_CREDIT,
            'due_date' => now()->subDays(2)->addDays($kafe->payment_terms_days)->toDateString(),
            'status' => Order::STATUS_CONFIRMED,
            'payment_status' => Order::PAYMENT_STATUS_UNPAID,
            'subtotal' => $subtotal1,
            'discount' => $discount1,
            'shipping_cost' => $shipping1,
            'total_amount' => $total1,
            'paid_amount' => 0,
            'shipping_address' => $kafe->shipping_address ?? $kafe->address,
            'recipient_name' => $kafe->pic_name,
            'recipient_phone' => $kafe->phone,
            'stock_reduced' => true,
            'confirmed_at' => now()->subDays(2),
            'confirmed_by_user_id' => $admin->id,
            'notes' => 'Pesanan rutin 2 mingguan Kafe Senja.',
        ]);

        $order1->items()->create([
            'product_variant_id' => $v1->id,
            'quantity' => $qty1,
            'unit_price' => $price1,
            'cost_price' => (float)$v1->cost_price,
            'discount_amount' => 0,
            'subtotal' => $subtotal1,
        ]);

        // Audit mutasi stok keluar
        StockMovement::create([
            'product_variant_id' => $v1->id,
            'user_id' => $admin->id,
            'type' => StockMovement::TYPE_OUT,
            'reference_type' => StockMovement::REF_SALES_ORDER,
            'reference_id' => $order1->id,
            'quantity' => -$qty1,
            'stock_before' => $v1->current_stock,
            'stock_after' => max(0, $v1->current_stock - $qty1),
            'notes' => "Penjualan pesanan #SO-202610-0001 kepada {$kafe->business_name}",
            'created_at' => now()->subDays(2),
        ]);
        $v1->update(['current_stock' => max(0, $v1->current_stock - $qty1)]);

        // 2. Pesanan B2B Pending (SO-202610-0002) - Toko Kelontong Berkah Makmur
        if ($toko) {
            $qty2 = 10;
            $price2 = (float)$v2->selling_price;
            $subtotal2 = $qty2 * $price2;
            $total2 = $subtotal2 + 15000;

            $order2 = Order::create([
                'order_number' => 'SO-202610-0002',
                'order_type' => Order::TYPE_B2B,
                'customer_id' => $toko->id,
                'user_id' => $sales->id,
                'sales_user_id' => $toko->sales_user_id ?? $sales->id,
                'order_date' => now()->subDay()->toDateString(),
                'payment_type' => Order::PAYMENT_TRANSFER,
                'due_date' => null,
                'status' => Order::STATUS_PENDING,
                'payment_status' => Order::PAYMENT_STATUS_UNPAID,
                'subtotal' => $subtotal2,
                'discount' => 0,
                'shipping_cost' => 15000,
                'total_amount' => $total2,
                'paid_amount' => 0,
                'shipping_address' => $toko->shipping_address ?? $toko->address,
                'recipient_name' => $toko->pic_name,
                'recipient_phone' => $toko->phone,
                'stock_reduced' => false,
                'notes' => 'Pesanan baru restock etalase toko. Siap untuk dikonfirmasi di demo web.',
            ]);

            $order2->items()->create([
                'product_variant_id' => $v2->id,
                'quantity' => $qty2,
                'unit_price' => $price2,
                'cost_price' => (float)$v2->cost_price,
                'discount_amount' => 0,
                'subtotal' => $subtotal2,
            ]);
        }

        // 3. Pesanan Retail Online (ORD-202610-0001)
        $retailCust = Customer::where('customer_type', Customer::TYPE_RETAIL)->first();
        if ($retailCust) {
            $order3 = Order::create([
                'order_number' => 'ORD-202610-0001',
                'order_type' => Order::TYPE_RETAIL,
                'customer_id' => $retailCust->id,
                'user_id' => $customerUser?->id,
                'sales_user_id' => null,
                'order_date' => now()->toDateString(),
                'payment_type' => Order::PAYMENT_TRANSFER,
                'status' => Order::STATUS_CONFIRMED,
                'payment_status' => Order::PAYMENT_STATUS_UNPAID,
                'subtotal' => 2 * (float)$v1->selling_price,
                'discount' => 0,
                'shipping_cost' => 15000,
                'total_amount' => (2 * (float)$v1->selling_price) + 15000,
                'paid_amount' => 0,
                'shipping_address' => 'Jl. Mawar No. 45, Jakarta Selatan',
                'recipient_name' => 'Andi Pelanggan Retail',
                'recipient_phone' => '081399887766',
                'stock_reduced' => true,
                'confirmed_at' => now(),
                'confirmed_by_user_id' => $customerUser?->id,
                'notes' => 'Pembelian online via website toko.',
            ]);

            $order3->items()->create([
                'product_variant_id' => $v1->id,
                'quantity' => 2,
                'unit_price' => $v1->selling_price,
                'cost_price' => $v1->cost_price,
                'discount_amount' => 0,
                'subtotal' => 2 * (float)$v1->selling_price,
            ]);

            StockMovement::create([
                'product_variant_id' => $v1->id,
                'user_id' => $customerUser?->id ?? $admin->id,
                'type' => StockMovement::TYPE_OUT,
                'reference_type' => StockMovement::REF_SALES_ORDER,
                'reference_id' => $order3->id,
                'quantity' => -2,
                'stock_before' => $v1->current_stock,
                'stock_after' => max(0, $v1->current_stock - 2),
                'notes' => "Penjualan Retail Online #ORD-202610-0001 (Andi Pelanggan Retail)",
                'created_at' => now(),
            ]);
            $v1->update(['current_stock' => max(0, $v1->current_stock - 2)]);
        }
    }
}
