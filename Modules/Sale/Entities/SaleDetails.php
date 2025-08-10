<?php

namespace Modules\Sale\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Modules\Product\Entities\Product;

class SaleDetails extends Model
{
    use HasFactory;

    protected $guarded = [];
    protected $with = ['product'];

    protected static function booted()
    {
        static::creating(function (SaleDetails $d) {
            $d->item_name    = $d->item_name ?: ($d->product_name ?? '-');
            $d->product_code = $d->product_code ?: '-';
        });
    }

    public function product()
    {
        return $this->belongsTo(Product::class, 'product_id', 'id');
    }

    public function sale()
    {
        return $this->belongsTo(Sale::class, 'sale_id', 'id');
    }
}
