<?php

namespace App\Listeners;

use App\Events\ManualInputCreated;
use App\Models\OwnerNotification;
use App\Models\User;
use App\Services\Fontee\FonteeNotificationService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Log;
use Modules\Sale\Entities\ManualInputLog;

class NotifyOwnerOfManualInput
{
    use InteractsWithQueue;

    // Biar aman saat retry
    public $tries = 3;
    public $timeout = 30;

    protected $fonteeService;

    public function __construct(FonteeNotificationService $fonteeService)
    {
        $this->fonteeService = $fonteeService;
    }

    /**
     * Handle event: Generate & send notification ke owner/supervisor.
     * Idempoten terhadap duplicate job & retry.
     */
    public function handle(ManualInputCreated $event): void
    {
        // ✅ Logging uji listener
        Log::info('NotifyOwnerOfManualInput triggered', ['sale_id' => $event->sale->id]);

        $sale = $event->sale->fresh(); // ambil state terbaru (is_manual_input_notified dll)
        $manualItems = is_array($event->manualItems) ? $event->manualItems : [];
        $cashier = $event->cashier;

        // 0) Idempotency: kalau sudah dinotifikasi, langsung berhenti
        if ((int) ($sale->is_manual_input_notified ?? 0) === 1) {
            Log::info('Manual input already notified, skipping.', ['sale_id' => $sale->id]);
            return;
        }

        try {
            // 1) Target penerima (Owner, Supervisor, Super Admin) - case-insensitive & guard aman
            $owners = \App\Models\User::query()
                ->where(function ($q) {
                    // is_active default ke 1 kalau null
                    $q->whereNull('is_active')->orWhere('is_active', 1);
                })
                ->whereHas('roles', function ($r) {
                    // dukung variasi penamaan role (case-insensitive)
                    $r->whereIn(\DB::raw('LOWER(name)'), ['owner', 'supervisor', 'super admin']);
                })
                ->get();

            // logging tambahan biar gampang trace
            \Log::info('NotifyOwnerOfManualInput owners resolved', [
                'sale_id' => $sale->id,
                'count' => $owners->count(),
                'ids' => $owners->pluck('id')->all(),
            ]);

            if ($owners->isEmpty()) {
                // ❌ JANGAN menandai sale sebagai notified di sini
                // Biar bisa dicoba ulang setelah role/flag user dibereskan
                \Log::warning('No eligible recipients for manual-input notification. Skipping create.', [
                    'sale_id' => $sale->id,
                ]);
                return;
            }

            // 2) Hitung ringkasan nilai
            $itemCount = count($manualItems);
            $totalValue = collect($manualItems)->sum(function ($item) {
                $qty = (int) ($item['quantity'] ?? 1);
                $price = (int) ($item['price'] ?? 0);
                return $qty * $price;
            });

            // 3) List item (locale Indonesia)
            $itemsList = collect($manualItems)
                ->filter(fn($it) => is_array($it) && !empty($it))
                ->map(function ($item) {
                    $nm = (string) ($item['name'] ?? '-');
                    $qty = (int) ($item['quantity'] ?? 1);
                    $prc = (int) ($item['price'] ?? 0);
                    $rp = number_format($prc, 0, ',', '.');
                    return "{$nm} ({$qty}x @ Rp {$rp})";
                })
                ->join(', ');

            // 4) Ambil semua log manual input YANG BELUM dinotifikasi → untuk dilink ke notifikasi
            $pendingLogs = ManualInputLog::query()->where('sale_id', $sale->id)->manualInput()->unnotified()->get();

            // 5) Kirim notifikasi per owner (satu owner gagal tidak menggagalkan yang lain)
            foreach ($owners as $owner) {
                try {
                    $notification = $this->createNotification($owner, $sale, $manualItems, $itemCount, $totalValue, $itemsList, $cashier);

                    // Link semua log pending ke notifikasi barusan
                    foreach ($pendingLogs as $log) {
                        $log->linkToNotification($notification);
                    }

                    // Kirim keluar via Fontee (async/non-blocking di sisi service)
                    $this->fonteeService->sendNotification($notification);
                } catch (\Throwable $inner) {
                    Log::error('NotifyOwnerOfManualInput: per-recipient failed', [
                        'sale_id' => $sale->id,
                        'owner_id' => $owner->id,
                        'message' => $inner->getMessage(),
                    ]);
                    // lanjut ke owner berikutnya
                }
            }

            // 6) Tandai SALE sudah dinotifikasi (idempoten)
            $sale->update([
                'is_manual_input_notified' => 1,
                'notified_at' => now(),
            ]);
        } catch (\Throwable $e) {
            Log::error('NotifyOwnerOfManualInput Error', [
                'sale_id' => $event->sale->id,
                'exception' => $e->getMessage(),
            ]);
            // biarkan queue retry sesuai $tries
        }
    }

    /**
     * Helper: Buat record OwnerNotification (termasuk payload 'data')
     */
    private function createNotification($owner, $sale, array $manualItems, int $itemCount, int $totalValue, string $itemsList, $cashier): OwnerNotification
    {
        $ref = $sale->reference ?? '#' . $sale->id;
        $title = "⚠️ Input Manual - Inv {$ref}";
        $totalStr = number_format($totalValue, 0, ',', '.');

        $message = "Kasir {$cashier->name} membuat transaksi dengan {$itemCount} item input manual:\n\n" . $itemsList . "\n\n" . "Total: Rp {$totalStr}\n" . "Invoice: {$ref}\n" . 'Waktu: ' . ($sale->created_at?->format('d-m-Y H:i:s') ?? now()->format('d-m-Y H:i:s'));

        return OwnerNotification::create([
            'user_id' => $owner->id,
            'sale_id' => $sale->id,
            'notification_type' => 'manual_input_alert',
            'title' => $title,
            'message' => $message,
            'data' => [
                'cashier_name' => $cashier->name,
                'cashier_id' => $cashier->id,
                'items_count' => $itemCount,
                'total_amount' => $totalValue,
                'invoice_no' => $sale->reference,
                'items' => $manualItems,
            ],
            'severity' => $totalValue > 1_000_000 ? 'critical' : ($itemCount >= 2 ? 'warning' : 'info'),
            'is_read' => 0,
            'is_reviewed' => 0,
        ]);
    }
}
