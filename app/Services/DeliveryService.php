<?php

namespace App\Services;

use App\Models\DeliveryOrder;
use App\Models\DeliveryOrderItem;
use App\Models\Order;
use Carbon\Carbon;
use Exception;
use Illuminate\Support\Facades\DB;

class DeliveryService
{
    /**
     * Terbitkan Surat Jalan (Delivery Order) untuk Pesanan Penjualan
     */
    public function createDeliveryOrder(Order $order, array $data, int $userId): DeliveryOrder
    {
        if ($order->isPending() || $order->isCancelled()) {
            throw new Exception("Surat Jalan hanya dapat diterbitkan untuk pesanan yang telah dikonfirmasi.");
        }

        return DB::transaction(function () use ($order, $data, $userId) {
            // Generate Nomor Surat Jalan: SJ-YYYYMM-0001
            $prefix = 'SJ-' . date('Ym') . '-';
            $latest = DeliveryOrder::where('delivery_number', 'like', "{$prefix}%")->latest('id')->first();
            $nextNumber = 1;
            if ($latest) {
                $nextNumber = (int)substr($latest->delivery_number, -4) + 1;
            }
            $deliveryNumber = $prefix . str_pad($nextNumber, 4, '0', STR_PAD_LEFT);

            $initialStatus = $data['status'] ?? DeliveryOrder::STATUS_PROCESSING;

            $deliveryOrder = DeliveryOrder::create([
                'delivery_number' => $deliveryNumber,
                'order_id' => $order->id,
                'customer_id' => $order->customer_id,
                'user_id' => $userId,
                'delivery_date' => $data['delivery_date'] ?? now()->toDateString(),
                'courier_type' => $data['courier_type'] ?? DeliveryOrder::COURIER_INTERNAL,
                'courier_name' => $data['courier_name'] ?? null,
                'driver_name' => $data['driver_name'] ?? null,
                'driver_phone' => $data['driver_phone'] ?? null,
                'vehicle_plate_number' => $data['vehicle_plate_number'] ?? null,
                'tracking_number' => $data['tracking_number'] ?? null,
                'shipping_address' => $data['shipping_address'] ?? $order->shipping_address ?? ($order->customer->address ?? '-'),
                'recipient_name' => $data['recipient_name'] ?? $order->recipient_name ?? ($order->customer->pic_name ?? '-'),
                'recipient_phone' => $data['recipient_phone'] ?? $order->recipient_phone ?? ($order->customer->phone ?? '-'),
                'status' => $initialStatus,
                'notes' => $data['notes'] ?? null,
            ]);

            // Salin seluruh item pesanan ke item surat jalan
            foreach ($order->items as $item) {
                DeliveryOrderItem::create([
                    'delivery_order_id' => $deliveryOrder->id,
                    'order_item_id' => $item->id,
                    'product_variant_id' => $item->product_variant_id,
                    'quantity' => $item->quantity,
                    'package_info' => $item->variant ? ($item->quantity . ' Pack ' . $item->variant->package_weight_grams . 'g') : null,
                ]);
            }

            // Sinkronkan status pada Order
            if ($initialStatus === DeliveryOrder::STATUS_SHIPPED) {
                $order->update(['status' => Order::STATUS_SHIPPED]);
            } else {
                $order->update(['status' => Order::STATUS_PROCESSING]);
            }

            return $deliveryOrder;
        });
    }

    /**
     * Konfirmasi barang telah dibawa sopir / diserahkan ke kurir (Shipped)
     */
    public function markAsShipped(
        DeliveryOrder $deliveryOrder,
        ?string $trackingNumber = null,
        ?string $driverName = null,
        ?string $notes = null
    ): DeliveryOrder {
        return DB::transaction(function () use ($deliveryOrder, $trackingNumber, $driverName, $notes) {
            $updateData = ['status' => DeliveryOrder::STATUS_SHIPPED];

            if ($trackingNumber) {
                $updateData['tracking_number'] = $trackingNumber;
            }
            if ($driverName) {
                $updateData['driver_name'] = $driverName;
            }
            if ($notes) {
                $updateData['notes'] = $notes;
            }

            $deliveryOrder->update($updateData);
            $deliveryOrder->order->update(['status' => Order::STATUS_SHIPPED]);

            return $deliveryOrder;
        });
    }

    /**
     * Konfirmasi barang telah sampai dan diterima fisik di alamat tujuan (Delivered)
     */
    public function markAsDelivered(
        DeliveryOrder $deliveryOrder,
        string $receivedBy,
        ?string $receivedAt = null,
        ?string $notes = null
    ): DeliveryOrder {
        return DB::transaction(function () use ($deliveryOrder, $receivedBy, $receivedAt, $notes) {
            $deliveryOrder->update([
                'status' => DeliveryOrder::STATUS_DELIVERED,
                'received_by' => $receivedBy,
                'received_at' => $receivedAt ? Carbon::parse($receivedAt) : now(),
                'notes' => $notes ?? $deliveryOrder->notes,
            ]);

            $deliveryOrder->order->update(['status' => Order::STATUS_DELIVERED]);

            return $deliveryOrder;
        });
    }

    /**
     * Batalkan surat jalan
     */
    public function cancelDeliveryOrder(DeliveryOrder $deliveryOrder): DeliveryOrder
    {
        return DB::transaction(function () use ($deliveryOrder) {
            $deliveryOrder->update(['status' => DeliveryOrder::STATUS_CANCELLED]);

            // Jika tidak ada surat jalan aktif lainnya, kembalikan order ke confirmed
            $hasActive = DeliveryOrder::where('order_id', $deliveryOrder->order_id)
                ->whereIn('status', [DeliveryOrder::STATUS_PROCESSING, DeliveryOrder::STATUS_SHIPPED, DeliveryOrder::STATUS_DELIVERED])
                ->exists();

            if (!$hasActive) {
                $deliveryOrder->order->update(['status' => Order::STATUS_CONFIRMED]);
            }

            return $deliveryOrder;
        });
    }
}
