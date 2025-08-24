<?php

namespace Modules\Expense\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Carbon;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Builder;


class Expense extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $fillable = [
        'category_id', 'date', 'reference', 'details', 'amount',
        'user_id', 'payment_method', 'bank_name', 'attachment_path',
    ];

    protected $casts = [
        'date'   => 'date',
        'amount' => 'integer',
    ];

    public function user()     { return $this->belongsTo(\App\Models\User::class); }

    public static function nextReference(Carbon $date): string
    {
        $prefix = 'EX-' . $date->format('Ymd') . '-';
        $last = static::whereDate('date', $date)->max('reference');
        $seq  = ($last && preg_match('/-(\d+)$/', $last, $m)) ? ((int)$m[1] + 1) : 1;
        return $prefix . str_pad((string)$seq, 4, '0', STR_PAD_LEFT);
    }

    protected $guarded = [];

    public function category() {
        return $this->belongsTo(ExpenseCategory::class, 'category_id', 'id');
    }

    public static function boot() {
        parent::boot();

        static::creating(function ($model) {
            $number = Expense::max('id') + 1;
            $model->reference = make_reference_id('EXP', $number);
        });
    }

    public function getDateAttribute($value) {
        return Carbon::parse($value)->format('d M, Y');
    }

    public function setAmountAttribute($value) {
        $this->attributes['amount'] = ($value * 100);
    }

    public function getAmountAttribute($value) {
        return ($value / 100);
    }
    public function scopeBetween(Builder $q, $from, $to): Builder
    {
        $from = Carbon::parse($from)->startOfDay();
        $to   = Carbon::parse($to)->endOfDay();
        return $q->whereBetween('date', [$from, $to]);
    }

}
