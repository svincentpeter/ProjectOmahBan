<?php

namespace Modules\Product\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ServiceMaster extends Model
{
    use SoftDeletes;

    protected $table = 'service_masters';

    protected $fillable = ['service_name', 'standard_price', 'category', 'description', 'status', 'price_before', 'price_after', 'price_updated_at', 'updated_by'];

    protected $casts = [
        'standard_price' => 'integer',
        'status' => 'integer',
        'price_updated_at' => 'datetime',
    ];

    // Query helper: hanya ambil jasa yang aktif
    public function scopeActive($query)
    {
        return $query->where('status', 1)->whereNull('deleted_at');
    }

    // Relasi ke audit log
    public function audits()
    {
        return $this->hasMany(ServiceMasterAudit::class, 'service_master_id');
    }

    // Helper format harga
    public function getFormattedPrice()
    {
        return format_currency($this->standard_price);
    }

    // Relasi ke user yang update
    public function updatedBy()
    {
        return $this->belongsTo(\App\Models\User::class, 'updated_by');
    }
}
