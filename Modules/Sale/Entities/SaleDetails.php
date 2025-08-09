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

    public function product()
    {
        return $this->belongsTo(Product::class, 'product_id', 'id');
    }

    public function sale()
    {
        return $this->belongsTo(Sale::class, 'sale_id', 'id');
    }

    public function getPriceAttribute($value)
    {
        return $value / 100;
    }
    public function setPriceAttribute($value)
    {
        $this->attributes['price'] = (int) round($value * 100);
    }

    public function getUnitPriceAttribute($value)
    {
        return $value / 100;
    }
    public function setUnitPriceAttribute($value)
    {
        $this->attributes['unit_price'] = (int) round($value * 100);
    }

    public function getSubTotalAttribute($value)
    {
        return $value / 100;
    }
    public function setSubTotalAttribute($value)
    {
        $this->attributes['sub_total'] = (int) round($value * 100);
    }

    public function getProductDiscountAmountAttribute($value)
    {
        return $value / 100;
    }
    public function setProductDiscountAmountAttribute($value)
    {
        $this->attributes['product_discount_amount'] = (int) round($value * 100);
    }

    public function getProductTaxAmountAttribute($value)
    {
        return $value / 100;
    }
    public function setProductTaxAmountAttribute($value)
    {
        $this->attributes['product_tax_amount'] = (int) round($value * 100);
    }
}
