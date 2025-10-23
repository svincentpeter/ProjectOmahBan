<?php

namespace Modules\Sale\Entities;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Sale extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'sales';

    // 👇 REVISI: Tambahkan customer_name dan kolom lain yang diperlukan
    protected $fillable = [
        'reference',
        'date',
        'customer_name', // 👈 TAMBAHAN: Agar customer_name bisa disimpan
        'status', // Draft | Pending | Completed
        'payment_status', // Unpaid | Partial | Paid
        'total_amount',
        'paid_amount',
        'due_amount',
        'payment_method', // info default di header
        'bank_name', // opsional (di header)
        'note',
        'user_id',
        'tax_percentage', // 👈 TAMBAHAN: Agar bisa simpan tax dari POS
        'tax_amount', // 👈 TAMBAHAN
        'discount_percentage', // 👈 TAMBAHAN: Agar bisa simpan discount dari POS
        'discount_amount', // 👈 TAMBAHAN
        'shipping_amount', // 👈 TAMBAHAN: Agar bisa simpan ongkir
        'total_hpp', // 👈 TAMBAHAN: Harga Pokok Penjualan
        'total_profit', // 👈 TAMBAHAN: Profit per transaksi
        'snap_token',
        'midtrans_transaction_id',
        'midtrans_payment_type',
        'paid_at',
    ];

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
    ];

    /* =========================================================
    | RELATIONSHIPS
    |=========================================================*/

    /**
     * Detail item (relasi hasMany ke SaleDetails)
     */
    public function saleDetails(): HasMany
    {
        return $this->hasMany(SaleDetails::class, 'sale_id');
    }

    /**
     * Alias untuk saleDetails (kompatibilitas kode lama)
     */
    public function details(): HasMany
    {
        return $this->hasMany(SaleDetails::class, 'sale_id');
    }

    /**
     * Relasi ke pembayaran (payments)
     */
    public function payments(): HasMany
    {
        return $this->hasMany(SalePayment::class, 'sale_id');
    }

    /**
     * Alias untuk payments (kompatibilitas kode lama)
     */
    public function salePayments(): HasMany
    {
        return $this->hasMany(\Modules\Sale\Entities\SalePayment::class, 'sale_id');
    }

    /**
     * Pembayaran terakhir
     */
    public function latestPayment()
    {
        return $this->hasOne(\Modules\Sale\Entities\SalePayment::class, 'sale_id')->latestOfMany('date');
    }

    /**
     * Relasi ke user (kasir yang membuat transaksi)
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(\App\Models\User::class, 'user_id');
    }

    /* =========================================================
    | QUERY SCOPES
    |=========================================================*/

    /**
     * Filter sale berdasarkan rentang tanggal
     */
    public function scopeBetween(Builder $q, $from, $to): Builder
    {
        $from = Carbon::parse($from)->startOfDay();
        $to = Carbon::parse($to)->endOfDay();
        return $q->whereBetween('date', [$from, $to]);
    }

    /**
     * Alias untuk scopeBetween
     */
    public function scopeDatedBetween(Builder $q, $start, $end): Builder
    {
        return $q->whereBetween('date', [$start, $end]);
    }

    /**
     * Filter hanya sale yang sudah completed
     */
    public function scopeCompleted(Builder $q): Builder
    {
        return $q->where('status', 'Completed')->whereNull('deleted_at');
    }

    /* =========================================================
    | HELPERS
    |=========================================================*/

    /**
     * Hitung ulang paid_amount, due_amount, payment_status,
     * dan otomatis mapping workflow status (Draft/Pending/Completed),
     * lalu save().
     */
    public function recalcPaymentAndStatus(): self
    {
        $paid = (int) $this->payments()->sum('amount');
        $this->paid_amount = $paid;
        $this->due_amount = max(0, (int) $this->total_amount - $paid);

        // Payment status
        if ($this->due_amount <= 0) {
            $this->payment_status = 'Paid';
        } elseif ($this->paid_amount > 0) {
            $this->payment_status = 'Partial';
        } else {
            $this->payment_status = 'Unpaid';
        }

        // Workflow status otomatis
        if ($this->payment_status === 'Paid') {
            $this->status = 'Completed';
        } elseif ($this->payment_status === 'Partial' && in_array($this->status, ['Draft', 'Pending'])) {
            $this->status = 'Pending';
        } elseif ($this->payment_status === 'Unpaid' && $this->status === 'Completed') {
            // downgrade jika refund penuh
            $this->status = 'Pending';
        }

        $this->save();
        return $this;
    }

    /**
     * Ringkasan angka + format rupiah untuk frontend/AJAX.
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

    /* =========================================================
    | BOOT METHOD (Auto-generate reference)
    |=========================================================*/

    /**
     * Method ini akan berjalan otomatis saat ada data baru dibuat.
     * Kita akan membuat nomor referensi di sini.
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            if (empty($model->reference)) {
                $number = Sale::max('id') + 1;
                $model->reference = make_reference_id('OB2', $number);
            }
        });
    }
}
