<?php

namespace Modules\Sale\Entities;

use Illuminate\Database\Eloquent\Model;

class PriceVarianceLog extends Model
{
    protected $table = 'price_variance_logs';

    public $timestamps = false; // Hanya pakai created_at

    protected $fillable = ['sale_id', 'sale_detail_id', 'item_name', 'master_price', 'input_price', 'variance_amount', 'variance_percent', 'variance_level', 'reason_provided', 'approval_status', 'approved_by', 'approved_at', 'cashier_id'];

    protected $casts = [
        'master_price' => 'integer',
        'input_price' => 'integer',
        'variance_amount' => 'integer',
        'variance_percent' => 'float',
        'approved_at' => 'datetime',
        'created_at' => 'datetime',
    ];

    // Relasi
    public function sale()
    {
        return $this->belongsTo(Sale::class, 'sale_id');
    }

    public function saleDetail()
    {
        return $this->belongsTo(SaleDetails::class, 'sale_detail_id');
    }

    public function cashier()
    {
        return $this->belongsTo(\App\Models\User::class, 'cashier_id');
    }

    public function approver()
    {
        return $this->belongsTo(\App\Models\User::class, 'approved_by');
    }

    // Query helpers
    public function scopeWarning($query)
    {
        return $query->where('variance_level', 'warning');
    }

    public function scopeCritical($query)
    {
        return $query->where('variance_level', 'critical');
    }

    public function scopePending($query)
    {
        return $query->where('approval_status', 'pending');
    }

    // Helper format variance untuk display
    public function getFormattedVariance()
    {
        $sign = $this->variance_amount >= 0 ? '+' : '';
        return "{$sign}" . format_currency($this->variance_amount) . " ({$this->variance_percent}%)";
    }

    // Helper badge HTML untuk level
    public function getLevelBadge()
    {
        return match ($this->variance_level) {
            'critical' => '<span class="badge bg-danger">CRITICAL</span>',
            'warning' => '<span class="badge bg-warning text-dark">WARNING</span>',
            default => '<span class="badge bg-info">Minor</span>',
        };
    }
}
