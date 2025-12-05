<?php

namespace Modules\Adjustment\Entities;

use Illuminate\Database\Eloquent\Model;
use Modules\Product\Entities\Product;
use app\Models\User;

class StockOpnameItem extends Model
{
    protected $fillable = [
        'stock_opname_id', 'product_id', 'system_qty', 'actual_qty',
        'variance_reason', 'notes', 'counted_at', 'counted_by', 'adjustment_id'
    ];

    protected $casts = [
        'system_qty' => 'integer',
        'actual_qty' => 'integer',
        'counted_at' => 'datetime',
    ];

    // Computed columns (dari GENERATED ALWAYS AS)
    protected $appends = ['variance_qty', 'variance_type'];

    // -------------------- RELATIONS --------------------
    
    public function stockOpname()
    {
        return $this->belongsTo(StockOpname::class, 'stock_opname_id');
    }

    public function product()
    {
        return $this->belongsTo(Product::class, 'product_id');
    }

    public function counter()
    {
        return $this->belongsTo(User::class, 'counted_by');
    }

    public function adjustment()
    {
        return $this->belongsTo(Adjustment::class, 'adjustment_id');
    }

    // -------------------- ACCESSORS --------------------
    
    public function getVarianceQtyAttribute()
    {
        return $this->actual_qty !== null 
            ? ($this->actual_qty - $this->system_qty) 
            : null;
    }

    public function getVarianceTypeAttribute(): string
    {
        if ($this->actual_qty === null) return 'pending';
        if ($this->actual_qty > $this->system_qty) return 'surplus';
        if ($this->actual_qty < $this->system_qty) return 'shortage';
        return 'match';
    }

    public function getVarianceColorAttribute(): string
    {
        return match($this->variance_type) {
            'surplus' => 'success',
            'shortage' => 'danger',
            'match' => 'secondary',
            default => 'warning'
        };
    }
}
