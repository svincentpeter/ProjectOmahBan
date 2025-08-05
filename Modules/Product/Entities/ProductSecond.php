<?php

namespace Modules\Product\Entities; // <-- Namespace sudah otomatis disesuaikan

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;

class ProductSecond extends Model
{
    use HasFactory, SoftDeletes;

    // Definisikan kolom yang boleh diisi
    protected $fillable = [
        'name',
        'unique_code',
        'condition_notes',
        'photo_path',
        'purchase_price',
        'selling_price',
        'status',
    ];

    // (Kita bisa tambahkan protected static function newFactory() nanti jika perlu)
}