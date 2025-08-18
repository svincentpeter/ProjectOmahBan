<?php

namespace Modules\Sale\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Carbon;

class SalePayment extends Model
{

    use HasFactory;

    protected $guarded = [];

    protected $fillable = [
    'sale_id','reference','amount','payment_method','note','date'
];


    public function sale()
    {
        return $this->belongsTo(Sale::class, 'sale_id', 'id');
    }


    public function getDateAttribute($value)
    {
        return Carbon::parse($value)->format('d M, Y');
    }

    public function scopeBySale($query)
    {
        return $query->where('sale_id', request()->route('sale_id'));
    }
}
