<?php

namespace Modules\Adjustment\Entities;

use Illuminate\Database\Eloquent\Model;
use App\Models\User;

class AdjustmentLog extends Model
{
    protected $table = 'adjustment_logs';

    protected $fillable = [
        'adjustment_id',
        'user_id',
        'action', // created|update|approved|rejected|delete ...
        'old_status',
        'new_status',
        'notes',
        'locked',
        'created_at',
    ];

    public const UPDATED_AT = null;
    public const CREATED_AT = 'created_at';
    public $timestamps = true;

    protected $casts = [
        'created_at' => 'datetime',
        'locked' => 'boolean',
    ];

    /** -------------------- RELATIONS -------------------- */
    public function adjustment()
    {
        return $this->belongsTo(Adjustment::class, 'adjustment_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /** -------------------- SCOPES -------------------- */
    public function scopeByAdjustment($q, int $adjustmentId)
    {
        return $q->where('adjustment_id', $adjustmentId);
    }

    public function scopeRecent($q, int $limit = 50)
    {
        return $q->latest('created_at')->limit($limit);
    }

    /** -------------------- ACCESSORS -------------------- */
    public function getActionBadgeAttribute(): string
    {
        $map = [
            'created' => ['Created', 'info'],
            'update' => ['Updated', 'secondary'],
            'approved' => ['Approved', 'success'],
            'rejected' => ['Rejected', 'danger'],
            'delete' => ['Deleted', 'warning'],
        ];
        [$text, $variant] = $map[$this->action] ?? [ucfirst($this->action ?? 'Log'), 'light'];
        return sprintf('<span class="badge badge-%s">%s</span>', $variant, e($text));
    }

    protected $appends = ['action_badge'];
}
