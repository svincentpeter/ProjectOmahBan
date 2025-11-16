<?php

namespace Modules\People\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Modules\Purchase\Entities\Purchase;

class Supplier extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * Nama tabel di database
     */
    protected $table = 'suppliers';

    /**
     * Kolom yang boleh diisi mass assignment
     */
    protected $fillable = ['supplier_name', 'supplier_email', 'supplier_phone', 'city', 'country', 'address'];

    /**
     * Kolom yang di-cast ke tipe data tertentu
     */
    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    /**
     * Default values untuk kolom tertentu
     */
    protected $attributes = [
        'country' => 'Indonesia', // Default untuk UMKM lokal
    ];

    // ========================================
    // RELATIONSHIPS
    // ========================================

    /**
     * Relasi: Supplier punya banyak Purchase
     * Untuk tracking pembelian dari supplier ini
     */
    public function purchases()
    {
        return $this->hasMany(Purchase::class, 'supplier_id', 'id');
    }

    // ========================================
    // QUERY SCOPES
    // ========================================

    /**
     * Scope: Filter supplier by city
     *
     * Usage: Supplier::byCity('Jakarta')->get();
     */
    public function scopeByCity($query, $city)
    {
        return $query->where('city', $city);
    }

    /**
     * Scope: Search supplier by name, email, or phone
     *
     * Usage: Supplier::search('toko')->get();
     */
    public function scopeSearch($query, $keyword)
    {
        return $query->where(function ($q) use ($keyword) {
            $q->where('supplier_name', 'like', "%{$keyword}%")
                ->orWhere('supplier_email', 'like', "%{$keyword}%")
                ->orWhere('supplier_phone', 'like', "%{$keyword}%")
                ->orWhere('city', 'like', "%{$keyword}%");
        });
    }

    /**
     * Scope: Supplier yang masih aktif (belum dihapus)
     *
     * Usage: Supplier::active()->get();
     */
    public function scopeActive($query)
    {
        return $query->whereNull('deleted_at');
    }

    // ========================================
    // ACCESSORS (Format data untuk tampilan)
    // ========================================

    /**
     * Accessor: Format nama lengkap supplier dengan kota
     *
     * Usage: $supplier->full_name
     * Output: "Toko Ban Jaya - Jakarta"
     */
    public function getFullNameAttribute()
    {
        return "{$this->supplier_name} - {$this->city}";
    }

    /**
     * Accessor: Format tanggal dibuat (user-friendly)
     *
     * Usage: $supplier->formatted_created_at
     * Output: "10 Nov 2025"
     */
    public function getFormattedCreatedAtAttribute()
    {
        return $this->created_at ? $this->created_at->format('d M Y') : '-';
    }

    /**
     * Accessor: Total pembelian dari supplier (count)
     *
     * Usage: $supplier->total_purchases
     * Output: 15 (jumlah transaksi)
     */
    public function getTotalPurchasesAttribute()
    {
        return $this->purchases()->count();
    }

    /**
     * Accessor: Total nilai pembelian dari supplier (sum)
     *
     * Usage: $supplier->total_purchase_amount
     * Output: 50000000 (dalam Rupiah integer)
     */
    public function getTotalPurchaseAmountAttribute()
    {
        return $this->purchases()->sum('total_amount');
    }

    /**
     * Accessor: Status aktif supplier (berdasarkan riwayat pembelian)
     *
     * Usage: $supplier->is_active
     * Output: true/false
     */
    public function getIsActiveAttribute()
    {
        // Dianggap aktif jika ada pembelian dalam 6 bulan terakhir
        $sixMonthsAgo = now()->subMonths(6);
        return $this->purchases()->where('date', '>=', $sixMonthsAgo)->exists();
    }

    // ========================================
    // CUSTOM METHODS
    // ========================================

    /**
     * Cek apakah supplier bisa dihapus (hard delete)
     * Supplier tidak bisa dihapus jika ada purchase history
     *
     * @return bool
     */
    public function canBeDeleted()
    {
        return $this->purchases()->count() === 0;
    }

    /**
     * Dapatkan daftar kota unik dari semua supplier
     * Untuk dropdown filter
     *
     * @return \Illuminate\Support\Collection
     */
    public static function getUniqueCities()
    {
        return self::select('city')->distinct()->orderBy('city', 'asc')->pluck('city');
    }

    /**
     * Sanitasi input sebelum disimpan (security layer)
     *
     * @param array $data
     * @return array
     */
    public static function sanitizeInput(array $data)
    {
        return [
            'supplier_name' => strip_tags(trim($data['supplier_name'] ?? '')),
            'supplier_email' => filter_var(trim($data['supplier_email'] ?? ''), FILTER_SANITIZE_EMAIL),
            'supplier_phone' => preg_replace('/[^0-9+\-\s]/', '', $data['supplier_phone'] ?? ''),
            'city' => strip_tags(trim($data['city'] ?? '')),
            'country' => strip_tags(trim($data['country'] ?? 'Indonesia')),
            'address' => strip_tags(trim($data['address'] ?? '')),
        ];
    }

    // ========================================
    // BOOT METHOD (Auto Sanitasi)
    // ========================================

    /**
     * Boot method untuk auto-sanitasi saat create/update
     */
    protected static function boot()
    {
        parent::boot();

        // Event: Sebelum create, sanitasi input
        static::creating(function ($model) {
            $model->supplier_name = strip_tags(trim($model->supplier_name));
            $model->supplier_email = filter_var(trim($model->supplier_email), FILTER_SANITIZE_EMAIL);
            $model->supplier_phone = preg_replace('/[^0-9+\-\s]/', '', $model->supplier_phone);
            $model->city = strip_tags(trim($model->city));
            $model->address = strip_tags(trim($model->address));
        });

        // Event: Sebelum update, sanitasi input
        static::updating(function ($model) {
            $model->supplier_name = strip_tags(trim($model->supplier_name));
            $model->supplier_email = filter_var(trim($model->supplier_email), FILTER_SANITIZE_EMAIL);
            $model->supplier_phone = preg_replace('/[^0-9+\-\s]/', '', $model->supplier_phone);
            $model->city = strip_tags(trim($model->city));
            $model->address = strip_tags(trim($model->address));
        });

        // Event: Sebelum soft delete, cek apakah aman
        static::deleting(function ($model) {
            // Jika force delete dan ada purchase history, throw exception
            if ($model->isForceDeleting() && !$model->canBeDeleted()) {
                throw new \Exception('Tidak dapat menghapus supplier yang memiliki riwayat pembelian. Gunakan soft delete saja.');
            }
        });
    }
}
