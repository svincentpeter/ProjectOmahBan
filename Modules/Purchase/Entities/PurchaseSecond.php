<?php

namespace Modules\Purchase\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Models\User;

class PurchaseSecond extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected $casts = [
        'date' => 'date',
        'total_amount' => 'integer',
        'paid_amount' => 'integer',
        'due_amount' => 'integer',
    ];

    protected $with = ['purchaseSecondDetails'];

    /**
     * Generate reference number otomatis
     * Format: PBS-YYYYMMDD-0001
     */
    public static function nextReference(): string
    {
        $date = now()->format('Ymd');
        $prefix = "PBS-{$date}-";

        $lastPurchase = self::where('reference', 'like', "{$prefix}%")
            ->latest('id')
            ->first();

        if (!$lastPurchase) {
            return "{$prefix}0001";
        }

        $lastNumber = (int) substr($lastPurchase->reference, -4);
        $nextNumber = str_pad($lastNumber + 1, 4, '0', STR_PAD_LEFT);

        return "{$prefix}{$nextNumber}";
    }

    /**
     * Relasi ke purchase_second_details
     */
    public function purchaseSecondDetails()
    {
        return $this->hasMany(PurchaseSecondDetail::class, 'purchase_second_id');
    }

    /**
     * Relasi ke user (yang input pembelian)
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Scope: Filter by date range
     */
    public function scopeBetween($query, $startDate, $endDate)
    {
        return $query->whereDate('date', '>=', $startDate)->whereDate('date', '<=', $endDate);
    }

    /**
     * Scope: Filter by customer name
     */
    public function scopeByCustomer($query, $customerName)
    {
        return $query->where('customer_name', 'like', "%{$customerName}%");
    }

    /**
     * Scope: Filter by payment status
     */
    public function scopeByPaymentStatus($query, $status)
    {
        return $query->where('payment_status', $status);
    }

    /**
     * Accessor: Format total_amount sebagai Rupiah
     */
    public function getFormattedTotalAttribute(): string
    {
        return rupiah($this->total_amount);
    }

    /**
     * Accessor: Format paid_amount sebagai Rupiah
     */
    public function getFormattedPaidAttribute(): string
    {
        return rupiah($this->paid_amount);
    }

    /**
     * Accessor: Format due_amount sebagai Rupiah
     */
    public function getFormattedDueAttribute(): string
    {
        return rupiah($this->due_amount);
    }

    /**
     * Accessor: Payment status badge color
     */
    public function getPaymentBadgeAttribute(): string
    {
        return $this->payment_status === 'Lunas' ? 'success' : 'warning';
    }

    /**
     * Accessor: Status badge color
     */
    public function getStatusBadgeAttribute(): string
    {
        return $this->status === 'Completed' ? 'info' : 'secondary';
    }
}
