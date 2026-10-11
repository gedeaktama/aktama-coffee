<?php

namespace Database\Seeders;

use App\Models\ProductVariant;
use App\Models\Purchase;
use App\Models\StockMovement;
use App\Models\Supplier;
use App\Models\User;
use App\Services\InventoryService;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class InventorySeeder extends Seeder
{
    public function run(): void
    {
        $admin = User::where('role', User::ROLE_ADMIN)->first();
        $warehouse = User::where('role', User::ROLE_WAREHOUSE)->first() ?? $admin;
        $supplierGayo = Supplier::where('name', 'like', '%Gayo%')->first() ?? Supplier::first();
        $supplierJava = Supplier::where('name', 'like', '%Java%')->first() ?? Supplier::skip(1)->first() ?? $supplierGayo;

        if (!$supplierGayo || !$admin) {
            return;
        }

        $variants = ProductVariant::take(4)->get();
        if ($variants->count() < 2) {
            return;
        }

        // Prevent duplicate seeding
        if (Purchase::where('purchase_number', 'PO-202610-0001')->exists()) {
            return;
        }

        // 1. PO Selesai Diterima (Received) - PO-202610-0001
        $v1 = $variants[0];
        $v2 = $variants[1];

        $subtotal1 = (50 * (float)$v1->cost_price) + (30 * (float)$v2->cost_price);
        $discount1 = 50000;
        $shipping1 = 75000;
        $total1 = $subtotal1 - $discount1 + $shipping1;

        $po1 = Purchase::create([
            'purchase_number' => 'PO-202610-0001',
            'supplier_id' => $supplierGayo->id,
            'user_id' => $admin->id,
            'purchase_date' => now()->subDays(3)->toDateString(),
            'status' => Purchase::STATUS_RECEIVED,
            'subtotal' => $subtotal1,
            'discount' => $discount1,
            'shipping_cost' => $shipping1,
            'total_amount' => $total1,
            'received_at' => now()->subDays(2),
            'received_by_user_id' => $warehouse->id,
            'notes' => 'Pengiriman perdana musim panen kopi Gayo via kargo darat.',
        ]);

        $po1->items()->createMany([
            [
                'product_variant_id' => $v1->id,
                'quantity' => 50,
                'cost_price' => $v1->cost_price,
                'subtotal' => 50 * (float)$v1->cost_price,
            ],
            [
                'product_variant_id' => $v2->id,
                'quantity' => 30,
                'cost_price' => $v2->cost_price,
                'subtotal' => 30 * (float)$v2->cost_price,
            ],
        ]);

        // Audit movements for PO1
        StockMovement::create([
            'product_variant_id' => $v1->id,
            'user_id' => $warehouse->id,
            'type' => StockMovement::TYPE_IN,
            'reference_type' => StockMovement::REF_PURCHASE,
            'reference_id' => $po1->id,
            'quantity' => 50,
            'stock_before' => $v1->current_stock,
            'stock_after' => $v1->current_stock + 50,
            'notes' => "Penerimaan PO #PO-202610-0001 dari Supplier {$supplierGayo->name}",
            'created_at' => now()->subDays(2),
        ]);
        $v1->update(['current_stock' => $v1->current_stock + 50]);

        StockMovement::create([
            'product_variant_id' => $v2->id,
            'user_id' => $warehouse->id,
            'type' => StockMovement::TYPE_IN,
            'reference_type' => StockMovement::REF_PURCHASE,
            'reference_id' => $po1->id,
            'quantity' => 30,
            'stock_before' => $v2->current_stock,
            'stock_after' => $v2->current_stock + 30,
            'notes' => "Penerimaan PO #PO-202610-0001 dari Supplier {$supplierGayo->name}",
            'created_at' => now()->subDays(2),
        ]);
        $v2->update(['current_stock' => $v2->current_stock + 30]);

        // 2. PO Menunggu Kedatangan Fisik (Ordered) - PO-202610-0002
        $v3 = $variants[2] ?? $variants[0];
        $v4 = $variants[3] ?? $variants[1];

        $subtotal2 = (40 * (float)$v3->cost_price) + (25 * (float)$v4->cost_price);
        $total2 = $subtotal2 + 50000; // Biaya kirim 50rb

        $po2 = Purchase::create([
            'purchase_number' => 'PO-202610-0002',
            'supplier_id' => $supplierJava->id,
            'user_id' => $admin->id,
            'purchase_date' => now()->subDay()->toDateString(),
            'status' => Purchase::STATUS_ORDERED,
            'subtotal' => $subtotal2,
            'discount' => 0,
            'shipping_cost' => 50000,
            'total_amount' => $total2,
            'notes' => 'Pesanan stok restock mingguan. Menunggu kedatangan truk ekspedisi.',
        ]);

        $po2->items()->createMany([
            [
                'product_variant_id' => $v3->id,
                'quantity' => 40,
                'cost_price' => $v3->cost_price,
                'subtotal' => 40 * (float)$v3->cost_price,
            ],
            [
                'product_variant_id' => $v4->id,
                'quantity' => 25,
                'cost_price' => $v4->cost_price,
                'subtotal' => 25 * (float)$v4->cost_price,
            ],
        ]);

        // 3. Contoh Penyesuaian Stock Opname (Audit Movement)
        StockMovement::create([
            'product_variant_id' => $v1->id,
            'user_id' => $warehouse->id,
            'type' => StockMovement::TYPE_ADJUSTMENT,
            'reference_type' => StockMovement::REF_MANUAL_ADJUSTMENT,
            'reference_id' => null,
            'quantity' => -2,
            'stock_before' => $v1->current_stock,
            'stock_after' => $v1->current_stock - 2,
            'notes' => 'Penyesuaian Fisik (Opname): 2 pack kemasan sobek saat penyusunan di rak gudang B',
            'created_at' => now()->subHours(6),
        ]);
        $v1->update(['current_stock' => $v1->current_stock - 2]);
    }
}
