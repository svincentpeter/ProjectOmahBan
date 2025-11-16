<?php

namespace Modules\Product\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class ProductSecond extends Model implements HasMedia
{
    use HasFactory, SoftDeletes, InteractsWithMedia;

    protected $table = 'product_seconds';

    protected $fillable = [
        'name',
        'unique_code',
        'condition_notes',
        'purchase_price',
        'selling_price',
        'status',        // 'available' | 'sold'
        'category_id',
        'brand_id',
        'size',
        'ring',
        'product_year',
    ];

    protected $casts = [
        'purchase_price' => 'integer',
        'selling_price'  => 'integer',
        'product_year'   => 'integer',
    ];

    // ===== RELATION =====

    public function category()
    {
        return $this->belongsTo(Category::class, 'category_id');
    }

    public function brand()
    {
        return $this->belongsTo(Brand::class, 'brand_id');
    }

    // ===== ACCESSOR: stok logika (1 kalau available, 0 kalau sold) =====

    public function getCurrentStockAttribute(): int
    {
        return $this->status === 'available' ? 1 : 0;
    }

    // ===== MEDIA =====

    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('images')
            ->useFallbackUrl(asset('images/fallback/product-image.png'));
    }

    public function registerMediaConversions(Media $media = null): void
    {
        $this->addMediaConversion('thumb')
            ->width(50)
            ->height(50)
            ->performOnCollections('images');
    }
}
