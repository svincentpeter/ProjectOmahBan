<?php

namespace Modules\Product\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Brand extends Model
{
    use HasFactory;

    protected $fillable = ['name']; // Izinkan kolom 'name' untuk diisi

    public function products()
    {
        return $this->hasMany(Product::class, 'brand_id');
    }
}
