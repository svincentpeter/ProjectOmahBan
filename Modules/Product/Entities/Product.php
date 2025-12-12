<?php

namespace Modules\Product\Entities;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media; // FIX: Import untuk conversions
use Modules\Adjustment\Entities\StockMovement;
use Modules\Adjustment\Entities\AdjustedProduct;

class Product extends Model implements HasMedia
{
    use HasFactory, SoftDeletes, InteractsWithMedia;

    protected $fillable = [
        // Unified fields (prioritas untuk unifikasi)
        'sku',
        'name',
        'condition',
        'category_id',
        'brand_id',
        'unit',
        'buy_price',
        'sell_price',
        'avg_cost',
        'is_active',
        'meta',
        
        // Mapped Database Columns (snake_case)
        'product_name',
        'product_code',
        'product_quantity',
        'product_cost',
        'product_price',
        'product_unit',
        'product_stock_alert',
        'product_note',
        'product_size',
        'ring',
        'product_year',
        'stok_awal',

        // Legacy fields (map saat migrasi, tetap support existing jika masih ada referensi)
        'productname',
        'productcode',
        'categoryid',
        'brandid',
        'productsize',
        'productyear',
        'productcost',
        'productprice',
        'productquantity',
        'stokawal',
        'productunit',
        'productstockalert',
        'productnote',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'meta' => 'array',
        'buy_price' => 'decimal:2',
        'sell_price' => 'decimal:2',
        'avg_cost' => 'decimal:6',
        'productcost' => 'integer',
        'productprice' => 'integer',
        'productquantity' => 'integer',
        'stokawal' => 'integer',
    ];

    protected $with = ['media']; // Eager load media untuk efisiensi

    // Relasi existing (tetap, siap ganti foreign key saat migrasi)
    public function category()
    {
        return $this->belongsTo(Category::class, 'category_id'); // Adjust namespace sesuai
    }

    public function brand()
    {
        return $this->belongsTo(\Modules\Product\Entities\Brand::class, 'brand_id'); // Fixed: was 'brandid'
    }

    public function adjustedProducts()
    {
        return $this->hasMany(AdjustedProduct::class, 'product_id');}

    // Relasi ke stock movements (polymorphic, adaptif ke DB current)
    public function stockMovements()
    {
        return $this->morphMany(StockMovement::class, 'productable');
    }

    // Accessor untuk current stock (sum ledger + stokawal, pakai scope untuk efisiensi)
    public function getCurrentStockAttribute()
    {
        // ✅ Ambil langsung dari kolom current_stock atau fallback ke stokawal
        if (isset($this->attributes['current_stock'])) {
            return (float) $this->attributes['current_stock'];
        }
        return (float) ($this->attributes['stokawal'] ?? 0);
    }

    // Scopes untuk filter menu (siap unifikasi dengan condition enum)
    public function scopeNew($query)
    {
        return $query->where('condition', 'new');
    }

    public function scopeSecond($query)
    {
        return $query->where('condition', 'second');
    }

    public function scopeManual($query)
    {
        return $query->where('condition', 'manual');
    }

    public function scopeActive($query)
    {
        return $query->whereNull('deleted_at');
    }

    // Media methods (existing, tetap sama)
    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('images')->useDisk('public')->useFallbackUrl(asset('images/fallback/product-image.png'));
    }

    /**
     * Register multiple image conversions untuk berbagai kebutuhan
     */
    public function registerMediaConversions(Media $media = null): void
    {
        // Thumbnail kecil untuk datatable (50x50)
        $this->addMediaConversion('thumb')->width(50)->height(50)->performOnCollections('images');

        // Medium untuk POS grid (300x300) - SQUARE
        $this->addMediaConversion('pos-grid')->width(300)->height(300)->performOnCollections('images');

        // Large untuk detail page (800x600)
        $this->addMediaConversion('large')->width(800)->height(600)->keepOriginalImageFormat()->performOnCollections('images');

        // Preview untuk modal/lightbox (1200x900)
        $this->addMediaConversion('preview')->width(1200)->height(900)->keepOriginalImageFormat()->performOnCollections('images');
    }

    /**
     * Helper method untuk get image URL dengan conversion options
     */
    public function getImageUrl($conversion)
    {
        if ($this->hasMedia('images')) {
            $media = $this->getFirstMedia('images');
            if ($media->hasGeneratedConversion($conversion)) {
                return $media->getUrl($conversion);
            }
            return $media->getUrl();
        }
        return asset('images/fallback/product-image.png');
    }
}
