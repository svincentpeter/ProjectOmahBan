<?php

namespace Modules\Purchase\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Carbon\Carbon;

class Purchase extends Model
{
    use HasFactory;

    protected $guarded = [];

    /**
     * Fillable attributes - Disesuaikan dengan struktur tabel yang sudah disederhanakan
     */
    protected $fillable = ['date', 'reference', 'supplier_id', 'supplier_name', 'total_amount', 'paid_amount', 'due_amount', 'status', 'payment_status', 'payment_method', 'bank_name', 'note', 'user_id'];

    /**
     * Casts attributes
     * Amount disimpan sebagai integer Rupiah tanpa desimal
     */
    protected $casts = [
        'date' => 'date',
        'total_amount' => 'integer',
        'paid_amount' => 'integer',
        'due_amount' => 'integer',
    ];

    /**
     * Relasi ke PurchaseDetail
     */
    public function purchaseDetails()
    {
        return $this->hasMany(PurchaseDetail::class, 'purchase_id', 'id');
    }

    /**
     * Relasi ke Supplier
     */
    public function supplier()
    {
        return $this->belongsTo(\Modules\People\Entities\Supplier::class, 'supplier_id', 'id');
    }

    /**
     * Relasi ke User (yang input pembelian) - BARU untuk audit trail
     */
    public function user()
    {
        return $this->belongsTo(\App\Models\User::class, 'user_id', 'id');
    }

    /**
     * Boot method untuk auto-generate reference number
     */
    public static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            // Generate reference number otomatis seperti Expense
            $model->reference = static::nextReference($model->date);
        });
    }

    /**
     * Generate reference number format: PB-YYYYMMDD-0001
     * PB = Purchase Ban (Pembelian Ban)
     * Mirip dengan Expense model
     */
    public static function nextReference(Carbon $date): string
    {
        $prefix = 'PB-' . $date->format('Ymd') . '-';
        $last = static::whereDate('date', $date)->max('reference');
        $seq = $last && preg_match('/-(\d+)$/', $last, $m) ? (int) $m[1] + 1 : 1;

        return $prefix . str_pad((string) $seq, 4, '0', STR_PAD_LEFT);
    }

    /**
     * Scope untuk filter purchase yang sudah completed
     */
    public function scopeCompleted($query)
    {
        return $query->where('status', 'Completed');
    }

    /**
     * Scope untuk filter berdasarkan range tanggal
     * Mirip dengan Expense model
     */
    public function scopeBetween($query, $from, $to)
    {
        $from = Carbon::parse($from)->startOfDay();
        $to = Carbon::parse($to)->endOfDay();

        return $query->whereBetween('date', [$from, $to]);
    }

    /**
     * Scope untuk filter by supplier
     */
    public function scopeBySupplier($query, $supplierId)
    {
        return $query->where('supplier_id', $supplierId);
    }

    /**
     * Scope untuk filter by payment status
     */
    public function scopeByPaymentStatus($query, $paymentStatus)
    {
        return $query->where('payment_status', $paymentStatus);
    }

    /**
     * Accessor untuk format tanggal yang user-friendly
     */
    public function getFormattedDateAttribute()
    {
        return Carbon::parse($this->date)->format('d M, Y');
    }

    /**
     * Relasi ke PurchasePayment (payment history)
     */
    public function payments()
    {
        return $this->hasMany(PurchasePayment::class, 'purchase_id');
    }

    /**
     * Alias for payments
     */
    public function purchasePayments()
    {
        return $this->payments();
    }

    /**
     * Recalculate paid_amount, due_amount, and payment_status based on payments
     */
    public function recalcPaymentStatus(): self
    {
        $paid = (int) $this->payments()->sum('amount');
        $this->paid_amount = $paid;
        $this->due_amount = max(0, (int) $this->total_amount - $paid);

        // Determine payment status
        if ($this->due_amount <= 0) {
            $this->payment_status = 'Paid';
        } elseif ($this->paid_amount > 0) {
            $this->payment_status = 'Partial';
        } else {
            $this->payment_status = 'Unpaid';
        }

        // Auto-update status if fully paid
        if ($this->payment_status === 'Paid' && $this->status !== 'Completed') {
            $this->status = 'Completed';
        }

        $this->save();
        return $this;
    }

    /**
     * Get supplier display name
     */
    public function getSupplierDisplayNameAttribute(): string
    {
        if ($this->supplier) {
            return (string) $this->supplier->supplier_name;
        }
        return (string) ($this->supplier_name ?? '-');
    }
}

