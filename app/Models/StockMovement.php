<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StockMovement extends Model
{
    use HasFactory;

    public const TYPE_IN = 'in';
    public const TYPE_OUT = 'out';
    public const TYPE_ADJUSTMENT = 'adjustment';

    public const REF_PURCHASE = 'purchase';
    public const REF_SALES_ORDER = 'sales_order';
    public const REF_MANUAL_ADJUSTMENT = 'manual_adjustment';
    public const REF_INITIAL_STOCK = 'initial_stock';

    protected $fillable = [
        'product_variant_id',
        'user_id',
        'type',
        'reference_type',
        'reference_id',
        'quantity',
        'stock_before',
        'stock_after',
        'notes',
    ];

    protected $casts = [
        'quantity' => 'integer',
        'stock_before' => 'integer',
        'stock_after' => 'integer',
    ];

    public function variant(): BelongsTo
    {
        return $this->belongsTo(ProductVariant::class, 'product_variant_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function getTypeBadgeClassAttribute(): string
    {
        return match($this->type) {
            self::TYPE_IN => 'bg-success',
            self::TYPE_OUT => 'bg-danger',
            self::TYPE_ADJUSTMENT => 'bg-warning text-dark',
            default => 'bg-secondary',
        };
    }

    public function getTypeLabelAttribute(): string
    {
        return match($this->type) {
            self::TYPE_IN => 'Masuk (+)',
            self::TYPE_OUT => 'Keluar (-)',
            self::TYPE_ADJUSTMENT => 'Penyesuaian',
            default => ucfirst($this->type),
        };
    }
}
