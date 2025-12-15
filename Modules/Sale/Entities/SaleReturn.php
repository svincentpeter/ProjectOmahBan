<?php

namespace Modules\Sale\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Modules\People\Entities\Customer;
use App\Models\User;

class SaleReturn extends Model
{
    use SoftDeletes;

    protected $table = 'sale_returns';

    // Status constants
    public const STATUS_PENDING = 'Pending';
    public const STATUS_APPROVED = 'Approved';
    public const STATUS_REJECTED = 'Rejected';
    public const STATUS_COMPLETED = 'Completed';

    // Refund method constants
    public const REFUND_CASH = 'Cash';
    public const REFUND_CREDIT = 'Credit';
    public const REFUND_STORE_CREDIT = 'Store Credit';

    protected $fillable = [
        'reference',
        'sale_id',
        'customer_id',
        'date',
        'status',
        'total_amount',
        'refund_amount',
        'refund_method',
        'reason',
        'note',
        'created_by',
        'approved_by',
        'approved_at',
    ];

    protected $casts = [
        'date' => 'date',
        'total_amount' => 'integer',
        'refund_amount' => 'integer',
        'approved_at' => 'datetime',
    ];

    /* ============================
     | Relationships
     |============================ */

    /**
     * The original sale being returned
     */
    public function sale(): BelongsTo
    {
        return $this->belongsTo(Sale::class, 'sale_id');
    }

    /**
     * Customer (optional, inherited from sale)
     */
    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class, 'customer_id');
    }

    /**
     * User who created the return
     */
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * User who approved/rejected the return
     */
    public function approver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    /**
     * Return detail items
     */
    public function details(): HasMany
    {
        return $this->hasMany(SaleReturnDetail::class, 'sale_return_id');
    }

    /**
     * Alias for details
     */
    public function saleReturnDetails(): HasMany
    {
        return $this->details();
    }

    /* ============================
     | Scopes
     |============================ */

    public function scopePending($query)
    {
        return $query->where('status', self::STATUS_PENDING);
    }

    public function scopeApproved($query)
    {
        return $query->where('status', self::STATUS_APPROVED);
    }

    public function scopeCompleted($query)
    {
        return $query->where('status', self::STATUS_COMPLETED);
    }

    /* ============================
     | Accessors
     |============================ */

    /**
     * Get customer display name
     */
    public function getCustomerDisplayNameAttribute(): string
    {
        if ($this->customer) {
            return (string) $this->customer->customer_name;
        }
        
        if ($this->sale && $this->sale->customer) {
            return (string) $this->sale->customer->customer_name;
        }

        return 'Guest';
    }

    /**
     * Get status badge class for UI
     */
    public function getStatusBadgeClassAttribute(): string
    {
        return match($this->status) {
            self::STATUS_PENDING => 'bg-yellow-100 text-yellow-800',
            self::STATUS_APPROVED => 'bg-blue-100 text-blue-800',
            self::STATUS_REJECTED => 'bg-red-100 text-red-800',
            self::STATUS_COMPLETED => 'bg-green-100 text-green-800',
            default => 'bg-gray-100 text-gray-800',
        };
    }

    /* ============================
     | Helpers
     |============================ */

    /**
     * Recalculate total amount from details
     */
    public function recalculateTotals(): self
    {
        $this->total_amount = (int) $this->details()->sum('sub_total');
        $this->refund_amount = $this->total_amount; // Can be adjusted manually
        $this->save();
        return $this;
    }

    /**
     * Check if return can be approved
     */
    public function canBeApproved(): bool
    {
        return $this->status === self::STATUS_PENDING;
    }

    /**
     * Check if return can be rejected
     */
    public function canBeRejected(): bool
    {
        return $this->status === self::STATUS_PENDING;
    }

    /* ============================
     | Boot
     |============================ */

    protected static function boot()
    {
        parent::boot();

        static::creating(function (self $model) {
            if (empty($model->reference)) {
                $next = (int) (self::withTrashed()->max('id') ?? 0) + 1;
                $model->reference = 'RTN-' . now()->format('Ymd') . '-' . str_pad((string) $next, 5, '0', STR_PAD_LEFT);
            }

            if (empty($model->created_by)) {
                $model->created_by = auth()->id();
            }

            if (empty($model->date)) {
                $model->date = now();
            }

            if (empty($model->status)) {
                $model->status = self::STATUS_PENDING;
            }
        });
    }
}
