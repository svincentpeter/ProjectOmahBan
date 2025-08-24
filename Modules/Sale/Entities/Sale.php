<?php

namespace Modules\Sale\Entities;

use App\Models\User; // <-- TAMBAHKAN BARIS INI
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;

class Sale extends Model
{
     // ====== PATCH: casts & relations & scopes ======
    protected $casts = [
        'total_amount' => 'integer',
        'total_hpp'    => 'integer',
        'total_profit' => 'integer',
        'date'         => 'datetime',
    ];

    public function saleDetails(): HasMany
    {
        return $this->hasMany(SaleDetails::class, 'sale_id');
    }

    public function payments(): HasMany
    {
        return $this->hasMany(SalePayment::class, 'sale_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(\App\Models\User::class, 'user_id');
    }

    public function scopeBetween(Builder $q, $from, $to): Builder
    {
        $from = Carbon::parse($from)->startOfDay();
        $to   = Carbon::parse($to)->endOfDay();
        return $q->whereBetween('date', [$from, $to]);
    }


// ====== END PATCH ======

    use HasFactory, SoftDeletes;

    protected $table = 'sales';

    protected $fillable = [
        'reference',
        'date',
        'status',            // Draft | Pending | Completed
        'payment_status',    // Unpaid | Partial | Paid
        'total_amount',
        'paid_amount',
        'due_amount',
        'payment_method',    // info default di header
        'bank_name',         // opsional (di header)
        'note',
        'user_id' // <-- TAMBAHKAN INI
    ];

    /* =========================================================
     |                        RELATIONSHIPS
     |=========================================================*/

    

    /**
     * Detail item (alias generik).
     * Banyak kode lama memanggil saleDetails(), jadi kita sediakan keduanya.
     */
    public function details()
    {
        return $this->hasMany(SaleDetails::class, 'sale_id');
    }


    

public function scopeDatedBetween($q, $start, $end)
{
    return $q->whereBetween('date', [$start, $end]);
}
    /* =========================================================
     |                           HELPERS
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
        $this->due_amount  = max(0, (int) $this->total_amount - $paid);

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
            return function_exists('format_currency')
                ? format_currency($n, true)
                : number_format((int) $n, 0, ',', '.');
        };

        return [
            'total'           => (int) $this->total_amount,
            'paid'            => (int) $this->paid_amount,
            'due'             => (int) $this->due_amount,
            'status'          => (string) $this->payment_status,
            'total_formatted' => $fmt((int) $this->total_amount),
            'paid_formatted'  => $fmt((int) $this->paid_amount),
            'due_formatted'   => $fmt((int) $this->due_amount),
        ];
    }

    public function salePayments()
    {
        // semua pembayaran milik sale ini
        return $this->hasMany(\Modules\Sale\Entities\SalePayment::class, 'sale_id');
    }

    public function latestPayment()
    {
        // pembayaran terakhir berdasarkan tanggal (dan id sebagai tie-breaker)
        return $this->hasOne(\Modules\Sale\Entities\SalePayment::class, 'sale_id')
            ->latestOfMany('date');
    }

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
    public function scopeCompleted($q)
{
    return $q->where('status', 'Completed')->whereNull('deleted_at');
}
}
