<?php

namespace Modules\Sale\Entities;

use App\Models\User; // <-- TAMBAHKAN BARIS INI
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Sale extends Model
{
    use HasFactory;

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
     * FUNGSI INI YANG MEMPERBAIKI ERROR ANDA
     * Mendefinisikan relasi ke model User.
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Pembayaran (SalePayment).
     */
    public function payments()
    {
        return $this->hasMany(SalePayment::class, 'sale_id');
    }

    /**
     * Detail item (alias generik).
     * Banyak kode lama memanggil saleDetails(), jadi kita sediakan keduanya.
     */
    public function details()
    {
        return $this->hasMany(SaleDetails::class, 'sale_id');
    }

    /**
     * ALIAS untuk kompatibilitas lama.
     * Beberapa query/DataTable memanggil relasi bernama "saleDetails".
     */
    public function saleDetails()
    {
        return $this->hasMany(SaleDetails::class, 'sale_id');
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
            // 1. Ambil ID terakhir, tambahkan 1 untuk nomor berikutnya.
            $number = Sale::max('id') + 1;
            // 2. Gunakan helper untuk membuat format 'OB2-00001'
            $model->reference = make_reference_id('OB2', $number);
        });
    }
}