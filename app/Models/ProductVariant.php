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
        'current_stock',
        'minimum_stock',
        'is_active',
    ];

    protected $casts = [
        'package_weight_grams' => 'integer',
        'cost_price' => 'decimal:2',
        'selling_price' => 'decimal:2',
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
     * Format Rupiah price string
     */
    public function getFormattedPriceAttribute(): string
    {
        return 'Rp ' . number_format($this->selling_price, 0, ',', '.');
    }

    public function getFormattedCostAttribute(): string
    {
        return 'Rp ' . number_format($this->cost_price, 0, ',', '.');
    }
}
