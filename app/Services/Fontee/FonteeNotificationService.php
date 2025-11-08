<?php

namespace App\Services\Fontee;

use App\Models\OwnerNotification;
use App\Models\FonteeConfig;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Queue;
use App\Jobs\SendFonteeNotificationJob;

class FonteeNotificationService
{
    protected ?string $apiKey = null;
    protected ?string $channelId = null;
    protected string $baseUrl;
    protected bool $isEnabled = false;
    protected string $notificationType = 'whatsapp';

    public function __construct()
    {
        $this->loadConfig();
    }

    /**
     * Muat konfigurasi dari ENV/config lalu fallback ke DB (fontee_config).
     * - ENV/config selalu diprioritaskan.
     * - DB hanya fallback jika ENV kosong (kecuali enabled: ENV false tetap mematikan).
     */
    private function loadConfig(): void
    {
        // base URL (boleh diubah via .env)
        $this->baseUrl = (string) config('fontee.base_url', 'https://api.fontee.io/v1');

        // master switch: ENV/config menang; jika ENV false => tetap false walau DB true
        $this->isEnabled = (bool) config('fontee.enabled', false);
        if ($this->isEnabled === false) {
            // kalau ENV tidak mengatur, coba cek DB (opsional)
            $dbEnabled = $this->getConfigFromDb('fontee_enabled', '0') === '1';
            $this->isEnabled = $this->isEnabled || $dbEnabled;
        }

        // kredensial: ENV/config → DB
        $this->apiKey = config('fontee.api_key') ?? $this->getConfigFromDb('fontee_api_key');
        $this->channelId = config('fontee.channel_id') ?? $this->getConfigFromDb('fontee_channel_id');

        // tipe notifikasi
        $this->notificationType = (string) (config('fontee.type', null) ?? $this->getConfigFromDb('fontee_notification_type', 'whatsapp'));
    }

    /**
     * Ambil config dari DB; anggap NULL sebagai aktif (compat data lama).
     */
    private function getConfigFromDb(string $key, $default = null)
    {
        $row = FonteeConfig::query()
            ->where('config_key', $key)
            ->where(function ($q) {
                $q->whereNull('is_active')->orWhere('is_active', 1);
            })
            ->first();

        return $row?->config_value ?? $default;
    }

    /**
     * Non-blocking: masukkan ke antrean (job).
     * Idempoten: jika sudah terkirim & ada message_id, skip re-queue.
     */
    public function sendNotification(OwnerNotification $notification): bool
    {
        if (!$this->isEnabled) {
            Log::info('Fontee disabled, skip sending notification', ['notification_id' => $notification->id]);
            return false;
        }

        if (!$this->apiKey || !$this->channelId) {
            Log::error('Fontee credentials missing', [
                'api_key_exists' => !empty($this->apiKey),
                'channel_id_exists' => !empty($this->channelId),
            ]);
            return false;
        }

        if ($notification->fontee_status === 'sent' && !empty($notification->fontee_message_id)) {
            Log::info('Fontee already sent, skip re-queue', ['notification_id' => $notification->id]);
            return true;
        }

        // kamu bisa ubah ke: SendFonteeNotificationJob::dispatch($notification)->onQueue('notifications');
        Queue::dispatch(new SendFonteeNotificationJob($notification));
        Log::info('Fontee notification queued', ['notification_id' => $notification->id]);

        return true;
    }

    /**
     * Dipanggil dari Job. Menangani call HTTP + update status.
     * Tetap idempoten bila job terduplikasi.
     */
    public function send(OwnerNotification $notification): bool
    {
        try {
            if ($notification->fontee_status === 'sent' && !empty($notification->fontee_message_id)) {
                Log::info('Fontee already sent (job)', ['notification_id' => $notification->id]);
                return true;
            }

            if (!$notification->user || empty($notification->user->phone_number)) {
                Log::warning('User has no phone number', ['user_id' => $notification->user_id]);
                $notification->update([
                    'fontee_status' => 'failed',
                    'fontee_error_message' => 'No phone number',
                ]);
                return false;
            }

            $payload = $this->buildPayload($notification);

            $timeout = (int) config('fontee.timeout', config('fontee.timeout_seconds', 30));
            $attempts = (int) config('fontee.retry_attempts', 3);
            $delayMs = (int) config('fontee.retry_delay_ms', 1000);

            $response = Http::withHeaders([
                'Authorization' => "Bearer {$this->apiKey}",
                'Content-Type' => 'application/json',
            ])
                ->timeout($timeout)
                ->retry($attempts, $delayMs)
                ->post("{$this->baseUrl}/messages/send", $payload);

            if ($response->successful()) {
                $data = $response->json();

                $notification->update([
                    'fontee_message_id' => $data['message_id'] ?? ($data['id'] ?? null),
                    'fontee_status' => 'sent',
                    'fontee_sent_at' => now(),
                    'fontee_error_message' => null,
                ]);

                Log::info('Fontee message sent successfully', [
                    'notification_id' => $notification->id,
                    'message_id' => $notification->fontee_message_id,
                ]);

                return true;
            }

            // Error dari API (body aman)
            $json = $response->json();
            $error = is_array($json) ? $json['error'] ?? ($json['message'] ?? 'Unknown error') : 'Unknown error';

            $notification->update([
                'fontee_status' => 'failed',
                'fontee_error_message' => mb_strimwidth((string) $error, 0, 300, '…'),
            ]);

            Log::error('Fontee API error', [
                'notification_id' => $notification->id,
                'status_code' => $response->status(),
                'error' => $error,
            ]);

            return false;
        } catch (\Throwable $e) {
            Log::error('Fontee send exception', [
                'notification_id' => $notification->id,
                'exception' => $e->getMessage(),
            ]);

            $notification->update([
                'fontee_status' => 'failed',
                'fontee_error_message' => mb_strimwidth($e->getMessage(), 0, 300, '…'),
            ]);

            return false;
        }
    }

    /**
     * Payload builder berdasarkan tipe (WA/SMS).
     */
    private function buildPayload(OwnerNotification $notification): array
    {
        $phoneNumber = $this->normalizePhoneNumber($notification->user->phone_number);

        if ($this->notificationType === 'sms') {
            return $this->buildSmsPayload($notification, $phoneNumber);
        }

        // default: whatsapp
        return $this->buildWhatsappPayload($notification, $phoneNumber);
    }

    private function buildWhatsappPayload(OwnerNotification $notification, string $phoneNumber): array
    {
        $message = $this->formatWhatsappMessage($notification);

        return [
            'channel_id' => $this->channelId,
            'to' => $phoneNumber,
            'type' => 'whatsapp',
            'template' => 'freeform',
            'message' => $message,
            'reference_id' => "notif_{$notification->id}",
            'metadata' => [
                'notification_id' => $notification->id,
                'sale_id' => $notification->sale_id,
                'type' => $notification->notification_type,
            ],
        ];
    }

    private function buildSmsPayload(OwnerNotification $notification, string $phoneNumber): array
    {
        $message = substr(trim(($notification->title ?? '') . ' - ' . ($notification->message ?? '')), 0, 160);

        return [
            'channel_id' => $this->channelId,
            'to' => $phoneNumber,
            'type' => 'sms',
            'message' => $message,
            'reference_id' => "notif_{$notification->id}",
            'metadata' => [
                'notification_id' => $notification->id,
                'sale_id' => $notification->sale_id,
            ],
        ];
    }

    private function formatWhatsappMessage(OwnerNotification $notification): string
    {
        $title = (string) ($notification->title ?? 'Notifikasi');
        $body = (string) ($notification->message ?? '');
        $url = route('notifications.show', $notification->id); // pastikan APP_URL ter-set

        $msg = '🔔 *' . $title . "*\n\n";
        $msg .= $body . "\n\n";
        $msg .= '👉 Review: ' . $url . "\n";
        $msg .= "\n_Pesan otomatis dari sistem POS_";

        return $msg;
    }

    /**
     * Normalisasi nomor telepon (+62…).
     */
    private function normalizePhoneNumber(?string $phone): string
    {
        $phone = (string) $phone;
        $phone = preg_replace('/\s+/', '', $phone ?? '');
        $phone = preg_replace('/[^0-9\+]/', '', $phone);

        if ($phone === '') {
            return '';
        }

        if (preg_match('/^62\d+$/', $phone)) {
            return '+' . $phone;
        }

        if (str_starts_with($phone, '0')) {
            return '+62' . substr($phone, 1);
        }

        if (str_starts_with($phone, '+')) {
            return $phone;
        }

        return '+' . $phone;
    }

    /**
     * Webhook handler: validasi signature (ENV → DB), update status.
     */
    public function handleWebhook(array $payload, ?string $signature): bool
    {
        try {
            if (!$this->validateWebhookSignature($payload, $signature)) {
                Log::warning('Invalid webhook signature');
                return false;
            }

            $messageId = $payload['message_id'] ?? ($payload['id'] ?? null);
            $status = $payload['status'] ?? null;

            if (!$messageId || !$status) {
                return false;
            }

            $notification = OwnerNotification::where('fontee_message_id', $messageId)->first();
            if ($notification) {
                $mapped = $this->mapFonteeStatus((string) $status);
                $notification->update(['fontee_status' => $mapped]);

                if ($mapped === 'read' && !$notification->is_read) {
                    $notification->markAsRead();
                }
            }

            return true;
        } catch (\Throwable $e) {
            Log::error('Webhook handling error', ['e' => $e->getMessage()]);
            return false;
        }
    }

    private function validateWebhookSignature(array $payload, ?string $signature): bool
    {
        // ENV → DB
        $secret = config('fontee.webhook_secret') ?? $this->getConfigFromDb('fontee_webhook_secret');
        if (!$signature || !$secret) {
            return false;
        }

        $body = json_encode($payload, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
        $hash = hash_hmac('sha256', $body, (string) $secret);

        return hash_equals($hash, (string) $signature);
    }

    private function mapFonteeStatus(string $fonteeStatus): string
    {
        $s = strtolower($fonteeStatus);
        $map = [
            'pending' => 'pending',
            'queued' => 'pending',
            'sent' => 'sent',
            'delivered' => 'sent',
            'read' => 'read',
            'seen' => 'read',
            'failed' => 'failed',
            'error' => 'failed',
        ];
        return $map[$s] ?? 'pending';
    }
}
