<?php

namespace Database\Seeders;

use App\Models\ProductVariant;
use App\Models\StockMovement;
use App\Models\User;
use Illuminate\Database\Seeder;

class InventorySeeder extends Seeder
{
    /**
     * Run the inventory initial stock ledger for official products.
     */
    public function run(): void
    {
        $admin = User::where('role', User::ROLE_ADMIN)->first() ?? User::first();
        $warehouse = User::where('role', User::ROLE_WAREHOUSE)->first() ?? $admin;

        if (!$warehouse) {
            return;
        }

        // Catat kartu persediaan saldo awal untuk semua varian resmi
        $variants = ProductVariant::all();

        foreach ($variants as $variant) {
            if ($variant->current_stock > 0 && !StockMovement::where('product_variant_id', $variant->id)->exists()) {
                StockMovement::create([
                    'product_variant_id' => $variant->id,
                    'user_id' => $warehouse->id,
                    'type' => StockMovement::TYPE_IN,
                    'reference_type' => StockMovement::REF_INITIAL_STOCK,
                    'reference_id' => null,
                    'quantity' => $variant->current_stock,
                    'stock_before' => 0,
                    'stock_after' => $variant->current_stock,
                    'notes' => "Saldo Awal Stok Fisik Gudang — SKU {$variant->sku} ({$variant->variant_name})",
                    'created_at' => now(),
                ]);
            }
        }
    }
}
