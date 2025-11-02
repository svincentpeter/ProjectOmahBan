<?php

namespace Modules\Adjustment\Entities;

use Illuminate\Database\Eloquent\Model;
use App\Models\User;

class Adjustment extends Model
{
    protected $table = 'adjustments';

    // Lebih eksplisit daripada guarded=[]
    protected $fillable = ['date', 'reference', 'note', 'reason', 'description', 'status', 'requester_id', 'approver_id', 'approval_notes', 'approval_date', 'created_at', 'updated_at'];

    protected $casts = [
        'date' => 'date',
        'approval_date' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /** -------------------- RELATIONS -------------------- */
    public function adjustedProducts()
    {
        return $this->hasMany(AdjustedProduct::class, 'adjustment_id');
    }

    public function adjustmentFiles()
    {
        return $this->hasMany(AdjustmentFile::class, 'adjustment_id');
    }

    public function logs()
    {
        return $this->hasMany(AdjustmentLog::class, 'adjustment_id');
    }

    public function requester()
    {
        return $this->belongsTo(User::class, 'requester_id');
    }

    public function approver()
    {
        return $this->belongsTo(User::class, 'approver_id');
    }

    /** -------------------- SCOPES -------------------- */
    public function scopePending($q)
    {
        return $q->where('status', 'pending');
    }
    public function scopeApproved($q)
    {
        return $q->where('status', 'approved');
    }
    public function scopeRejected($q)
    {
        return $q->where('status', 'rejected');
    }

    // NEW: filter berdasarkan requester (default: user saat ini)
    public function scopeByRequester($q, ?int $userId = null)
    {
        $userId = $userId ?: auth()->id();
        return $q->where('requester_id', $userId);
    }

    /** -------------------- ACCESSORS -------------------- */

    // NEW: badge HTML untuk status (dipakai di table/list)
    public function getStatusBadgeAttribute(): string
    {
        $map = [
            'pending' => ['text' => 'Pending', 'class' => 'badge badge-warning'],
            'approved' => ['text' => 'Approved', 'class' => 'badge badge-success'],
            'rejected' => ['text' => 'Rejected', 'class' => 'badge badge-danger'],
        ];
        $conf = $map[$this->status] ?? ['text' => ucfirst($this->status ?? 'Unknown'), 'class' => 'badge badge-secondary'];
        return sprintf('<span class="%s">%s</span>', $conf['class'], e($conf['text']));
    }

    // NEW: total item/baris produk pada adjustment
    public function getTotalProductsAttribute(): int
    {
        // aman: kalau relasi belum dimuat, hitung cepat via count()
        return $this->relationLoaded('adjustedProducts') ? $this->adjustedProducts->count() : $this->adjustedProducts()->count();
    }

    // Opsional: jika mau otomatis ikut diserialisasi
    protected $appends = ['status_badge', 'total_products'];
}
