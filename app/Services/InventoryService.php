<?php

namespace App\Services;

use App\Models\ProductVariant;
use App\Models\StockMovement;
use Exception;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class InventoryService
{
    /**
     * Increase stock (e.g., from confirmed supplier purchase or return).
     * Uses pessimistic locking to prevent race conditions.
     */
    public function addStock(
        ProductVariant|int $variant,
        int $quantity,
        string $referenceType,
        ?int $referenceId = null,
        ?string $notes = null,
        ?int $userId = null
    ): StockMovement {
        if ($quantity <= 0) {
            throw new Exception("Jumlah penambahan stok harus lebih dari 0.");
        }

        return DB::transaction(function () use ($variant, $quantity, $referenceType, $referenceId, $notes, $userId) {
            $variantId = $variant instanceof ProductVariant ? $variant->id : $variant;

            // Lock row for update
            $lockedVariant = ProductVariant::where('id', $variantId)->lockForUpdate()->firstOrFail();

            $stockBefore = $lockedVariant->current_stock;
            $stockAfter = $stockBefore + $quantity;

            $lockedVariant->update(['current_stock' => $stockAfter]);

            return StockMovement::create([
                'product_variant_id' => $lockedVariant->id,
                'user_id' => $userId ?? Auth::id(),
                'type' => StockMovement::TYPE_IN,
                'reference_type' => $referenceType,
                'reference_id' => $referenceId,
                'quantity' => $quantity,
                'stock_before' => $stockBefore,
                'stock_after' => $stockAfter,
                'notes' => $notes ?? 'Penerimaan stok masuk',
            ]);
        });
    }

    /**
     * Decrease stock (e.g., for fulfilled sales order).
     * Uses pessimistic locking and strictly prevents negative stock.
     */
    public function reduceStock(
        ProductVariant|int $variant,
        int $quantity,
        string $referenceType,
        ?int $referenceId = null,
        ?string $notes = null,
        ?int $userId = null
    ): StockMovement {
        if ($quantity <= 0) {
            throw new Exception("Jumlah pengurangan stok harus lebih dari 0.");
        }

        return DB::transaction(function () use ($variant, $quantity, $referenceType, $referenceId, $notes, $userId) {
            $variantId = $variant instanceof ProductVariant ? $variant->id : $variant;

            // Lock row for update
            $lockedVariant = ProductVariant::where('id', $variantId)->lockForUpdate()->firstOrFail();

            $stockBefore = $lockedVariant->current_stock;

            if ($stockBefore < $quantity) {
                throw new Exception("Stok tidak mencukupi untuk SKU {$lockedVariant->sku}. Stok tersedia: {$stockBefore}, diminta: {$quantity}.");
            }

            $stockAfter = $stockBefore - $quantity;

            $lockedVariant->update(['current_stock' => $stockAfter]);

            return StockMovement::create([
                'product_variant_id' => $lockedVariant->id,
                'user_id' => $userId ?? Auth::id(),
                'type' => StockMovement::TYPE_OUT,
                'reference_type' => $referenceType,
                'reference_id' => $referenceId,
                'quantity' => -$quantity,
                'stock_before' => $stockBefore,
                'stock_after' => $stockAfter,
                'notes' => $notes ?? 'Pengurangan stok keluar',
            ]);
        });
    }

    /**
     * Manual stock adjustment with authorized reason (e.g. audit opname, rusak, hilang).
     */
    public function adjustStock(
        ProductVariant|int $variant,
        int $newActualStock,
        string $reason,
        ?int $userId = null
    ): StockMovement {
        if ($newActualStock < 0) {
            throw new Exception("Stok fisik tidak boleh bernilai negatif.");
        }

        return DB::transaction(function () use ($variant, $newActualStock, $reason, $userId) {
            $variantId = $variant instanceof ProductVariant ? $variant->id : $variant;

            $lockedVariant = ProductVariant::where('id', $variantId)->lockForUpdate()->firstOrFail();

            $stockBefore = $lockedVariant->current_stock;
            $diff = $newActualStock - $stockBefore;

            if ($diff === 0) {
                throw new Exception("Jumlah stok fisik baru sama dengan stok tercatat saat ini.");
            }

            $lockedVariant->update(['current_stock' => $newActualStock]);

            return StockMovement::create([
                'product_variant_id' => $lockedVariant->id,
                'user_id' => $userId ?? Auth::id(),
                'type' => StockMovement::TYPE_ADJUSTMENT,
                'reference_type' => StockMovement::REF_MANUAL_ADJUSTMENT,
                'reference_id' => null,
                'quantity' => $diff,
                'stock_before' => $stockBefore,
                'stock_after' => $newActualStock,
                'notes' => 'Penyesuaian Fisik (Opname): ' . $reason,
            ]);
        });
    }
}
