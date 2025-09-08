<?php

namespace Modules\SalesReturn\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class SaleReturn extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected $casts = [
        // semua kolom uang disimpan sebagai IDR integer
        'total_amount'    => 'integer',
        'paid_amount'     => 'integer',
        'due_amount'      => 'integer',
        'tax_amount'      => 'integer',
        'discount_amount' => 'integer',
        'shipping_amount' => 'integer',
    ];

    public function saleReturnDetails()
    {
        return $this->hasMany(SaleReturnDetail::class, 'sale_return_id', 'id');
    }

    public function saleReturnPayments()
    {
        return $this->hasMany(SaleReturnPayment::class, 'sale_return_id', 'id');
    }

    public static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            $number = SaleReturn::max('id') + 1;
            $model->reference = make_reference_id('SLRN', $number);;
        });
    }

    public function scopeCompleted($query)
    {
        return $query->where('status', 'Completed');
    }
}
