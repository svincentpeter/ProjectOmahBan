<?php

namespace Modules\PurchasesReturn\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class PurchaseReturn extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected $casts = [
        'total_amount'    => 'integer',
        'paid_amount'     => 'integer',
        'due_amount'      => 'integer',
        'tax_amount'      => 'integer',
        'discount_amount' => 'integer',
        'shipping_amount' => 'integer',
    ];


    public function purchaseReturnDetails()
    {
        return $this->hasMany(PurchaseReturnDetail::class, 'purchase_return_id', 'id');
    }

    public function purchaseReturnPayments()
    {
        return $this->hasMany(PurchaseReturnPayment::class, 'purchase_return_id', 'id');
    }

    public static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            $number = PurchaseReturn::max('id') + 1;
            $model->reference = make_reference_id('PRRN', $number);
        });
    }

    public function scopeCompleted($query)
    {
        return $query->where('status', 'Completed');
    }
}
