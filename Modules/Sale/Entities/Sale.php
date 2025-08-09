<?php

namespace Modules\Sale\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Sale extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function saleDetails()
    {
        return $this->hasMany(SaleDetails::class, 'sale_id', 'id');
    }

    public function salePayments()
    {
        return $this->hasMany(SalePayment::class, 'sale_id', 'id');
    }

    public static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            $number = Sale::max('id') + 1;
            $model->reference = make_reference_id('SL', $number);
        });
    }

    public function scopeCompleted($query)
    {
        return $query->where('status', 'Completed');
    }

    public function getShippingAmountAttribute($value)
    {
        return $value / 100;
    }
    public function setShippingAmountAttribute($value)
    {
        $this->attributes['shipping_amount'] = (int) round($value * 100);
    }

    public function getPaidAmountAttribute($value)
    {
        return $value / 100;
    }
    public function setPaidAmountAttribute($value)
    {
        $this->attributes['paid_amount'] = (int) round($value * 100);
    }

    public function getTotalAmountAttribute($value)
    {
        return $value / 100;
    }
    public function setTotalAmountAttribute($value)
    {
        $this->attributes['total_amount'] = (int) round($value * 100);
    }

    public function getDueAmountAttribute($value)
    {
        return $value / 100;
    }
    public function setDueAmountAttribute($value)
    {
        $this->attributes['due_amount'] = (int) round($value * 100);
    }

    public function getTaxAmountAttribute($value)
    {
        return $value / 100;
    }
    public function setTaxAmountAttribute($value)
    {
        $this->attributes['tax_amount'] = (int) round($value * 100);
    }

    public function getDiscountAmountAttribute($value)
    {
        return $value / 100;
    }
    public function setDiscountAmountAttribute($value)
    {
        $this->attributes['discount_amount'] = (int) round($value * 100);
    }
}
