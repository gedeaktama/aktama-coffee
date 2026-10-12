<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProductVariant extends Model
{
    use HasFactory;

    protected $fillable = [
        'product_id',
        'sku',
        'variant_name',
        'package_weight_grams',
        'grind_form',
        'barcode',
        'cost_price',
        'selling_price',
        'price_toko',
        'price_kafe',
        'tier_pricing',
        'current_stock',
        'minimum_stock',
        'is_active',
    ];

    protected $casts = [
        'package_weight_grams' => 'integer',
        'cost_price' => 'decimal:2',
        'selling_price' => 'decimal:2',
        'price_toko' => 'decimal:2',
        'price_kafe' => 'decimal:2',
        'tier_pricing' => 'array',
        'current_stock' => 'integer',
        'minimum_stock' => 'integer',
        'is_active' => 'boolean',
    ];

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function isLowStock(): bool
    {
        return $this->current_stock <= $this->minimum_stock;
    }

    /**
     * Hitung harga berdasarkan tipe pelanggan (retail, toko, kafe) dan kuantitas
     */
    public function getPriceFor(?string $customerType = null, int $quantity = 1): float
    {
        $customerType = strtolower($customerType ?? 'retail');

        if ($customerType === Customer::TYPE_TOKO || $customerType === Customer::TYPE_RESELLER || $customerType === Customer::TYPE_KONSINYASI) {
            // Cek jika ada tier pricing berbasis volume kuantitas
            if (!empty($this->tier_pricing) && is_array($this->tier_pricing)) {
                if ($quantity >= 100 && isset($this->tier_pricing['tier_100_up'])) {
                    return (float)$this->tier_pricing['tier_100_up'];
                }
                if ($quantity >= 60 && isset($this->tier_pricing['tier_60_99'])) {
                    return (float)$this->tier_pricing['tier_60_99'];
                }
                if ($quantity >= 10 && isset($this->tier_pricing['tier_10_59'])) {
                    return (float)$this->tier_pricing['tier_10_59'];
                }
            }

            return (float)($this->price_toko ?? $this->selling_price);
        }

        if ($customerType === Customer::TYPE_KAFE) {
            return (float)($this->price_kafe ?? $this->price_toko ?? $this->selling_price);
        }

        // Default pelanggan umum / retail
        return (float)$this->selling_price;
    }

    /**
     * Format Rupiah harga standar retail
     */
    public function getFormattedPriceAttribute(): string
    {
        return 'Rp ' . number_format($this->selling_price, 0, ',', '.');
    }

    public function getFormattedPriceTokoAttribute(): string
    {
        return 'Rp ' . number_format($this->price_toko ?? $this->selling_price, 0, ',', '.');
    }

    public function getFormattedPriceKafeAttribute(): string
    {
        return 'Rp ' . number_format($this->price_kafe ?? $this->price_toko ?? $this->selling_price, 0, ',', '.');
    }

    public function getFormattedPriceFor(?string $customerType = null, int $quantity = 1): string
    {
        return 'Rp ' . number_format($this->getPriceFor($customerType, $quantity), 0, ',', '.');
    }

    public function getFormattedCostAttribute(): string
    {
        return 'Rp ' . number_format($this->cost_price, 0, ',', '.');
    }

    /**
     * Cek apakah tipe pelanggan mendapatkan harga diskon khusus mitra
     */
    public function hasSpecialPriceFor(?string $customerType = null): bool
    {
        if (!$customerType || $customerType === Customer::TYPE_RETAIL) {
            return false;
        }

        $specialPrice = $this->getPriceFor($customerType);
        return $specialPrice < (float)$this->selling_price;
    }
}
