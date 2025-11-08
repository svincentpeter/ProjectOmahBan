<?php

namespace Modules\Sale\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class SaleDetails extends Model
{
    use HasFactory;

    protected $table = 'sale_details';

    /** Jangan eager-load default untuk hemat query */
    protected $with = [];

    protected $fillable = [
        'sale_id',
        'item_name',
        'product_id',
        'productable_id',
        'productable_type', // contoh: 'Modules\Product\Entities\ProductSecond'
        'source_type', // new|second|manual
        'manual_kind',
        'product_name',
        'product_code',
        'quantity',
        'price',
        'hpp',
        'manual_hpp',
        'unit_price',
        'sub_total',
        'subtotal_profit',
        'product_discount_amount',
        'product_discount_type', // fixed|percent
        'product_tax_amount',
        'original_price',
        'is_price_adjusted',
        'price_adjustment_amount',
        'price_adjustment_note',
        'adjusted_by',
        'adjusted_at',
    ];

    protected $casts = [
        'quantity' => 'integer',
        'price' => 'integer',
        'hpp' => 'integer',
        'manual_hpp' => 'integer',
        'unit_price' => 'integer',
        'sub_total' => 'integer',
        'subtotal_profit' => 'integer',
        'product_discount_amount' => 'integer',
        'product_tax_amount' => 'integer',
        'original_price' => 'integer',
        'price_adjustment_amount' => 'integer',
        'is_price_adjusted' => 'boolean',
        'adjusted_at' => 'datetime',
    ];

    protected $attributes = [
        'product_discount_type' => 'fixed',
    ];

    /* ============================
     | Model Hooks
     |============================ */

    protected static function booted()
    {
        static::creating(function (SaleDetails $d) {
            // default name/code
            $d->item_name = $d->item_name ?: ($d->product_name ?: '-');
            $d->product_code = $d->product_code ?: '-';

            // sanitasi source_type
            $st = strtolower((string) $d->source_type);
            $d->source_type = in_array($st, ['new', 'second', 'manual'], true) ? $st : 'new';

            // default discount type
            $dt = strtolower((string) $d->product_discount_type);
            $d->product_discount_type = in_array($dt, ['fixed', 'percent'], true) ? $dt : 'fixed';

            // ===== Default & kalkulasi edit harga =====
            $d->price = (int) ($d->price ?? 0);
            $d->original_price = is_null($d->original_price) ? $d->price : (int) $d->original_price;

            $d->is_price_adjusted = (int) ($d->price !== $d->original_price);
            $d->price_adjustment_amount = (int) ($d->original_price - $d->price); // bisa negatif jika markup

            if ($d->is_price_adjusted && empty($d->adjusted_by)) {
                $d->adjusted_by = optional(auth()->user())->id;
                $d->adjusted_at = now();
            }
        });

        static::updating(function (SaleDetails $d) {
            // hygiene
            $st = strtolower((string) $d->source_type);
            $d->source_type = in_array($st, ['new', 'second', 'manual'], true) ? $st : 'new';

            $dt = strtolower((string) $d->product_discount_type);
            $d->product_discount_type = in_array($dt, ['fixed', 'percent'], true) ? $dt : 'fixed';

            // recalculation
            $d->price = (int) ($d->price ?? 0);
            $d->original_price = is_null($d->original_price) ? $d->price : (int) $d->original_price;

            $d->is_price_adjusted = (int) ($d->price !== $d->original_price);
            $d->price_adjustment_amount = (int) ($d->original_price - $d->price);

            if ($d->is_price_adjusted && empty($d->adjusted_by)) {
                $d->adjusted_by = optional(auth()->user())->id;
                $d->adjusted_at = now();
            }
        });
    }

    /* ============================
     | Relationships
     |============================ */

    public function sale(): BelongsTo
    {
        return $this->belongsTo(Sale::class, 'sale_id');
    }

    public function productable()
    {
        return $this->morphTo();
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(\Modules\Product\Entities\Product::class, 'product_id', 'id');
    }

    public function adjuster(): BelongsTo
    {
        return $this->belongsTo(\App\Models\User::class, 'adjusted_by');
    }

    /* ============================
     | Scopes bantu
     |============================ */

    public function scopeAdjusted($q)
    {
        return $q->where('is_price_adjusted', 1);
    }

    public function scopeManual($q)
    {
        return $q->where('source_type', 'manual');
    }
}
