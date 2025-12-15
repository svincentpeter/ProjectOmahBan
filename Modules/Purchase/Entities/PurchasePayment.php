<?php

namespace Modules\Purchase\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Carbon\Carbon;

class PurchasePayment extends Model
{
    use SoftDeletes;

    protected $table = 'purchase_payments';

    protected $fillable = [
        'purchase_id',
        'reference',
        'amount',
        'date',
        'payment_method',
        'bank_name',
        'note',
        'user_id',
    ];

    protected $casts = [
        'amount' => 'integer',
        'date' => 'date',
    ];

    /* ============================
     | Relationships
     |============================ */

    /**
     * The purchase this payment belongs to
     */
    public function purchase(): BelongsTo
    {
        return $this->belongsTo(Purchase::class, 'purchase_id');
    }

    /**
     * User who recorded the payment
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(\App\Models\User::class, 'user_id');
    }

    /* ============================
     | Boot
     |============================ */

    protected static function boot()
    {
        parent::boot();

        static::creating(function (self $model) {
            // Auto-generate reference
            if (empty($model->reference)) {
                $model->reference = static::generateReference();
            }

            // Default user
            if (empty($model->user_id)) {
                $model->user_id = auth()->id();
            }

            // Default date
            if (empty($model->date)) {
                $model->date = now();
            }
        });

        // After saving, update parent purchase totals
        static::saved(function (self $model) {
            if ($model->purchase) {
                $model->purchase->recalcPaymentStatus();
            }
        });

        static::deleted(function (self $model) {
            if ($model->purchase) {
                $model->purchase->recalcPaymentStatus();
            }
        });
    }

    /**
     * Generate unique reference number
     * Format: PP-YYYYMMDD-HHMMSS-XXXXX
     */
    public static function generateReference(): string
    {
        $prefix = 'PP-' . now()->format('Ymd-His') . '-';
        $random = str_pad((string) mt_rand(1, 99999), 5, '0', STR_PAD_LEFT);
        return $prefix . $random;
    }

    /* ============================
     | Accessors
     |============================ */

    /**
     * Get formatted amount
     */
    public function getFormattedAmountAttribute(): string
    {
        return format_currency($this->amount);
    }

    /**
     * Get formatted date
     */
    public function getFormattedDateAttribute(): string
    {
        return $this->date ? Carbon::parse($this->date)->format('d M Y') : '-';
    }
}
