<?php

namespace Modules\Sale\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class SaleDetails extends Model
{
    use HasFactory;

    /**
     * Jangan eager-load apapun by default agar hemat query.
     * Pakai ->with('product') hanya saat dibutuhkan.
     */
    protected $with = [];

    /**
     * Kolom yang boleh mass-assign.
     */
    protected $fillable = [
        'sale_id',
        'item_name',
        'product_id', // untuk produk baru
        'productable_id', // untuk produk second
        'productable_type', // 'Modules\Product\Entities\ProductSecond'
        'source_type', // new|second|manual
        'product_name',
        'product_code',
        'quantity',
        'price',
        'hpp',
        'unit_price',
        'sub_total',
        'subtotal_profit',
        'product_discount_amount',
        'product_discount_type', // fixed|percent (konsistenkan di UI)
        'product_tax_amount',
    ];

    /**
     * Casting numerik ke integer agar perhitungan konsisten.
     */
    protected $casts = [
        'quantity' => 'integer',
        'price' => 'integer',
        'hpp' => 'integer',
        'unit_price' => 'integer',
        'sub_total' => 'integer',
        'subtotal_profit' => 'integer',
        'product_discount_amount' => 'integer',
        'product_tax_amount' => 'integer',
    ];

    /**
     * Default attributes (fallback aman).
     */
    protected $attributes = [
        'product_discount_type' => 'fixed',
    ];

    /**
     * Hook: set nilai default agar data rapi.
     */
    protected static function booted()
    {
        static::creating(function (SaleDetails $d) {
            // default name/code
            $d->item_name = $d->item_name ?: ($d->product_name ?: '-');
            $d->product_code = $d->product_code ?: '-';

            // sanitasi source_type
            $st = strtolower((string) $d->source_type);
            if (!in_array($st, ['new', 'second', 'manual'], true)) {
                $st = 'new';
            }
            $d->source_type = $st;

            // default discount type
            $dt = strtolower((string) $d->product_discount_type);
            if (!in_array($dt, ['fixed', 'percent'], true)) {
                $dt = 'fixed';
            }
            $d->product_discount_type = $dt;
        });

        static::updating(function (SaleDetails $d) {
            // Jaga konsistensi saat update
            $st = strtolower((string) $d->source_type);
            if (!in_array($st, ['new', 'second', 'manual'], true)) {
                $st = 'new';
            }
            $d->source_type = $st;

            $dt = strtolower((string) $d->product_discount_type);
            if (!in_array($dt, ['fixed', 'percent'], true)) {
                $dt = 'fixed';
            }
            $d->product_discount_type = $dt;
        });
    }

    /**
     * Relasi ke header penjualan.
     */
    public function sale()
    {
        return $this->belongsTo(\Modules\Sale\Entities\Sale::class);
    }

    /**
     * Relasi polymorphic ke barang second (atau entitas lain jika nanti ada).
     */
    public function productable()
    {
        return $this->morphTo();
    }

    /**
     * Relasi ke produk baru (non-polymorphic).
     */
    public function product()
    {
        return $this->belongsTo(\Modules\Product\Entities\Product::class, 'product_id', 'id');
    }

    /**
     * Helper kecil untuk mempermudah logika di tempat lain.
     */
    public function isNew(): bool
    {
        return $this->source_type === 'new';
    }

    public function isSecond(): bool
    {
        return $this->source_type === 'second';
    }

    public function isManual(): bool
    {
        return $this->source_type === 'manual';
    }

    public function adjuster(): BelongsTo
    {
        return $this->belongsTo(\App\Models\User::class, 'adjusted_by');
    }
}
