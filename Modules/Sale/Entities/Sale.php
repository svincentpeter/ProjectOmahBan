<?php

namespace Modules\Sale\Entities;

use App\Models\User; // Ditambahkan untuk relasi
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Sale extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected static function boot()
    {
        parent::boot();
        static::creating(function ($model) {
            $number = Sale::max('id') + 1;
            $model->reference = make_reference_id('SL', $number);
        });
    }

    /**
     * Mendefinisikan relasi ke model User.
     * Ini akan memperbaiki error yang Anda alami.
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function saleDetails()
    {
        return $this->hasMany(SaleDetails::class, 'sale_id', 'id');
    }

    public function salePayments()
    {
        return $this->hasMany(SalePayment::class, 'sale_id', 'id');
    }

    public function scopeCompleted($query)
    {
        return $query->where('status', 'Completed');
    }
}