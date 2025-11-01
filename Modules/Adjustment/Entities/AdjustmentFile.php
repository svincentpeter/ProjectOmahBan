<?php

// ✅ FILE: Modules/Adjustment/Entities/AdjustmentFile.php

namespace Modules\Adjustment\Entities;

use Illuminate\Database\Eloquent\Model;

class AdjustmentFile extends Model
{
    protected $table = 'adjustment_files';
    protected $guarded = [];
    public $timestamps = true;

    // ✅ RELASI: Kembali ke Adjustment
    public function adjustment()
    {
        return $this->belongsTo(Adjustment::class, 'adjustment_id');
    }

    // ✅ Mutator: Akses file url
    public function getFileUrlAttribute()
    {
        return asset('storage/' . $this->file_path);
    }
}
