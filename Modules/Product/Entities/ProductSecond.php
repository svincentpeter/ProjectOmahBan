<?php

namespace Modules\Product\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media;
use Modules\Adjustment\Entities\StockMovement;

class ProductSecond extends Model implements HasMedia
{
    use HasFactory, SoftDeletes, InteractsWithMedia;

    protected $fillable = [
        // UPDATE: Tambah unified di awal
        'sku', // BARU: Map dari uniquecode
        'name', // Existing
        'condition', // BARU: Fixed 'second' saat migrasi
        'category_id',
        'brand_id', // BARU: Rename
        'buy_price',
        'sell_price', // BARU: Dari purchaseprice/sellingprice
        'is_active', // BARU: Map dari status 'available' → true
        'meta', // BARU: conditionnotes ke meta['notes']
        'name',
        'uniquecode',
        'categoryid',
        'brandid',
        'size',
        'ring',
        'productyear', // Existing
        'conditionnotes',
        'purchaseprice',
        'sellingprice',
        'status', // Existing
    ];

    protected $casts = [
        // UPDATE: Tambah baru
        'is_active' => 'boolean', // BARU
        'meta' => 'array', // BARU
        'buy_price' => 'decimal:2',
        'sell_price' => 'decimal:2', // BARU
        'purchaseprice' => 'integer',
        'sellingprice' => 'integer', // Existing
        'status' => 'string', // Existing
    ];

    // Existing relations (tetap)
    public function category()
    {
        return $this->belongsTo(\Modules\Product\Entities\Category::class, 'categoryid'); // Existing
    }

    public function brand()
    {
        return $this->belongsTo(\Modules\Product\Entities\Brand::class, 'brandid'); // Existing
    }

    // BARU: Relasi ke stock movements (polymorphic)
    public function stockMovements()
    {
        return $this->morphMany(StockMovement::class, 'productable'); // BARU
    }

    // BARU: Accessor current stock (sum movements only, second per unit)
    public function getCurrentStockAttribute(): float
    {
        $inQty = $this->stockMovements()->where('type', 'in')->sum('quantity') ?? 0; // BARU
        $outQty = $this->stockMovements()->where('type', 'out')->sum('quantity') ?? 0; // BARU
        return (float) ($inQty - $outQty); // BARU: Biasanya 1 atau 0 untuk second
    }

    // BARU: Scopes (second fixed, tapi active untuk konsistensi)
    public function scopeSecond($query)
    {
        return $query->where('condition', 'second'); // BARU: Fixed filter
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true); // BARU
    }

    // Existing media methods (registerMediaCollections, registerMediaConversions) – TETAP SAMA
    public function registerMediaCollections(): void
    {
        // Existing
        $this->addMediaCollection('images')->useFallbackUrl(asset('images/fallback/product-image.png')); // Existing
    }

    public function registerMediaConversions(\Spatie\MediaLibrary\MediaCollections\Models\Media $media = null): void
    {
        // Existing
        $this->addMediaConversion('thumb')->width(50)->height(50)->performOnCollections('images');
    }
}
