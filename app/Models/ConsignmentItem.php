<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ConsignmentItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'consignment_id',
        'product_variant_id',
        'initial_quantity',
        'current_quantity',
        'sold_quantity',
        'returned_quantity',
        'selling_price',
        'notes',
    ];

    protected $casts = [
        'initial_quantity' => 'integer',
        'current_quantity' => 'integer',
        'sold_quantity' => 'integer',
        'returned_quantity' => 'integer',
        'selling_price' => 'decimal:2',
    ];

    public function consignment(): BelongsTo
    {
        return $this->belongsTo(Consignment::class);
    }

    public function variant(): BelongsTo
    {
        return $this->belongsTo(ProductVariant::class, 'product_variant_id');
    }

    public function auditItems(): HasMany
    {
        return $this->hasMany(ConsignmentAuditItem::class);
    }

    public function getFormattedPriceAttribute(): string
    {
        return 'Rp ' . number_format($this->selling_price, 0, ',', '.');
    }

    public function getCurrentValueAttribute(): float
    {
        return (float) ($this->current_quantity * $this->selling_price);
    }

    public function getFormattedCurrentValueAttribute(): string
    {
        return 'Rp ' . number_format($this->current_value, 0, ',', '.');
    }
}
