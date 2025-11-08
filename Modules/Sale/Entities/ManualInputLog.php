<?php

namespace Modules\Sale\Entities;

use Illuminate\Database\Eloquent\Model;
use Modules\Sale\Entities\Sale;
use Modules\Sale\Entities\SaleDetails;
use App\Models\User;
use App\Models\OwnerNotification;
use App\Models\ManualInputDetail;

class ManualInputLog extends Model
{
    protected $table = 'manual_input_logs';

    // === IZINKAN MASS ASSIGN SESUAI KOLOM DI DATABASE ===
    protected $fillable = [
        'sale_id',
        'sale_detail_id',
        'cashier_id',

        'input_type', // 'manual_item','manual_service','price_override','discount_applied'
        'item_name',

        // -- Harga & deviasi (SAMAKAN DENGAN SKEMA DB) --
        'standard_price', // <- di DB: standard_price
        'input_price',
        'price_variance', // <- di DB: price_variance
        'variance_percent',

        // -- Wajib ada agar error kamu hilang --
        'quantity',

        // -- Alasan & approval --
        'reason_provided',
        'supervisor_pin_required',
        'supervisor_id',
        'approval_status', // pending|approved|rejected
        'approval_notes',
        'approved_by',
        'approved_at',

        // -- Notifikasi owner --
        'owner_notified',
        'owner_notification_id',
        'owner_notified_at',

        // Opsional: kalau suatu saat kamu tambahkan kolom ini di DB
        // 'manual_input_detail_id',
    ];

    protected $casts = [
        'quantity' => 'integer',
        'standard_price' => 'integer',
        'input_price' => 'integer',
        'price_variance' => 'integer',
        'variance_percent' => 'float',

        'supervisor_pin_required' => 'boolean',
        'owner_notified' => 'boolean',

        'approved_at' => 'datetime',
        'owner_notified_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    // =============== RELATIONSHIPS ===============
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

    public function approver()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function manualInputDetail()
    {
        // NOTE: hanya aktif kalau kolom 'manual_input_detail_id' memang ada di DB
        return $this->belongsTo(ManualInputDetail::class, 'manual_input_detail_id');
    }

    public function ownerNotification()
    {
        return $this->belongsTo(OwnerNotification::class, 'owner_notification_id');
    }

    // =============== SCOPES ===============
    public function scopeManualInput($query)
    {
        return $query->whereIn('input_type', ['manual_item', 'manual_service']);
    }

    public function scopePriceEdit($query)
    {
        return $query->where('input_type', 'price_override');
    }

    public function scopeUnnotified($query)
    {
        return $query->where(function ($q) {
            $q->whereNull('owner_notified')->orWhere('owner_notified', false);
        });
    }

    public function scopeUnapproved($query)
    {
        return $query->where('approval_status', 'pending');
    }

    public function scopeApproved($query)
    {
        return $query->where('approval_status', 'approved');
    }

    public function scopeCritical($query)
    {
        return $query->where('variance_percent', '>=', 20); // contoh kriteria
    }

    // =============== HELPERS ===============
    public function getLevelBadge(): string
    {
        // contoh sederhana dari variance_percent
        $level = $this->variance_percent >= 20 ? 'critical' : ($this->variance_percent >= 10 ? 'warning' : 'info');
        return match ($level) {
            'critical' => '<span class="badge bg-danger">🚨 CRITICAL</span>',
            'warning' => '<span class="badge bg-warning text-dark">⚠️ WARNING</span>',
            default => '<span class="badge bg-info">ℹ️ Minor</span>',
        };
    }

    public function linkToNotification(OwnerNotification $notification): self
    {
        if ($this->owner_notification_id === $notification->id && $this->owner_notified) {
            return $this;
        }

        $this->owner_notification_id = $notification->id;
        $this->owner_notified = true;
        $this->owner_notified_at = now();
        $this->save();

        return $this;
    }

    public function isPending(): bool
    {
        return $this->approval_status === 'pending';
    }

    public function isApproved(): bool
    {
        return $this->approval_status === 'approved';
    }

    public function isRejected(): bool
    {
        return $this->approval_status === 'rejected';
    }

    public function approve(User $approver, ?string $notes = null): void
    {
        $this->update([
            'approval_status' => 'approved',
            'approved_by' => $approver->id,
            'approved_at' => now(),
        ]);
    }

    public function reject(User $rejector, ?string $notes = null): void
    {
        $this->update([
            'approval_status' => 'rejected',
            'approved_by' => $rejector->id,
            'approved_at' => now(),
        ]);
    }
}
