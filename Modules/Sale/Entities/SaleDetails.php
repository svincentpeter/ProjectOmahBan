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

    protected static function booted()
    {
        static::creating(function (SaleDetails $detail) {
            // Default fallback untuk kolom yang NOT NULL
            if (empty($detail->item_name)) {
                // pakai product_name kalau ada, kalau tidak ada pakai '-'
                $detail->item_name = $detail->product_name ?? '-';
            }

            if (empty($detail->product_code)) {
                $detail->product_code = '-';
            }
        });
    }

    public function product()
    {
        return $this->belongsTo(Product::class, 'product_id', 'id');
    }

    public function sale()
    {
        return $this->belongsTo(Sale::class, 'sale_id', 'id');
    }

    // HAPUS mutator ×100/÷100 karena DB menyimpan angka rupiah utuh.
    // Kalau kamu memang butuh format tampilan, formatting lakukan di view (helper/Blade), bukan di DB mutator.
}
