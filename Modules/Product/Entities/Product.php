<?php

namespace Modules\Product\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Modules\Product\Entities\Category;
use Modules\Product\Entities\Brand;
use Modules\Adjustment\Entities\AdjustedProduct;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class Product extends Model implements HasMedia
{
    use HasFactory, InteractsWithMedia;

    protected $casts = [
        'product_cost' => 'integer',
        'product_price' => 'integer',
        'product_quantity' => 'integer',
    ];

    protected $fillable = [
        'product_name',
        'product_code',
        'category_id',
        'brand_id',
        'product_size',
        'ring',
        'product_year',
        'product_cost',
        'product_price',
        'product_quantity',
        'stok_awal',
        'product_unit',
        'product_stock_alert',
        'product_note',
    ];

    protected $with = ['media'];

    public function category() {
        return $this->belongsTo(Category::class, 'category_id', 'id');
    }

    public function brand() {
        return $this->belongsTo(Brand::class, 'brand_id', 'id');
    }

    public function adjustedProducts() {
        return $this->hasMany(AdjustedProduct::class, 'product_id', 'id');
    }

    public function registerMediaCollections(): void {
        $this->addMediaCollection('images')
            ->useDisk('public')
            ->useFallbackUrl(asset('images/fallback_product_image.png'));
    }

    /**
     * Register multiple image conversions untuk berbagai kebutuhan
     */
    public function registerMediaConversions(Media $media = null): void {
        // Thumbnail kecil untuk datatable (50x50)
        $this->addMediaConversion('thumb')
            ->width(50)
            ->height(50)
            ->performOnCollections('images');
        
        // Medium untuk POS grid (300x300) - SQUARE
        $this->addMediaConversion('pos-grid')
            ->width(300)
            ->height(300)
            ->performOnCollections('images');
        
        // Large untuk detail page (800x600)
        $this->addMediaConversion('large')
            ->width(800)
            ->height(600)
            ->keepOriginalImageFormat()
            ->performOnCollections('images');
        
        // Preview untuk modal/lightbox (1200x900)
        $this->addMediaConversion('preview')
            ->width(1200)
            ->height(900)
            ->keepOriginalImageFormat()
            ->performOnCollections('images');
    }

    /**
     * Helper method untuk get image URL dengan conversion options
     */
    public function getImageUrl($conversion = '')
    {
        if ($this->hasMedia('images')) {
            $media = $this->getFirstMedia('images');
            
            if ($conversion && $media->hasGeneratedConversion($conversion)) {
                return $media->getUrl($conversion);
            }
            
            return $media->getUrl();
        }
        
        return asset('images/fallback_product_image.png');
    }
}
