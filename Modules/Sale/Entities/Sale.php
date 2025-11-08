<?php

namespace Modules\Sale\Entities;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Sale extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'sales';

    /** Status & Payment status (hindari typo) */
    public const STATUS_DRAFT = 'Draft';
    public const STATUS_PENDING = 'Pending';
    public const STATUS_COMPLETED = 'Completed';

    public const PAY_UNPAID = 'Unpaid';
    public const PAY_PARTIAL = 'Partial';
    public const PAY_PAID = 'Paid';

    protected $fillable = ['reference', 'date', 'customer_name', 'status', 'payment_status', 'total_amount', 'paid_amount', 'due_amount', 'payment_method', 'bank_name', 'note', 'user_id', 'tax_percentage', 'tax_amount', 'discount_percentage', 'discount_amount', 'shipping_amount', 'total_hpp', 'total_profit', 'snap_token', 'midtrans_transaction_id', 'midtrans_payment_type', 'paid_at', 'has_price_adjustment', 'has_manual_input', 'manual_input_count', 'manual_input_summary', 'is_manual_input_notified', 'notified_at'];

    protected $casts = [
        'shipping_amount' => 'integer',
        'paid_amount' => 'integer',
        'total_amount' => 'integer',
        'due_amount' => 'integer',
        'tax_amount' => 'integer',
        'discount_amount' => 'integer',
        'tax_percentage' => 'integer',
        'discount_percentage' => 'integer',
        'total_hpp' => 'integer',
        'total_profit' => 'integer',
        'date' => 'datetime',
        'has_price_adjustment' => 'boolean',
        'has_manual_input' => 'boolean',
        'manual_input_count' => 'integer',
        'manual_input_summary' => 'array',
        'is_manual_input_notified' => 'boolean',
        'paid_at' => 'datetime',
        'notified_at' => 'datetime',
    ];

    /* ============================
     | Relationships
     |============================ */

    /** Detail item */
    public function saleDetails(): HasMany
    {
        return $this->hasMany(SaleDetails::class, 'sale_id');
    }

    /** Alias kompatibilitas */
    public function details(): HasMany
    {
        return $this->saleDetails();
    }

    /** Pembayaran (riwayat) */
    public function payments(): HasMany
    {
        return $this->hasMany(SalePayment::class, 'sale_id');
    }

    /** Alias kompatibilitas */
    public function salePayments(): HasMany
    {
        return $this->payments();
    }

    /** Pembayaran terakhir berdasar kolom `date` pada sale_payments */
    public function latestPayment(): HasOne
    {
        return $this->hasOne(SalePayment::class, 'sale_id')->latestOfMany('date');
    }

    /** Kasir / pembuat transaksi */
    public function user(): BelongsTo
    {
        return $this->belongsTo(\App\Models\User::class, 'user_id');
    }

    /* ============================
     | Query Scopes
     |============================ */

    /** Filter berdasarkan rentang tanggal (normalisasi hari) */
    public function scopeBetween(Builder $q, $from, $to): Builder
    {
        $from = Carbon::parse($from)->startOfDay();
        $to = Carbon::parse($to)->endOfDay();
        return $q->whereBetween('date', [$from, $to]);
    }

    /** Alias */
    public function scopeDatedBetween(Builder $q, $start, $end): Builder
    {
        return $q->whereBetween('date', [$start, $end]);
    }

    /** Hanya transaksi Completed */
    public function scopeCompleted(Builder $q): Builder
    {
        return $q->where('status', self::STATUS_COMPLETED)->whereNull('deleted_at');
    }

    /** Hanya transaksi yang punya input manual */
    public function scopeHasManualInput(Builder $q): Builder
    {
        return $q->where('has_manual_input', 1);
    }

    /** Hanya transaksi yang punya item edit harga */
    public function scopeHasPriceAdjustment(Builder $q): Builder
    {
        return $q->where('has_price_adjustment', 1);
    }

    /* ============================
     | Helpers
     |============================ */

    /**
     * Recalc paid_amount, due_amount, payment_status, status, & paid_at lalu save().
     * - paid_amount = sum(sale_payments.amount)
     * - due_amount  = max(total_amount - paid_amount, 0)
     * - payment_status: Paid | Partial | Unpaid
     * - status otomatis: Paid -> Completed; Partial -> Pending (jika Draft/Pending); Unpaid -> minimal Pending
     * - paid_at di-set saat menjadi Paid, di-null-kan bila mundur dari Paid
     */
    public function recalcPaymentAndStatus(): self
    {
        $paid = (int) $this->payments()->sum('amount');
        $this->paid_amount = $paid;
        $this->due_amount = max(0, (int) $this->total_amount - $paid);

        // Payment status
        if ($this->due_amount <= 0) {
            $newPayStatus = self::PAY_PAID;
        } elseif ($this->paid_amount > 0) {
            $newPayStatus = self::PAY_PARTIAL;
        } else {
            $newPayStatus = self::PAY_UNPAID;
        }
        $oldPayStatus = $this->payment_status;
        $this->payment_status = $newPayStatus;

        // Workflow status otomatis
        if ($this->payment_status === self::PAY_PAID) {
            $this->status = self::STATUS_COMPLETED;
        } elseif ($this->payment_status === self::PAY_PARTIAL && in_array($this->status, [self::STATUS_DRAFT, self::STATUS_PENDING], true)) {
            $this->status = self::STATUS_PENDING;
        } elseif ($this->payment_status === self::PAY_UNPAID && $this->status === self::STATUS_COMPLETED) {
            // downgrade jika refund penuh
            $this->status = self::STATUS_PENDING;
        }

        // paid_at handling
        if ($oldPayStatus !== self::PAY_PAID && $this->payment_status === self::PAY_PAID && empty($this->paid_at)) {
            $this->paid_at = now();
        }
        if ($oldPayStatus === self::PAY_PAID && $this->payment_status !== self::PAY_PAID) {
            // bila mundur dari Paid → kosongkan paid_at agar akurat
            $this->paid_at = null;
        }

        $this->save();
        return $this;
    }

    /**
     * Hitung ulang flag header (price adjustment & manual input) + counter lalu save().
     */
    public function refreshHeaderFlags(): self
    {
        $hasAdj = $this->saleDetails()->where('is_price_adjusted', 1)->exists();
        $miCount = (int) $this->saleDetails()->where('source_type', 'manual')->count();

        $this->has_price_adjustment = $hasAdj ? 1 : 0;
        $this->manual_input_count = $miCount;
        $this->has_manual_input = $miCount > 0 ? 1 : 0;

        $this->save();
        return $this;
    }

    /**
     * Ringkasan angka + format rupiah (untuk AJAX/UI ringan).
     */
    public function toMoneySummary(): array
    {
        $fmt = function ($n) {
            return function_exists('format_currency') ? format_currency($n, true) : number_format((int) $n, 0, ',', '.');
        };

        return [
            'total' => (int) $this->total_amount,
            'paid' => (int) $this->paid_amount,
            'due' => (int) $this->due_amount,
            'status' => (string) $this->payment_status,
            'total_formatted' => $fmt((int) $this->total_amount),
            'paid_formatted' => $fmt((int) $this->paid_amount),
            'due_formatted' => $fmt((int) $this->due_amount),
        ];
    }

    /* ============================
     | Boot
     |============================ */

    /** Auto-generate reference yang aman dari kosong (fallback bila helper tak ada). */
    protected static function boot()
    {
        parent::boot();

        static::creating(function (self $model) {
            if (empty($model->reference)) {
                $next = (int) (self::max('id') ?? 0) + 1;
                if (function_exists('make_reference_id')) {
                    $model->reference = make_reference_id('OB2', $next);
                } else {
                    // fallback sederhana tapi unik
                    $model->reference = 'OB2-' . now()->format('Ymd') . '-' . str_pad((string) $next, 6, '0', STR_PAD_LEFT);
                }
            }
        });
    }
}
