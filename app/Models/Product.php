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
        'gallery',
        'net_weight',
        'producer',
        'origin',
        'composition',
        'serving_suggestion',
        'storage_instruction',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'gallery' => 'array',
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

        return asset('images/products/01_atapmerah_135g_depan_edit.png');
    }

    public function getGalleryUrlsAttribute(): array
    {
        $urls = [];
        if (!empty($this->gallery) && is_array($this->gallery)) {
            foreach ($this->gallery as $img) {
                if (file_exists(public_path($img))) {
                    $urls[] = asset($img);
                }
            }
        }
        return $urls;
    }

    public function getAllImagesAttribute(): array
    {
        $images = [$this->image_url];
        foreach ($this->gallery_urls as $url) {
            if (!in_array($url, $images)) {
                $images[] = $url;
            }
        }
        return $images;
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
