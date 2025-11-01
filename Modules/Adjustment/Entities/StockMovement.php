<?php

// ✅ FILE: Modules/Adjustment/Entities/StockMovement.php

namespace Modules\Adjustment\Entities;

use Illuminate\Database\Eloquent\Model;
use Modules\Product\Entities\Product;

class StockMovement extends Model
{
    protected $table = 'stock_movements';
    protected $guarded = [];
    public $timestamps = true;

    // ✅ RELASI: Produk yang bergerak stoknya
    public function product()
    {
        return $this->belongsTo(Product::class, 'product_id');
    }

    // ✅ RELASI: Adjustment yang menyebabkan movement
    public function adjustment()
    {
        return $this->belongsTo(Adjustment::class, 'adjustment_id');
    }
}
