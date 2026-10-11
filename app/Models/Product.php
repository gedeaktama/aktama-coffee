<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        'category_id',
        'name',
        'slug',
        'brand',
        'coffee_type',
        'form',
        'description',
        'image',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function variants(): HasMany
    {
        return $this->hasMany(ProductVariant::class);
    }

    public function getImageUrlAttribute(): string
    {
        if (!empty($this->image) && file_exists(public_path($this->image))) {
            return asset($this->image);
        }

        $slug = $this->slug ?? Str::slug($this->name);

        if (str_contains($slug, 'gayo')) {
            return asset('images/gayo.jpg');
        }
        if (str_contains($slug, 'toraja')) {
            return asset('images/toraja.jpg');
        }
        if (str_contains($slug, 'robusta') || str_contains($slug, 'lampung') || str_contains($slug, 'temanggung')) {
            return asset('images/temanggung.jpg');
        }

        return asset('images/flores.jpg');
    }

    public static function boot()
    {
        parent::boot();

        static::creating(function ($product) {
            if (empty($product->slug)) {
                $product->slug = Str::slug($product->name);
            }
        });
    }
}
