<?php

namespace Modules\Adjustment\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\User;

class StockOpname extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'reference', 'opname_date', 'status', 'scope_type', 'scope_ids',
        'pic_id', 'supervisor_id', 'approved_at', 'notes',
        'total_items', 'total_variance', 'variance_value'
    ];

    protected $casts = [
        'opname_date' => 'date',
        'approved_at' => 'datetime',
        'scope_ids' => 'array', // JSON array
        'total_items' => 'integer',
        'total_variance' => 'integer',
        'variance_value' => 'decimal:2'
    ];

    // -------------------- RELATIONS --------------------
    
    public function items()
    {
        return $this->hasMany(StockOpnameItem::class, 'stock_opname_id');
    }

    public function logs()
    {
        return $this->hasMany(StockOpnameLog::class, 'stock_opname_id');
    }

    public function pic()
    {
        return $this->belongsTo(User::class, 'pic_id');
    }

    public function supervisor()
    {
        return $this->belongsTo(User::class, 'supervisor_id');
    }

    // -------------------- SCOPES --------------------
    
    public function scopeDraft($q)
    {
        return $q->where('status', 'draft');
    }

    public function scopeInProgress($q)
    {
        return $q->where('status', 'in_progress');
    }

    public function scopeCompleted($q)
    {
        return $q->where('status', 'completed');
    }

    // -------------------- ACCESSORS --------------------
    
    public function getStatusBadgeAttribute(): string
    {
        $map = [
            'draft' => ['text' => 'Draft', 'class' => 'badge badge-secondary'],
            'in_progress' => ['text' => 'Sedang Berjalan', 'class' => 'badge badge-warning'],
            'completed' => ['text' => 'Selesai', 'class' => 'badge badge-success'],
            'cancelled' => ['text' => 'Dibatalkan', 'class' => 'badge badge-danger'],
        ];
        $conf = $map[$this->status] ?? ['text' => 'Unknown', 'class' => 'badge badge-secondary'];
        return sprintf('<span class="%s">%s</span>', $conf['class'], e($conf['text']));
    }

    public function getCompletionPercentageAttribute(): float
    {
        $total = $this->items()->count();
        if ($total === 0) return 0;
        
        $counted = $this->items()->whereNotNull('actual_qty')->count();
        return round(($counted / $total) * 100, 2);
    }

    // -------------------- HELPERS --------------------
    
    /**
     * Generate reference SO-YYYYMMDD-#####
     */
    public static function generateReference(): string
    {
        $dateCode = now()->format('Ymd');
        $last = self::whereDate('created_at', today())
            ->lockForUpdate()
            ->latest('id')
            ->first();
        
        $seq = $last ? ((int) substr($last->reference, -5)) + 1 : 1;
        return 'SO-' . $dateCode . '-' . str_pad($seq, 5, '0', STR_PAD_LEFT);
    }

    /**
     * Generate adjustment otomatis untuk variance
     */
    public function generateAdjustments(): array
    {
        $adjustments = [];
        
        // Hanya untuk item yang ada variance
        $variances = $this->items()
            ->whereNotNull('actual_qty')
            ->where('variance_qty', '!=', 0)
            ->with('product')
            ->get();

        if ($variances->isEmpty()) {
            return $adjustments;
        }

        \DB::transaction(function() use ($variances, &$adjustments) {
            foreach ($variances as $item) {
                $type = $item->variance_qty > 0 ? 'add' : 'sub';
                $qty = abs($item->variance_qty);
                
                // Tentukan reason berdasarkan tipe variance
                $reason = $item->variance_qty < 0 ? 'Hilang' : 'Lainnya';
                
                // Buat adjustment
                $adjustment = Adjustment::create([
                    'date' => $this->opname_date,
                    'reference' => Adjustment::generateReference(),
                    'note' => "Auto-generated dari Stok Opname {$this->reference}",
                    'reason' => $reason,
                    'description' => $item->variance_reason ?? "Selisih hasil stock opname: System {$item->system_qty}, Fisik {$item->actual_qty}",
                    'requester_id' => $this->pic_id,
                    'status' => 'pending', // Tetap perlu approval
                ]);

                // Buat detail adjustment
                AdjustedProduct::create([
                    'adjustment_id' => $adjustment->id,
                    'product_id' => $item->product_id,
                    'quantity' => $qty,
                    'type' => $type,
                ]);

                // Log
                AdjustmentLog::create([
                    'adjustment_id' => $adjustment->id,
                    'user_id' => auth()->id(),
                    'action' => 'created',
                    'new_status' => 'pending',
                    'notes' => "Generated from Stock Opname {$this->reference}",
                    'locked' => 1,
                ]);

                // Update link di opname item
                $item->update(['adjustment_id' => $adjustment->id]);
                
                $adjustments[] = $adjustment;
            }
        });

        return $adjustments;
    }
}
