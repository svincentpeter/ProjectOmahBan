<?php

namespace Modules\Adjustment\Entities;

use Illuminate\Database\Eloquent\Model;
use App\Models\User;

class AdjustmentLog extends Model
{
    protected $table = 'adjustment_logs';
    protected $guarded = [];

    public const UPDATED_AT = null;
    public const CREATED_AT = 'created_at';
    public $timestamps = true;

    protected $casts = [
        'created_at' => 'datetime',
    ];

    // ✅ Relasi
    public function adjustment()
    {
        return $this->belongsTo(Adjustment::class, 'adjustment_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
