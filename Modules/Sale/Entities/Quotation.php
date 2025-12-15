<?php

namespace Modules\Sale\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Modules\People\Entities\Customer;

class Quotation extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'date',
        'reference',
        'customer_id',
        'customer_name',
        'tax_percentage',
        'tax_amount',
        'discount_percentage',
        'discount_amount',
        'shipping_amount',
        'total_amount',
        'status', // Pending, Sent, Accepted, Rejected, Converted
        'note',
    ];

    protected $casts = [
        'date' => 'datetime',
        'tax_percentage' => 'integer',
        'tax_amount' => 'integer',
        'discount_percentage' => 'integer',
        'discount_amount' => 'integer',
        'shipping_amount' => 'integer',
        'total_amount' => 'integer',
    ];

    public function quotationDetails(): HasMany
    {
        return $this->hasMany(QuotationDetail::class);
    }

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    public function getCustomerDisplayNameAttribute(): string
    {
        if ($this->customer) {
            return (string) $this->customer->customer_name;
        }
        return (string) ($this->customer_name ?? 'Guest');
    }

    public function getCustomerInfoAttribute(): array
    {
        if ($this->customer) {
            return [
                'name' => (string) $this->customer->customer_name,
                'email' => (string) $this->customer->customer_email,
                'phone' => (string) $this->customer->customer_phone,
                'city' => (string) ($this->customer->city ?? '-'),
                'address' => (string) ($this->customer->address ?? '-'),
            ];
        }
        return [
            'name' => (string) ($this->customer_name ?? 'Guest'),
            'email' => '-',
            'phone' => '-',
            'city' => '-',
            'address' => '-',
        ];
    }

    protected static function boot()
    {
        parent::boot();

        static::creating(function (self $model) {
            if (empty($model->reference)) {
                $next = (int) (self::max('id') ?? 0) + 1;
                // Simple reference: QT-YYYYMMDD-ID
                $model->reference = 'QT-' . now()->format('Ymd') . '-' . str_pad((string) $next, 6, '0', STR_PAD_LEFT);
            }
        });
    }
}
