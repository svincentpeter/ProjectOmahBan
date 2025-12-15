<?php

namespace App\Services\WhatsApp;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use App\Models\OwnerNotification;

class BaileysNotificationService
{
    protected string $baseUrl;
    protected string $apiKey;
    protected int $timeout;
    protected bool $enabled;

    public function __construct()
    {
        $this->baseUrl = rtrim(config('whatsapp.baileys.base_url', 'http://localhost:3001'), '/');
        $this->apiKey = config('whatsapp.baileys.api_key', '');
        $this->timeout = config('whatsapp.baileys.timeout', 30);
        $this->enabled = config('whatsapp.driver') === 'baileys';
    }

    /**
     * Check if Baileys service is enabled
     */
    public function isEnabled(): bool
    {
        return $this->enabled;
    }

    /**
     * Get connection status from Baileys service
     */
    public function getStatus(): array
    {
        try {
            $response = Http::timeout($this->timeout)
                ->get("{$this->baseUrl}/status");

            if ($response->successful()) {
                return $response->json();
            }

            return [
                'success' => false,
                'status' => 'error',
                'connected' => false,
                'error' => 'Failed to get status'
            ];
        } catch (\Exception $e) {
            Log::error('Baileys status check failed', ['error' => $e->getMessage()]);
            return [
                'success' => false,
                'status' => 'offline',
                'connected' => false,
                'error' => 'Service tidak berjalan. Pastikan whatsapp-service sudah dijalankan.'
            ];
        }
    }

    /**
     * Check if WhatsApp is connected
     */
    public function isConnected(): bool
    {
        $status = $this->getStatus();
        return $status['connected'] ?? false;
    }

    /**
     * Get QR code for authentication
     */
    public function getQrCode(): ?array
    {
        try {
            $response = Http::timeout($this->timeout)
                ->withHeaders(['X-API-Key' => $this->apiKey])
                ->get("{$this->baseUrl}/qr");

            if ($response->successful()) {
                return $response->json();
            }

            return null;
        } catch (\Exception $e) {
            Log::error('Failed to get QR code', ['error' => $e->getMessage()]);
            return null;
        }
    }

    /**
     * Send message to a phone number
     *
     * @param string $phone Phone number (08xx atau 62xxx)
     * @param string $message Message text
     * @return array
     */
    public function sendMessage(string $phone, string $message): array
    {
        if (!$this->enabled) {
            return [
                'success' => false,
                'error' => 'Baileys service is disabled'
            ];
        }

        try {
            $response = Http::timeout($this->timeout)
                ->withHeaders(['X-API-Key' => $this->apiKey])
                ->post("{$this->baseUrl}/send", [
                    'phone' => $this->normalizePhone($phone),
                    'message' => $message
                ]);

            $data = $response->json();

            if ($response->successful() && ($data['success'] ?? false)) {
                Log::info('WhatsApp message sent via Baileys', [
                    'phone' => $phone,
                    'messageId' => $data['messageId'] ?? null
                ]);
                return $data;
            }

            Log::warning('WhatsApp message failed', [
                'phone' => $phone,
                'error' => $data['error'] ?? 'Unknown error'
            ]);

            return [
                'success' => false,
                'error' => $data['error'] ?? 'Failed to send message'
            ];
        } catch (\Exception $e) {
            Log::error('Baileys send message exception', [
                'phone' => $phone,
                'error' => $e->getMessage()
            ]);

            return [
                'success' => false,
                'error' => $e->getMessage()
            ];
        }
    }

    /**
     * Send notification to owner
     */
    public function notifyOwner(string $message, ?string $title = null): array
    {
        if (!$this->enabled) {
            return ['success' => false, 'error' => 'Baileys disabled'];
        }

        try {
            $response = Http::timeout($this->timeout)
                ->withHeaders(['X-API-Key' => $this->apiKey])
                ->post("{$this->baseUrl}/notify-owner", [
                    'message' => $message,
                    'title' => $title
                ]);

            return $response->json();
        } catch (\Exception $e) {
            Log::error('Failed to notify owner', ['error' => $e->getMessage()]);
            return ['success' => false, 'error' => $e->getMessage()];
        }
    }

    /**
     * Send OwnerNotification via Baileys WhatsApp
     */
    public function sendNotification(OwnerNotification $notification): bool
    {
        if (!$this->enabled) {
            Log::info('Baileys disabled, skipping notification', ['id' => $notification->id]);
            return false;
        }

        if (!$this->isConnected()) {
            Log::warning('WhatsApp not connected, skipping notification', ['id' => $notification->id]);
            return false;
        }

        // Prioritas: user phone → fallback ke owner phone dari config
        $phone = $notification->user?->phone_number ?? config('whatsapp.owner_phone');
        if (!$phone) {
            Log::warning('No phone number for notification', ['id' => $notification->id]);
            return false;
        }

        $message = $this->formatNotificationMessage($notification);
        $result = $this->sendMessage($phone, $message);

        if ($result['success'] ?? false) {
            $notification->update([
                'whatsapp_status' => 'sent',
                'whatsapp_sent_at' => now(),
                'whatsapp_message_id' => $result['messageId'] ?? null,
            ]);
            Log::info('WhatsApp notification sent', [
                'notification_id' => $notification->id,
                'phone' => $phone,
                'messageId' => $result['messageId'] ?? null,
            ]);
            return true;
        }

        $notification->update([
            'whatsapp_status' => 'failed',
            'whatsapp_error_message' => $result['error'] ?? 'Unknown error',
        ]);

        Log::error('WhatsApp notification failed', [
            'notification_id' => $notification->id,
            'error' => $result['error'] ?? 'Unknown error',
        ]);

        return false;
    }

    /**
     * Send low stock alert
     */
    public function sendLowStockAlert(array $products): array
    {
        if (empty($products)) {
            return ['success' => false, 'error' => 'No products to alert'];
        }

        $message = "⚠️ *ALERT STOK RENDAH*\n\n";
        $message .= "Ditemukan " . count($products) . " produk dengan stok rendah:\n\n";

        $count = 0;
        foreach ($products as $product) {
            if ($count >= 10) break;
            
            $status = ($product['quantity'] ?? 0) <= 0 ? '🔴' : '🟡';
            $name = $product['name'] ?? $product['product_name'] ?? 'Unknown';
            $qty = $product['quantity'] ?? $product['product_quantity'] ?? 0;
            $message .= "{$status} {$name}: {$qty} unit\n";
            $count++;
        }

        if (count($products) > 10) {
            $message .= "\n...dan " . (count($products) - 10) . " produk lainnya.";
        }

        $message .= "\n\n📦 Segera lakukan restok!";
        $message .= $this->getBotSignature();

        return $this->notifyOwner($message, 'Alert Stok Rendah');
    }

    /**
     * Send daily report summary
     */
    public function sendDailyReport(array $data): array
    {
        $date = $data['date'] ?? now()->format('d M Y');
        $totalSales = number_format($data['total_sales'] ?? 0, 0, ',', '.');
        $transactionCount = $data['transaction_count'] ?? 0;
        $topProduct = $data['top_product'] ?? '-';

        $message = "📊 *LAPORAN HARIAN*\n";
        $message .= "📅 {$date}\n\n";
        $message .= "💰 Total Penjualan: Rp {$totalSales}\n";
        $message .= "🧾 Jumlah Transaksi: {$transactionCount}\n";
        $message .= "🏆 Produk Terlaris: {$topProduct}";
        $message .= $this->getBotSignature();

        return $this->notifyOwner($message, 'Laporan Harian');
    }

    /**
     * Reconnect WhatsApp
     */
    public function reconnect(): array
    {
        try {
            $response = Http::timeout($this->timeout)
                ->withHeaders(['X-API-Key' => $this->apiKey])
                ->post("{$this->baseUrl}/reconnect");

            return $response->json();
        } catch (\Exception $e) {
            return ['success' => false, 'error' => $e->getMessage()];
        }
    }

    /**
     * Disconnect WhatsApp
     */
    public function disconnect(): array
    {
        try {
            $response = Http::timeout($this->timeout)
                ->withHeaders(['X-API-Key' => $this->apiKey])
                ->post("{$this->baseUrl}/disconnect");

            return $response->json();
        } catch (\Exception $e) {
            return ['success' => false, 'error' => $e->getMessage()];
        }
    }

    /**
     * Normalize phone number to 62xxx format
     */
    protected function normalizePhone(string $phone): string
    {
        $phone = preg_replace('/[^0-9]/', '', $phone);
        
        if (str_starts_with($phone, '0')) {
            $phone = '62' . substr($phone, 1);
        }
        
        if (!str_starts_with($phone, '62')) {
            $phone = '62' . $phone;
        }
        
        return $phone;
    }

    /**
     * Format notification message for WhatsApp
     */
    protected function formatNotificationMessage(OwnerNotification $notification): string
    {
        $title = $notification->title ?? 'Notifikasi';
        $body = $notification->message ?? '';
        $url = route('notifications.show', $notification->id);

        $msg = "🔔 *{$title}*\n\n";
        $msg .= "{$body}\n\n";
        $msg .= "👉 Detail: {$url}";
        $msg .= $this->getBotSignature();

        return $msg;
    }

    /**
     * Get bot signature/footer for messages
     */
    protected function getBotSignature(): string
    {
        $config = config('whatsapp.signature', []);
        
        $botName = $config['bot_name'] ?? '🤖 Bot Omah Ban POS';
        $divider = $config['divider'] ?? '━━━━━━━━━━━━━━━━━━━━━';
        $footerTemplate = $config['footer'] ?? 'Pesan ini dikirim otomatis oleh {bot_name}. Jangan balas pesan ini.';
        $showTimestamp = $config['show_timestamp'] ?? true;
        $timestampFormat = $config['timestamp_format'] ?? 'd M Y, H:i';
        
        // Replace placeholder
        $footer = str_replace('{bot_name}', $botName, $footerTemplate);
        
        $signature = "\n\n{$divider}\n";
        $signature .= "*{$botName}*\n";
        $signature .= "_{$footer}_";
        
        if ($showTimestamp) {
            $timestamp = now()->format($timestampFormat);
            $signature .= "\n⏰ {$timestamp}";
        }
        
        return $signature;
    }
}
