<?php

namespace Modules\Sale\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Modules\Product\Entities\Product;
use Modules\Product\Entities\ProductSecond;
use Modules\Product\Entities\ServiceMaster;

class SaleReturnDetail extends Model
{
    protected $table = 'sale_return_details';

    // Condition constants
    public const CONDITION_GOOD = 'good';
    public const CONDITION_DAMAGED = 'damaged';
    public const CONDITION_DEFECTIVE = 'defective';

    // Source type constants (matches SaleDetails)
    public const SOURCE_NEW = 'new';
    public const SOURCE_SECOND = 'second';
    public const SOURCE_SERVICE = 'service';
    public const SOURCE_MANUAL = 'manual';

    protected $fillable = [
        'sale_return_id',
        'sale_detail_id',
        'product_name',
        'product_code',
        'quantity',
        'unit_price',
        'sub_total',
        'source_type',
        'reason',
        'condition',
        'restock',
        'productable_type',
        'productable_id',
    ];

    protected $casts = [
        'quantity' => 'integer',
        'unit_price' => 'integer',
        'sub_total' => 'integer',
        'restock' => 'boolean',
    ];

    /* ============================
     | Relationships
     |============================ */

    /**
     * Parent sale return
     */
    public function saleReturn(): BelongsTo
    {
        return $this->belongsTo(SaleReturn::class, 'sale_return_id');
    }

    /**
     * Original sale detail
     */
    public function saleDetail(): BelongsTo
    {
        return $this->belongsTo(SaleDetails::class, 'sale_detail_id');
    }

    /**
     * Polymorphic relation to product/service
     */
    public function productable()
    {
        return $this->morphTo();
    }

    /* ============================
     | Accessors
     |============================ */

    /**
     * Get condition badge class for UI
     */
    public function getConditionBadgeClassAttribute(): string
    {
        return match($this->condition) {
            self::CONDITION_GOOD => 'bg-green-100 text-green-800',
            self::CONDITION_DAMAGED => 'bg-orange-100 text-orange-800',
            self::CONDITION_DEFECTIVE => 'bg-red-100 text-red-800',
            default => 'bg-gray-100 text-gray-800',
        };
    }

    /**
     * Get source type label
     */
    public function getSourceTypeLabelAttribute(): string
    {
        return match($this->source_type) {
            self::SOURCE_NEW => 'Produk Baru',
            self::SOURCE_SECOND => 'Produk Second',
            self::SOURCE_SERVICE => 'Jasa',
            self::SOURCE_MANUAL => 'Manual Input',
            default => ucfirst($this->source_type ?? '-'),
        };
    }

    /**
     * Check if item can be restocked
     */
    public function getCanRestockAttribute(): bool
    {
        // Services and manual items cannot be restocked
        return in_array($this->source_type, [self::SOURCE_NEW, self::SOURCE_SECOND]);
    }

    /* ============================
     | Boot
     |============================ */

    protected static function boot()
    {
        parent::boot();

        static::creating(function (self $model) {
            // Calculate sub_total if not set
            if (empty($model->sub_total)) {
                $model->sub_total = $model->quantity * $model->unit_price;
            }

            // Default condition
            if (empty($model->condition)) {
                $model->condition = self::CONDITION_GOOD;
            }

            // Default restock based on source type
            if (!isset($model->restock)) {
                $model->restock = in_array($model->source_type, [self::SOURCE_NEW, self::SOURCE_SECOND]);
            }
        });

        // After saving, recalculate parent totals
        static::saved(function (self $model) {
            if ($model->saleReturn) {
                $model->saleReturn->recalculateTotals();
            }
        });

        static::deleted(function (self $model) {
            if ($model->saleReturn) {
                $model->saleReturn->recalculateTotals();
            }
        });
    }
}
