<?php

namespace Database\Seeders;

use App\Models\DeliveryOrder;
use App\Models\DeliveryOrderItem;
use App\Models\Order;
use App\Models\User;
use Illuminate\Database\Seeder;

class DeliverySeeder extends Seeder
{
    public function run(): void
    {
        $warehouse = User::where('role', User::ROLE_WAREHOUSE)->first() ?? User::where('role', User::ROLE_ADMIN)->first();

        // 1. Surat Jalan untuk Order B2B Kafe Senja (SO-202610-0001) - Status Selesai Diterima (Delivered)
        $order1 = Order::where('order_number', 'SO-202610-0001')->first();
        if ($order1 && !DeliveryOrder::where('order_id', $order1->id)->exists()) {
            $sj1 = DeliveryOrder::create([
                'delivery_number' => 'SJ-' . date('Ym') . '-0001',
                'order_id' => $order1->id,
                'customer_id' => $order1->customer_id,
                'user_id' => $warehouse->id,
                'delivery_date' => now()->subDay()->toDateString(),
                'courier_type' => DeliveryOrder::COURIER_INTERNAL,
                'driver_name' => 'Bambang Supriyadi (Driver Toko)',
                'driver_phone' => '0813-8899-7711',
                'vehicle_plate_number' => 'B 9128 KD',
                'shipping_address' => $order1->shipping_address ?? ($order1->customer->address ?? 'Jl. Senja Raya No. 45, Jakarta Selatan'),
                'recipient_name' => $order1->recipient_name ?? ($order1->customer->pic_name ?? 'Barista Senja'),
                'recipient_phone' => $order1->recipient_phone ?? ($order1->customer->phone ?? '0812-3456-7890'),
                'status' => DeliveryOrder::STATUS_DELIVERED,
                'received_by' => 'Rian (Head Barista Kafe Senja)',
                'received_at' => now()->subDay()->setTime(14, 30),
                'notes' => 'Barang diterima lengkap dan segel kardus utuh.',
            ]);

            foreach ($order1->items as $item) {
                DeliveryOrderItem::create([
                    'delivery_order_id' => $sj1->id,
                    'order_item_id' => $item->id,
                    'product_variant_id' => $item->product_variant_id,
                    'quantity' => $item->quantity,
                    'package_info' => $item->quantity . ' Pack (' . ($item->variant->package_weight_grams ?? 250) . 'g)',
                ]);
            }

            $order1->update(['status' => Order::STATUS_DELIVERED]);
        }

        // 2. Surat Jalan untuk Order Retail Online (ORD-202610-0001) - Status Dalam Perjalanan (Shipped)
        $orderRetail = Order::where('order_number', 'ORD-202610-0001')->first();
        if ($orderRetail && !DeliveryOrder::where('order_id', $orderRetail->id)->exists()) {
            $sj2 = DeliveryOrder::create([
                'delivery_number' => 'SJ-' . date('Ym') . '-0002',
                'order_id' => $orderRetail->id,
                'customer_id' => $orderRetail->customer_id,
                'user_id' => $warehouse->id,
                'delivery_date' => now()->toDateString(),
                'courier_type' => DeliveryOrder::COURIER_EXPEDITION,
                'courier_name' => 'SiCepat Ekspres (Best Service)',
                'driver_name' => 'Kurir SiCepat Hub Kebayoran',
                'driver_phone' => '0812-9988-1122',
                'tracking_number' => '004189281729',
                'shipping_address' => $orderRetail->shipping_address ?? 'Jl. Mawar No. 10, Jakarta Selatan',
                'recipient_name' => $orderRetail->recipient_name ?? 'Pelanggan Ritel',
                'recipient_phone' => $orderRetail->recipient_phone ?? '0812-0000-1111',
                'status' => DeliveryOrder::STATUS_SHIPPED,
                'notes' => 'Paket kopi kemasan rapuh, jangan ditindih beban berat.',
            ]);

            foreach ($orderRetail->items as $item) {
                DeliveryOrderItem::create([
                    'delivery_order_id' => $sj2->id,
                    'order_item_id' => $item->id,
                    'product_variant_id' => $item->product_variant_id,
                    'quantity' => $item->quantity,
                    'package_info' => $item->quantity . ' Pack Kemasan Segel',
                ]);
            }

            $orderRetail->update(['status' => Order::STATUS_SHIPPED]);
        }
    }
}
