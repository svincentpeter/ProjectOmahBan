<?php

namespace Modules\Purchase\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Modules\Product\Entities\Product;

class PurchaseDetail extends Model
{
    use HasFactory;

    protected $guarded = [];

    /**
     * Fillable attributes - Disesuaikan dengan struktur tabel yang sudah disederhanakan
     */
    protected $fillable = ['purchase_id', 'product_id', 'product_name', 'product_code', 'quantity', 'unit_price', 'sub_total'];

    /**
     * Casts attributes
     * Amount disimpan sebagai integer Rupiah tanpa desimal
     */
    protected $casts = [
        'quantity' => 'integer',
        'unit_price' => 'integer',
        'sub_total' => 'integer',
    ];

    /**
     * Relasi ke Product
     */
    public function product()
    {
        return $this->belongsTo(Product::class, 'product_id', 'id');
    }

    /**
     * Relasi ke Purchase
     */
    public function purchase()
    {
        return $this->belongsTo(Purchase::class, 'purchase_id', 'id');
    }
}
