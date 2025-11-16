<?php

namespace Modules\People\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Modules\Sale\Entities\Sale;

class Customer extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'customers';

    protected $fillable = ['customer_name', 'customer_email', 'customer_phone', 'city', 'country', 'address'];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    protected $attributes = [
        'country' => 'Indonesia',
    ];

    // ========================================
    // RELATIONSHIPS
    // ========================================

    /**
     * Relasi ke Sales (1 customer → many sales)
     */
    public function sales()
    {
        return $this->hasMany(Sale::class, 'customer_id', 'id');
    }

    // ========================================
    // QUERY SCOPES
    // ========================================

    /**
     * Scope: Filter by city
     */
    public function scopeByCity($query, $city)
    {
        return $query->where('city', $city);
    }

    /**
     * Scope: Search customer
     */
    public function scopeSearch($query, $keyword)
    {
        return $query->where(function ($q) use ($keyword) {
            $q->where('customer_name', 'like', "%{$keyword}%")
                ->orWhere('customer_email', 'like', "%{$keyword}%")
                ->orWhere('customer_phone', 'like', "%{$keyword}%")
                ->orWhere('city', 'like', "%{$keyword}%");
        });
    }

    /**
     * Scope: Get active customers (not soft deleted)
     */
    public function scopeActive($query)
    {
        return $query->whereNull('deleted_at');
    }

    /**
     * Scope: Get customers with sales in last X months
     */
    public function scopeActiveInPeriod($query, $months = 6)
    {
        return $query->whereHas('sales', function ($q) use ($months) {
            $q->where('date', '>=', now()->subMonths($months));
        });
    }

    // ========================================
    // ACCESSORS
    // ========================================

    /**
     * Full name with city (untuk dropdown)
     */
    public function getFullNameAttribute()
    {
        return "{$this->customer_name} - {$this->city}";
    }

    /**
     * Format created_at untuk display
     */
    public function getFormattedCreatedAtAttribute()
    {
        return $this->created_at ? $this->created_at->format('d M Y') : '-';
    }

    /**
     * Total sales count
     */
    public function getTotalSalesAttribute()
    {
        return $this->sales()->count();
    }

    /**
     * Total sale amount
     */
    public function getTotalSaleAmountAttribute()
    {
        return $this->sales()->sum('total_amount');
    }

    /**
     * Check if customer is active (ada sales dalam 6 bulan)
     */
    public function getIsActiveAttribute()
    {
        $sixMonthsAgo = now()->subMonths(6);
        return $this->sales()->where('date', '>=', $sixMonthsAgo)->exists();
    }

    // ========================================
    // CUSTOM METHODS
    // ========================================

    /**
     * Check apakah customer bisa dihapus (tidak ada sales history)
     */
    public function canBeDeleted()
    {
        return $this->sales()->count() === 0;
    }

    /**
     * Get unique cities dari semua customers
     */
    public static function getUniqueCities()
    {
        return self::select('city')->distinct()->orderBy('city', 'asc')->pluck('city');
    }

    /**
     * Sanitize input untuk security
     */
    public static function sanitizeInput(array $data)
    {
        return [
            'customer_name' => strip_tags(trim($data['customer_name'] ?? '')),
            'customer_email' => filter_var(trim($data['customer_email'] ?? ''), FILTER_SANITIZE_EMAIL),
            'customer_phone' => preg_replace('/[^0-9+\-\s\(\)]/', '', $data['customer_phone'] ?? ''),
            'city' => strip_tags(trim($data['city'] ?? '')),
            'country' => strip_tags(trim($data['country'] ?? 'Indonesia')),
            'address' => strip_tags(trim($data['address'] ?? '')),
        ];
    }

    // ========================================
    // BOOT METHOD (Auto-sanitize)
    // ========================================

    protected static function boot()
    {
        parent::boot();

        // Auto-sanitize saat creating
        static::creating(function ($model) {
            $model->customer_name = strip_tags(trim($model->customer_name));
            $model->customer_email = filter_var(trim($model->customer_email), FILTER_SANITIZE_EMAIL);
            $model->customer_phone = preg_replace('/[^0-9+\-\s\(\)]/', '', $model->customer_phone);
            $model->city = strip_tags(trim($model->city));
            $model->address = strip_tags(trim($model->address));
        });

        // Auto-sanitize saat updating
        static::updating(function ($model) {
            $model->customer_name = strip_tags(trim($model->customer_name));
            $model->customer_email = filter_var(trim($model->customer_email), FILTER_SANITIZE_EMAIL);
            $model->customer_phone = preg_replace('/[^0-9+\-\s\(\)]/', '', $model->customer_phone);
            $model->city = strip_tags(trim($model->city));
            $model->address = strip_tags(trim($model->address));
        });

        // Protect data integrity saat force delete
        static::deleting(function ($model) {
            if ($model->isForceDeleting() && !$model->canBeDeleted()) {
                throw new \Exception('Tidak dapat menghapus customer yang memiliki riwayat penjualan. Gunakan soft delete saja.');
            }
        });
    }
}
