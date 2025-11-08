<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Modules\Sale\Entities\Sale;
use Modules\Sale\Entities\ManualInputLog;

class OwnerNotification extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'owner_notifications';

    /**
     * Default attributes agar record baru konsisten.
     */
    protected $attributes = [
        'is_read' => false,
        'is_reviewed' => false,
        'severity' => 'info',
        'fontee_status' => null,
    ];

    /**
     * Tampilkan timestamp (epoch) untuk sorting cepat di FE.
     */
    protected $appends = ['created_at_ts'];

    public function getCreatedAtTsAttribute(): int
    {
        return $this->created_at?->timestamp ?? 0;
    }

    /**
     * Mass assignment.
     */
    protected $fillable = [
        'user_id',
        'sale_id',
        'notification_type',
        'title',
        'message',
        'data', // JSON payload
        'severity', // info|warning|critical (bebas, tapi di-badge-kan)
        'is_read',
        'read_at',
        'is_reviewed',
        'reviewed_at',
        'reviewed_by',
        'review_notes',
        'fontee_message_id',
        'fontee_status', // pending|sent|read|failed
        'fontee_sent_at',
        'fontee_error_message',
    ];

    /**
     * Casts.
     */
    protected $casts = [
        'data' => 'array',
        'is_read' => 'boolean',
        'is_reviewed' => 'boolean',
        'read_at' => 'datetime',
        'reviewed_at' => 'datetime',
        'fontee_sent_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /* ===========================
     * Relationships
     * ===========================
     */

    // Penerima notifikasi (Owner/Supervisor)
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function sale()
    {
        return $this->belongsTo(Sale::class);
    }

    // User yang menandai "reviewed"
    public function reviewer()
    {
        return $this->belongsTo(User::class, 'reviewed_by');
    }

    // Log manual input yang terkait (opsional)
    public function manualInputLogs()
    {
        return $this->hasMany(ManualInputLog::class, 'owner_notification_id');
    }

    /* ===========================
     * Scopes
     * ===========================
     */

    public function scopeUnread($query)
    {
        return $query->where('is_read', false);
    }

    public function scopeToday($query)
    {
        return $query->whereDate('created_at', today());
    }

    public function scopeReviewed($query, bool $state = true)
    {
        return $query->where('is_reviewed', $state ? 1 : 0);
    }

    public function scopeSeverity($query, string $level)
    {
        return $query->where('severity', $level);
    }

    public function scopeType($query, string $type)
    {
        return $query->where('notification_type', $type);
    }

    public function scopeForUser($query, int $userId)
    {
        return $query->where('user_id', $userId);
    }

    /* ===========================
     * Helpers - Read / Review
     * ===========================
     */

    public function markAsRead(): void
    {
        if (!$this->is_read) {
            $this->forceFill([
                'is_read' => true,
                'read_at' => now(),
            ])->save();
        }
    }

    public function markAsUnread(): void
    {
        if ($this->is_read) {
            $this->forceFill([
                'is_read' => false,
                'read_at' => null,
            ])->save();
        }
    }

    /**
     * Flexible signature:
     * - markAsReviewed($notes, $reviewerId)
     * - markAsReviewed($reviewerId, $notes)
     * - markAsReviewed($notes)  -> reviewer = auth()->id()
     */
    public function markAsReviewed($arg1 = null, $arg2 = null): void
    {
        $reviewerId = null;
        $notes = null;

        if (is_int($arg1) || (is_string($arg1) && ctype_digit($arg1))) {
            // ($reviewerId, $notes)
            $reviewerId = (int) $arg1;
            $notes = is_string($arg2) ? $arg2 : null;
        } else {
            // ($notes, $reviewerId)
            $notes = is_string($arg1) ? $arg1 : null;
            $reviewerId = $arg2 !== null ? (int) $arg2 : auth()->id() ?? null;
        }

        if (!$this->is_reviewed) {
            $this->forceFill([
                'is_reviewed' => true,
                'reviewed_at' => now(),
                'reviewed_by' => $reviewerId,
                'review_notes' => $notes,
            ])->save();
        } else {
            $this->forceFill([
                'reviewed_by' => $reviewerId,
                'review_notes' => $notes,
            ])->save();
        }
    }

    /* ===========================
     * Helpers - Fontee status
     * ===========================
     */

    public function markFonteeSent(string $messageId): void
    {
        $this->forceFill([
            'fontee_status' => 'sent',
            'fontee_message_id' => $messageId,
            'fontee_sent_at' => now(),
            'fontee_error_message' => null,
        ])->save();
    }

    public function markFonteeFailed(string $errorMessage): void
    {
        $this->forceFill([
            'fontee_status' => 'failed',
            'fontee_error_message' => $errorMessage,
        ])->save();
    }

    public function markFonteeRead(): void
    {
        $this->forceFill([
            'fontee_status' => 'read',
        ])->save();
    }

    /* ===========================
     * Presenters (untuk DataTables/Dropdown)
     * ===========================
     */

    public function getSeverityBadge(): string
    {
        $severity = strtolower((string) ($this->severity ?? 'info'));
        $color = $this->getSeverityColor();
        $label = ucfirst($severity);

        // label di-escape, color sudah whitelist dari getSeverityColor()
        return '<span class="badge bg-' . $color . '">' . htmlspecialchars($label, ENT_QUOTES, 'UTF-8') . '</span>';
    }

    public function getSeverityColor(): string
    {
        // Normalisasi ke lowercase agar input "CRITICAL", "Critical" tetap aman
        return match (strtolower((string) $this->severity)) {
            'critical' => 'danger',
            'warning' => 'warning',
            'info' => 'info',
            default => 'secondary',
        };
    }

    /* ===========================
     * Static helpers
     * ===========================
     */

    public static function getUnreadCount(int $userId): int
    {
        return static::forUser($userId)->unread()->count();
    }

    public static function markAllAsReadForUser(int $userId): void
    {
        static::forUser($userId)
            ->unread()
            ->update([
                'is_read' => true,
                'read_at' => now(),
            ]);
    }
}
