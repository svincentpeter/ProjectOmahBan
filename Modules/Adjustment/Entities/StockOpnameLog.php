<?php

namespace Modules\Adjustment\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\User;

/**
 * Model StockOpnameLog
 * 
 * Audit trail untuk setiap aktivitas pada stock opname.
 * Mencatat siapa, kapan, dan apa yang dilakukan.
 * 
 * @property int $id
 * @property int $stock_opname_id
 * @property int|null $user_id
 * @property string $action (created, started, item_counted, completed, approved, cancelled, etc.)
 * @property string|null $old_status
 * @property string|null $new_status
 * @property string|null $description
 * @property \Carbon\Carbon $created_at
 * 
 * @property-read StockOpname $stockOpname
 * @property-read User|null $user
 */
class StockOpnameLog extends Model
{
    use SoftDeletes;

    /**
     * Table name
     */
    protected $table = 'stock_opname_logs';

    /**
     * Mass assignable attributes
     */
    protected $fillable = [
        'stock_opname_id',
        'user_id',
        'action',
        'old_status',
        'new_status',
        'description',
    ];

    /**
     * Attributes yang tidak boleh diubah setelah dibuat
     */
    protected $guarded = [
        'id',
        'created_at',
    ];

    /**
     * Casting tipe data
     */
    protected $casts = [
        'stock_opname_id' => 'integer',
        'user_id' => 'integer',
        'created_at' => 'datetime',
    ];

    /**
     * Disable updated_at karena log sifatnya immutable (tidak boleh diubah)
     */
    public $timestamps = true;
    const UPDATED_AT = null; // Tidak pakai updated_at

    /**
     * Appends - accessor yang otomatis dimuat
     */
    protected $appends = [
        'action_label',
        'action_icon',
        'time_ago',
    ];

    // ================================================================
    // CONSTANTS - Daftar action yang valid
    // ================================================================

    const ACTION_CREATED = 'created';
    const ACTION_STARTED = 'started';
    const ACTION_ITEM_COUNTED = 'item_counted';
    const ACTION_ITEM_UPDATED = 'item_updated';
    const ACTION_COMPLETED = 'completed';
    const ACTION_APPROVED = 'approved';
    const ACTION_REJECTED = 'rejected';
    const ACTION_CANCELLED = 'cancelled';
    const ACTION_DELETED = 'deleted';
    const ACTION_ADJUSTMENT_GENERATED = 'adjustment_generated';

    /**
     * Get all valid actions
     */
    public static function getValidActions(): array
    {
        return [
            self::ACTION_CREATED,
            self::ACTION_STARTED,
            self::ACTION_ITEM_COUNTED,
            self::ACTION_ITEM_UPDATED,
            self::ACTION_COMPLETED,
            self::ACTION_APPROVED,
            self::ACTION_REJECTED,
            self::ACTION_CANCELLED,
            self::ACTION_DELETED,
            self::ACTION_ADJUSTMENT_GENERATED,
        ];
    }

    // ================================================================
    // RELATIONS
    // ================================================================

    /**
     * Relasi ke Stock Opname (parent)
     */
    public function stockOpname()
    {
        return $this->belongsTo(StockOpname::class, 'stock_opname_id');
    }

    /**
     * Relasi ke User yang melakukan action
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    // ================================================================
    // SCOPES
    // ================================================================

    /**
     * Scope: Filter by stock opname ID
     */
    public function scopeByStockOpname($query, int $stockOpnameId)
    {
        return $query->where('stock_opname_id', $stockOpnameId);
    }

    /**
     * Scope: Filter by action
     */
    public function scopeByAction($query, string $action)
    {
        return $query->where('action', $action);
    }

    /**
     * Scope: Filter by user
     */
    public function scopeByUser($query, int $userId)
    {
        return $query->where('user_id', $userId);
    }

    /**
     * Scope: Latest first (descending)
     */
    public function scopeLatestFirst($query)
    {
        return $query->orderBy('created_at', 'desc');
    }

    /**
     * Scope: Oldest first (ascending)
     */
    public function scopeOldestFirst($query)
    {
        return $query->orderBy('created_at', 'asc');
    }

    /**
     * Scope: Filter by date range
     */
    public function scopeBetweenDates($query, $startDate, $endDate)
    {
        return $query->whereBetween('created_at', [$startDate, $endDate]);
    }

    /**
     * Scope: Today's logs
     */
    public function scopeToday($query)
    {
        return $query->whereDate('created_at', today());
    }

    // ================================================================
    // ACCESSORS
    // ================================================================

    /**
     * Get human-readable action label
     */
    public function getActionLabelAttribute(): string
    {
        $labels = [
            self::ACTION_CREATED => 'Dibuat',
            self::ACTION_STARTED => 'Dimulai',
            self::ACTION_ITEM_COUNTED => 'Item Dihitung',
            self::ACTION_ITEM_UPDATED => 'Item Diupdate',
            self::ACTION_COMPLETED => 'Selesai',
            self::ACTION_APPROVED => 'Disetujui',
            self::ACTION_REJECTED => 'Ditolak',
            self::ACTION_CANCELLED => 'Dibatalkan',
            self::ACTION_DELETED => 'Dihapus',
            self::ACTION_ADJUSTMENT_GENERATED => 'Adjustment Dibuat',
        ];

        return $labels[$this->action] ?? ucfirst($this->action);
    }

    /**
     * Get icon for action (Bootstrap Icons)
     */
    public function getActionIconAttribute(): string
    {
        $icons = [
            self::ACTION_CREATED => 'bi-plus-circle text-success',
            self::ACTION_STARTED => 'bi-play-circle text-info',
            self::ACTION_ITEM_COUNTED => 'bi-check2-circle text-primary',
            self::ACTION_ITEM_UPDATED => 'bi-arrow-repeat text-warning',
            self::ACTION_COMPLETED => 'bi-check-circle-fill text-success',
            self::ACTION_APPROVED => 'bi-shield-check text-success',
            self::ACTION_REJECTED => 'bi-x-circle text-danger',
            self::ACTION_CANCELLED => 'bi-slash-circle text-secondary',
            self::ACTION_DELETED => 'bi-trash text-danger',
            self::ACTION_ADJUSTMENT_GENERATED => 'bi-clipboard-check text-info',
        ];

        return $icons[$this->action] ?? 'bi-circle text-secondary';
    }

    /**
     * Get time ago (human readable)
     */
    public function getTimeAgoAttribute(): string
    {
        if (!$this->created_at) {
            return '-';
        }

        return $this->created_at->diffForHumans();
    }

    /**
     * Get formatted created date
     */
    public function getFormattedDateAttribute(): string
    {
        if (!$this->created_at) {
            return '-';
        }

        return $this->created_at->format('d/m/Y H:i:s');
    }

    /**
     * Get user name (null-safe)
     */
    public function getUserNameAttribute(): string
    {
        return $this->user ? $this->user->name : 'System';
    }

    /**
     * Get status transition (jika ada old & new status)
     */
    public function getStatusTransitionAttribute(): ?string
    {
        if ($this->old_status && $this->new_status) {
            return strtoupper($this->old_status) . ' → ' . strtoupper($this->new_status);
        }

        return null;
    }

    // ================================================================
    // HELPERS / STATIC METHODS
    // ================================================================

    /**
     * Log activity (helper method untuk create log dengan mudah)
     * 
     * @param int $stockOpnameId
     * @param string $action
     * @param array $data (optional: old_status, new_status, description, user_id)
     * @return self
     */
    public static function logActivity(int $stockOpnameId, string $action, array $data = []): self
    {
        return self::create([
            'stock_opname_id' => $stockOpnameId,
            'user_id' => $data['user_id'] ?? auth()->id(),
            'action' => $action,
            'old_status' => $data['old_status'] ?? null,
            'new_status' => $data['new_status'] ?? null,
            'description' => $data['description'] ?? null,
        ]);
    }

    /**
     * Get timeline untuk stock opname tertentu
     * 
     * @param int $stockOpnameId
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public static function getTimeline(int $stockOpnameId)
    {
        return self::with('user')
            ->where('stock_opname_id', $stockOpnameId)
            ->orderBy('created_at', 'desc')
            ->get();
    }

    /**
     * Get activity summary untuk stock opname
     * 
     * @param int $stockOpnameId
     * @return array
     */
    public static function getActivitySummary(int $stockOpnameId): array
    {
        $logs = self::where('stock_opname_id', $stockOpnameId)->get();

        return [
            'total_activities' => $logs->count(),
            'items_counted' => $logs->where('action', self::ACTION_ITEM_COUNTED)->count(),
            'items_updated' => $logs->where('action', self::ACTION_ITEM_UPDATED)->count(),
            'last_activity' => $logs->sortByDesc('created_at')->first(),
            'first_activity' => $logs->sortBy('created_at')->first(),
            'unique_users' => $logs->pluck('user_id')->unique()->count(),
        ];
    }

    /**
     * Check if action is valid
     */
    public static function isValidAction(string $action): bool
    {
        return in_array($action, self::getValidActions(), true);
    }

    // ================================================================
    // EVENTS / OBSERVERS (Optional)
    // ================================================================

    /**
     * Boot method untuk event handling
     */
    protected static function booted()
    {
        // Validasi action saat creating
        static::creating(function (self $log) {
            if (!self::isValidAction($log->action)) {
                throw new \InvalidArgumentException(
                    "Invalid action: {$log->action}. Valid actions: " . 
                    implode(', ', self::getValidActions())
                );
            }

            // Set user_id otomatis jika kosong
            if (!$log->user_id && auth()->check()) {
                $log->user_id = auth()->id();
            }
        });
    }

    // ================================================================
    // MUTATORS
    // ================================================================

    /**
     * Set action (ensure lowercase)
     */
    public function setActionAttribute($value)
    {
        $this->attributes['action'] = strtolower($value);
    }

    /**
     * Set description (trim whitespace)
     */
    public function setDescriptionAttribute($value)
    {
        $this->attributes['description'] = $value ? trim($value) : null;
    }
}
