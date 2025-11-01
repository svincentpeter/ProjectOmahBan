<?php

// ✅ FILE: Modules/Adjustment/Entities/Adjustment.php

namespace Modules\Adjustment\Entities;

use Illuminate\Database\Eloquent\Model;
use App\Models\User;

class Adjustment extends Model
{
    // ❌ HAPUS: use SoftDeletes; - Column tidak ada di DB!

    protected $table = 'adjustments';
    protected $guarded = [];
    protected $casts = [
        'date' => 'date',
        'approval_date' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    // ✅ RELASI: Produk yang disesuaikan
    public function adjustedProducts()
    {
        return $this->hasMany(AdjustedProduct::class, 'adjustment_id');
    }

    // ✅ RELASI: File/Foto bukti
    public function adjustmentFiles()
    {
        return $this->hasMany(AdjustmentFile::class, 'adjustment_id');
    }

    // ✅ RELASI: Riwayat perubahan
    public function logs()
    {
        return $this->hasMany(AdjustmentLog::class, 'adjustment_id');
    }

    // ✅ RELASI: User yang membuat (Kasir/Requester)
    public function requester()
    {
        return $this->belongsTo(User::class, 'requester_id');
    }

    // ✅ RELASI: User yang approve (Admin/Supervisor)
    public function approver()
    {
        return $this->belongsTo(User::class, 'approver_id');
    }

    // ✅ SCOPE: Filter pending adjustments
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    // ✅ SCOPE: Filter approved adjustments
    public function scopeApproved($query)
    {
        return $query->where('status', 'approved');
    }

    // ✅ SCOPE: Filter rejected adjustments
    public function scopeRejected($query)
    {
        return $query->where('status', 'rejected');
    }
}
