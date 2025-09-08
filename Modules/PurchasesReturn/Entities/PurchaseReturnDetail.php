<?php

namespace Modules\PurchasesReturn\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Modules\Product\Entities\Product;

class PurchaseReturnDetail extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected $with = ['product'];

    // + Tambahkan setelah baris 15
    protected $casts = [
        'price'                   => 'integer',
        'unit_price'              => 'integer',
        'sub_total'               => 'integer',
        'product_discount_amount' => 'integer',
        'product_tax_amount'      => 'integer',
    ];


    public function product()
    {
        return $this->belongsTo(Product::class, 'product_id', 'id');
    }

    public function purchaseReturn()
    {
        return $this->belongsTo(PurchaseReturn::class, 'purchase_return_id', 'id');
    }
}
