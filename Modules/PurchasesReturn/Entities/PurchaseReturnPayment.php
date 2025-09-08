<?php

namespace Modules\PurchasesReturn\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Carbon;

class PurchaseReturnPayment extends Model
{
    use HasFactory;

    protected $guarded = [];

    // + Tambahkan setelah baris 13
    protected $casts = [
        'amount' => 'integer',
        'date'   => 'date',
    ];


    public function purchaseReturn()
    {
        return $this->belongsTo(PurchaseReturn::class, 'purchase_return_id', 'id');
    }

    public function getDateAttribute($value)
    {
        return Carbon::parse($value)->format('d M, Y');
    }

    public function scopeByPurchaseReturn($query)
    {
        return $query->where('purchase_return_id', request()->route('purchase_return_id'));
    }
}
