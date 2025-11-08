<?php

namespace App\Jobs;

use App\Models\OwnerNotification;
use App\Services\FonteeNotificationService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Contracts\Queue\ShouldBeUnique; // ✅ OPSIONAL
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class SendFonteeNotificationJob implements ShouldQueue /*, ShouldBeUnique*/
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $tries;
    public $backoff;

    protected $notificationId;

    public function __construct(OwnerNotification $notification)
    {
        $this->notificationId = $notification->id;

        $this->tries = (int) config('fontee.retry_attempts', 3);
        $this->backoff = (int) config('fontee.retry_delay_seconds', 60);
    }

    // public function uniqueId(): string { return 'fontee-'.$this->notificationId; } // ✅ OPSIONAL

    public function handle(FonteeNotificationService $fonteeService)
    {
        $notification = OwnerNotification::find($this->notificationId);
        if (!$notification) {
            Log::warning('Notification not found', ['id' => $this->notificationId]);
            return;
        }

        // ✅ FIX: idempoten — sudah sent? stop
        if ($notification->fontee_status === 'sent' && !empty($notification->fontee_message_id)) {
            Log::info('Skip send, already sent', ['id' => $this->notificationId]);
            return;
        }

        $result = $fonteeService->send($notification);

        if (!$result && $this->attempts() < $this->tries) {
            $this->release($this->backoff);
        }
    }

    public function failed(\Throwable $exception)
    {
        Log::error('SendFonteeNotificationJob failed permanently', [
            'notification_id' => $this->notificationId,
            'exception' => $exception->getMessage(),
        ]);
    }
}
