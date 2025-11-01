<?php

namespace Modules\Product\Entities; // Sesuaikan jika di Inventory

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class StockLedger extends Model // BARU: Semua baru
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'product_id', 'ref_type', 'ref_id', 'ref_number', // BARU: Link ke dokumen (purchase/sale id)
        'qty_in', 'qty_out', 'unit_cost', 'occurred_at', // BARU: Terpisah in/out, cost untuk valuasi
        'created_by', 'store_id', 'notes' // BARU: Audit who/where
    ];

    protected $casts = [
        'occurred_at' => 'datetime', // BARU
        'qty_in' => 'decimal:6', 'qty_out' => 'decimal:6', // BARU: Presisi stok
        'unit_cost' => 'decimal:6' // BARU
    ];

    // BARU: Relasi ke Product (belongsTo)
    public function product()
    {
        return $this->belongsTo(Product::class); // BARU: Balik ke unified products
    }

    // BARU: Scope untuk sum stok per product
    public function scopeIn($query)
    {
        return $query->where('qty_in', '>', 0); // BARU
    }

    public function scopeOut($query)
    {
        return $query->where('qty_out', '>', 0); // BARU
    }
}
