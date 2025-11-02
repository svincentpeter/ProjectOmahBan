<?php

namespace Modules\Adjustment\Entities;

use Illuminate\Database\Eloquent\Model;
use Modules\Product\Entities\Product;

class AdjustedProduct extends Model
{
    protected $table = 'adjusted_products';

    protected $fillable = [
        'adjustment_id',
        'product_id',
        'quantity',
        'type', // type: add|sub
        'created_at',
        'updated_at',
    ];

    protected $casts = [
        'adjustment_id' => 'integer',
        'product_id' => 'integer',
        'quantity' => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /** -------------------- RELATIONS -------------------- */
    public function adjustment()
    {
        return $this->belongsTo(Adjustment::class, 'adjustment_id');
    }

    public function product()
    {
        return $this->belongsTo(Product::class, 'product_id');
    }

    /** -------------------- ACCESSORS -------------------- */

    // NEW: tampilkan +10 / -5 berdasarkan type
    public function getFormattedQuantityAttribute(): string
    {
        $sign = $this->type === 'add' ? '+' : '-';
        return sprintf('%s%d', $sign, (int) $this->quantity);
    }

    protected $appends = ['formatted_quantity'];
}
