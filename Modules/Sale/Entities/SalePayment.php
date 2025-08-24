<?php

namespace Modules\Sale\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Carbon;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Modules\Sale\Entities\Sale;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletes;


class SalePayment extends Model
{
    
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'sale_id',
        'reference',
        'amount',
        'payment_method', // ⬅️ WAJIB
        'bank_name',      // ⬅️ kalau kolomnya ada
        'note',
        'date',
    ];

    protected $casts = [
        'date' => 'date:Y-m-d',
        'amount' => 'integer',
    ];

    public function sale()
{
    return $this->belongsTo(\Modules\Sale\Entities\Sale::class, 'sale_id');
}

    public function scopeBySale($query)
    {
        return $query->where('sale_id', request()->route('sale_id'));
    }

    
public function scopeBetween(Builder $q, $from, $to): Builder
{
    $from = Carbon::parse($from)->startOfDay();
    $to   = Carbon::parse($to)->endOfDay();
    return $q->whereBetween('date', [$from, $to]);
}
}
