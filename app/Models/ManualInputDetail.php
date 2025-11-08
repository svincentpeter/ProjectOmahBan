<?php namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Modules\Sale\Entities\Sale;
use Modules\Sale\Entities\SaleDetails;

class ManualInputDetail extends Model
{
    protected $table = 'manual_input_details';
    protected $fillable = ['sale_id', 'sale_detail_id', 'cashier_id', 'item_type', 'item_name', 'quantity', 'price', 'manual_reason', 'cost_price'];
    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    // ✅ Relationships
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
        return $this->belongsTo(User::class, 'cashier_id');
    }
}
