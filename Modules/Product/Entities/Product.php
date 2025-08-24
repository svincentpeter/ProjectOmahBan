<?php

namespace Modules\Product\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Modules\Product\Notifications\NotifyQuantityAlert;
use Modules\Adjustment\Entities\AdjustedProduct;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class Product extends Model implements HasMedia
{
    use HasFactory, InteractsWithMedia;

    protected $guarded = [];

    protected $casts = [
        'product_cost'  => 'integer',
        'product_price' => 'integer',
        'product_quantity' => 'integer',
    ];

    // GANTI SELURUH ARRAY $fillable MENJADI SEPERTI INI:
    protected $fillable = [
        'product_name',
        'product_code',
        'category_id',
        'brand_id',
        'product_size',
        'ring', // Tambahan baru
        'product_year',
        'product_cost',
        'product_price',
        'product_quantity',
        'stok_awal', // Tambahan baru
        'product_unit',
        'product_stock_alert',
        'product_note',
    ];
    // ===== KODE PERBAIKAN SELESAI DI SINI =====

    protected $with = ['media'];

    public function category() {
        return $this->belongsTo(Category::class, 'category_id', 'id');
    }

    public function registerMediaCollections(): void {
        $this->addMediaCollection('images')
            ->useFallbackUrl('/images/fallback_product_image.png');
    }

    public function registerMediaConversions(Media $media = null): void {
        $this->addMediaConversion('thumb')
            ->width(50)
            ->height(50);
    }

    public function setProductCostAttribute($value) {
        $this->attributes['product_cost'] = ($value * 100);
    }

    public function getProductCostAttribute($value) {
        return ($value / 100);
    }

    public function setProductPriceAttribute($value) {
        $this->attributes['product_price'] = ($value * 100);
    }

    public function getProductPriceAttribute($value) {
        return ($value / 100);
    }

    public function brand() {
    return $this->belongsTo(Brand::class, 'brand_id', 'id');
}

public function adjustedProducts() {
        return $this->hasMany(AdjustedProduct::class, 'product_id', 'id');
    }
}
