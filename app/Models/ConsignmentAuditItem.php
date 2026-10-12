<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ConsignmentAuditItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'consignment_audit_id',
        'consignment_item_id',
        'product_variant_id',
        'stock_before',
        'physical_remaining',
        'quantity_sold',
        'quantity_returned',
        'restock_quantity',
        'stock_after',
        'price_per_unit',
        'subtotal',
        'notes',
    ];

    protected $casts = [
        'stock_before' => 'integer',
        'physical_remaining' => 'integer',
        'quantity_sold' => 'integer',
        'quantity_returned' => 'integer',
        'restock_quantity' => 'integer',
        'stock_after' => 'integer',
        'price_per_unit' => 'decimal:2',
        'subtotal' => 'decimal:2',
    ];

    public function audit(): BelongsTo
    {
        return $this->belongsTo(ConsignmentAudit::class, 'consignment_audit_id');
    }

    public function consignmentItem(): BelongsTo
    {
        return $this->belongsTo(ConsignmentItem::class, 'consignment_item_id');
    }

    public function variant(): BelongsTo
    {
        return $this->belongsTo(ProductVariant::class, 'product_variant_id');
    }

    public function getFormattedPriceAttribute(): string
    {
        return 'Rp ' . number_format($this->price_per_unit, 0, ',', '.');
    }

    public function getFormattedSubtotalAttribute(): string
    {
        return 'Rp ' . number_format($this->subtotal, 0, ',', '.');
    }
}
