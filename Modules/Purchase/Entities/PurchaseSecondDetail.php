<?php

namespace Modules\Purchase\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Modules\Product\Entities\ProductSecond;

class PurchaseSecondDetail extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected $casts = [
        'quantity' => 'integer',
        'unit_price' => 'integer',
        'sub_total' => 'integer',
    ];

    /**
     * Relasi ke purchase_seconds
     */
    public function purchaseSecond()
    {
        return $this->belongsTo(PurchaseSecond::class, 'purchase_second_id');
    }

    /**
     * Relasi ke productseconds (produk bekas)
     */
    public function productSecond()
    {
        return $this->belongsTo(ProductSecond::class, 'product_second_id');
    }

    /**
     * Accessor: Format unit_price sebagai Rupiah
     */
    public function getFormattedPriceAttribute(): string
    {
        return 'Rp ' . number_format($this->unit_price, 0, ',', '.');
    }

    /**
     * Accessor: Format sub_total sebagai Rupiah
     */
    public function getFormattedSubtotalAttribute(): string
    {
        return 'Rp ' . number_format($this->sub_total, 0, ',', '.');
    }
}
