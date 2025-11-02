<?php

namespace Modules\Adjustment\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Facades\Log;
use Modules\Product\Entities\Product;
use App\Models\User;

class StockMovement extends Model
{
    use HasFactory;

    protected $table = 'stock_movements';

    // Konstanta tipe gerak stok
    public const TYPE_IN = 'in';
    public const TYPE_OUT = 'out';

    /** ✅ FILLABLE */
    protected $fillable = [
        // (Legacy) jika dulu pernah pakai polymorphic product
        'productable_type',
        'productable_id',

        // Rekomendasi: direct product reference
        'product_id',

        // Polymorphic reference ke dokumen sumber
        'ref_type', // 'adjustment', 'sale', 'purchase_return', dst.
        'ref_id',

        'type', // 'in' | 'out'
        'quantity', // positif; tanda diwakili oleh 'type'
        'description',
        'user_id',
    ];

    /** ✅ CASTS */
    protected $casts = [
        'productable_id' => 'integer',
        'product_id' => 'integer',
        'ref_id' => 'integer',
        'quantity' => 'integer',
        'user_id' => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /** ✅ APPENDS */
    protected $appends = ['formatted_quantity', 'signed_quantity', 'ref_name'];

    /** ✅ Hooks: normalisasi nilai sebelum create */
    protected static function booted(): void
    {
        static::creating(function (self $m) {
            // Pastikan tipe valid
            $m->type = $m->type === self::TYPE_IN ? self::TYPE_IN : self::TYPE_OUT;
            // Quantity tidak boleh negatif
            $m->quantity = max(0, (int) $m->quantity);
        });
    }

    // ======================
    // RELATIONS
    // ======================

    /** Produk (direct, disarankan) */
    public function product()
    {
        return $this->belongsTo(Product::class, 'product_id');
    }

    /** User pencatat */
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /** Referensi polymorphic (adjustment/sale/…): ref_type + ref_id */
    public function reference()
    {
        return $this->morphTo(__FUNCTION__, 'ref_type', 'ref_id');
    }

    /** Shortcut untuk adjustment (filter ref_type) */
    public function adjustment()
    {
        return $this->belongsTo(\Modules\Adjustment\Entities\Adjustment::class, 'ref_id')->where('stock_movements.ref_type', 'adjustment');
    }

    // ======================
    // SCOPES
    // ======================

    public function scopeByProduct($query, int $productId)
    {
        return $query->where('product_id', $productId);
    }

    public function scopeByType($query, string $type)
    {
        return $query->where('type', $type);
    }

    public function scopeRecent($query, int $days = 30)
    {
        return $query->where('created_at', '>=', now()->subDays($days))->latest('created_at');
    }

    public function scopeByRefType($query, string $type)
    {
        return $query->where('ref_type', $type);
    }

    public function scopeByAdjustment($query, int $adjustmentId)
    {
        return $query->where('ref_type', 'adjustment')->where('ref_id', $adjustmentId);
    }

    public function scopeOutgoing($query)
    {
        return $query->where('type', self::TYPE_OUT);
    }

    public function scopeIncoming($query)
    {
        return $query->where('type', self::TYPE_IN);
    }

    public function scopeBetweenDates($query, $start, $end)
    {
        return $query->whereBetween('created_at', [$start, $end]);
    }

    public function scopeSearch($query, string $term)
    {
        return $query->where(function ($w) use ($term) {
            $w->where('description', 'like', "%{$term}%")
                ->orWhere('type', 'like', "%{$term}%")
                ->orWhere('ref_type', 'like', "%{$term}%");
        });
    }

    // ======================
    // ACCESSORS
    // ======================

    /** "+10" atau "-5" (string) */
    public function getFormattedQuantityAttribute(): string
    {
        $sign = $this->type === self::TYPE_IN ? '+' : '-';
        return $sign . (int) $this->quantity;
    }

    /** 10 atau -5 (integer) */
    public function getSignedQuantityAttribute(): int
    {
        $mult = $this->type === self::TYPE_IN ? 1 : -1;
        return $mult * (int) $this->quantity;
    }

    /** Nama referensi untuk tampilan */
    public function getRefNameAttribute(): string
    {
        if (!$this->ref_type || !$this->ref_id) {
            return 'Manual Entry';
        }

        try {
            switch ($this->ref_type) {
                case 'adjustment':
                    $ref = \Modules\Adjustment\Entities\Adjustment::find($this->ref_id);
                    return $ref ? "Adjustment {$ref->reference}" : 'Unknown Adjustment';

                case 'sale':
                    if (class_exists(\Modules\Sale\Entities\Sale::class)) {
                        $ref = \Modules\Sale\Entities\Sale::find($this->ref_id);
                        return $ref ? "Sale {$ref->invoice_number}" : 'Unknown Sale';
                    }
                    return 'Sale#' . $this->ref_id;

                default:
                    return "{$this->ref_type}#{$this->ref_id}";
            }
        } catch (\Throwable $e) {
            Log::warning('StockMovement ref_name error: ' . $e->getMessage());
            return "{$this->ref_type}#{$this->ref_id}";
        }
    }

    // ======================
    // HELPERS
    // ======================

    /**
     * Buat movement secara konsisten.
     * Menerima:
     * - 'product_id' (wajib)
     * - 'type': 'in'|'out'
     * - 'quantity' (>0)
     * - 'description'
     * - 'user_id' (opsional; default auth()->id())
     * - Referensi:
     *     A) 'ref' => model (polymorphic otomatis), atau
     *     B) 'adjustment_id' / 'sale_id' (fallback), atau
     *     C) 'ref_type' + 'ref_id' manual
     */
    public static function record(array $data): ?self
    {
        try {
            $type = $data['type'] ?? self::TYPE_OUT;
            if (!in_array($type, [self::TYPE_IN, self::TYPE_OUT], true)) {
                $type = self::TYPE_OUT;
            }

            $qty = (int) ($data['quantity'] ?? 0);
            if ($qty <= 0) {
                throw new \InvalidArgumentException('Quantity must be > 0');
            }

            $movement = new self([
                'product_id' => $data['product_id'] ?? null,
                'type' => $type,
                'quantity' => $qty,
                'description' => $data['description'] ?? 'Stock Movement',
                'user_id' => $data['user_id'] ?? auth()->id(),
            ]);

            // Referensi polymorphic via object
            if (isset($data['ref']) && $data['ref'] instanceof Model) {
                $movement->ref_type = $data['ref']->getMorphClass();
                $movement->ref_id = $data['ref']->getKey();
            }
            // Fallback: field spesifik
            elseif (isset($data['adjustment_id'])) {
                $movement->ref_type = 'adjustment';
                $movement->ref_id = (int) $data['adjustment_id'];
            } elseif (isset($data['sale_id'])) {
                $movement->ref_type = 'sale';
                $movement->ref_id = (int) $data['sale_id'];
            }
            // Manual explicit
            elseif (isset($data['ref_type'], $data['ref_id'])) {
                $movement->ref_type = (string) $data['ref_type'];
                $movement->ref_id = (int) $data['ref_id'];
            }

            // (Legacy) dukung productable_* bila masih dipakai
            if (isset($data['productable_type'])) {
                $movement->productable_type = $data['productable_type'];
                $movement->productable_id = $data['productable_id'] ?? null;
            }

            if (!$movement->product_id) {
                throw new \InvalidArgumentException('product_id is required');
            }

            $movement->save();

            return $movement;
        } catch (\Throwable $e) {
            Log::error('StockMovement::record() error: ' . $e->getMessage(), ['payload' => $data]);
            return null;
        }
    }

    /** Saldo stok berdasarkan ledger */
    public static function getProductBalance(int $productId): int
    {
        $in = self::where('product_id', $productId)->incoming()->sum('quantity');
        $out = self::where('product_id', $productId)->outgoing()->sum('quantity');
        return (int) $in - (int) $out;
    }

    /** Ringkasan per produk (periode N hari) */
    public static function getProductSummary(int $productId, int $days = 30): array
    {
        $base = self::where('product_id', $productId)->where('created_at', '>=', now()->subDays($days));

        return [
            'total_in' => (clone $base)->incoming()->sum('quantity'),
            'total_out' => (clone $base)->outgoing()->sum('quantity'),
            'count' => (clone $base)->count(),
            'last_movement' => (clone $base)->latest('created_at')->first(),
        ];
    }
}
