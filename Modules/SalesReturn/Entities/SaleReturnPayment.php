<?php

namespace Modules\SalesReturn\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Carbon;

class SaleReturnPayment extends Model
{
    use HasFactory;

    protected $guarded = [];

    // + Tambahkan setelah baris 13
    protected $casts = [
        'amount' => 'integer',
        'date'   => 'date',
    ];


    public function saleReturn()
    {
        return $this->belongsTo(SaleReturn::class, 'sale_return_id', 'id');
    }

    public function getDateAttribute($value)
    {
        return Carbon::parse($value)->format('d M, Y');
    }

    public function scopeBySaleReturn($query)
    {
        return $query->where('sale_return_id', request()->route('sale_return_id'));
    }
}
