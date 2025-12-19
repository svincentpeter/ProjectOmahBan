<?php

namespace App\Http\Controllers;

use App\Models\NotificationSetting;
use App\Models\NotificationRecipient;
use App\Services\WhatsApp\BaileysNotificationService;
use Illuminate\Http\Request;

class WhatsAppController extends Controller
{
    protected BaileysNotificationService $whatsapp;

    public function __construct(BaileysNotificationService $whatsapp)
    {
        $this->whatsapp = $whatsapp;
        $this->middleware('auth');
        $this->middleware('permission:access_settings')->except(['status']);
    }

    /**
     * WhatsApp Settings Page
     */
    public function settings()
    {
        $status = $this->whatsapp->getStatus();
        $qrData = null;

        if (!($status['connected'] ?? false)) {
            $qrData = $this->whatsapp->getQrCode();
        }

        // Get notification settings and recipients
        $notificationSettings = NotificationSetting::all();
        $recipients = NotificationRecipient::all();

        return view('whatsapp.settings', [
            'status' => $status,
            'qrData' => $qrData,
            'driver' => config('whatsapp.driver'),
            'ownerPhone' => config('whatsapp.owner_phone'),
            'baileysUrl' => config('whatsapp.baileys.base_url'),
            'notificationSettings' => $notificationSettings,
            'recipients' => $recipients,
        ]);
    }

    /**
     * Get status via AJAX
     */
    public function status()
    {
        return response()->json($this->whatsapp->getStatus());
    }

    /**
     * Get QR code via AJAX
     */
    public function qrCode()
    {
        $qrData = $this->whatsapp->getQrCode();
        return response()->json($qrData ?? ['success' => false, 'message' => 'QR not available']);
    }

    /**
     * Send test message
     */
    public function testMessage(Request $request)
    {
        $request->validate([
            'phone' => 'required|string',
            'message' => 'required|string|max:1000',
        ]);

        $result = $this->whatsapp->sendMessage(
            $request->phone,
            $request->message
        );

        return response()->json($result);
    }

    /**
     * Notify owner
     */
    public function notifyOwner(Request $request)
    {
        $request->validate([
            'message' => 'required|string|max:1000',
            'title' => 'nullable|string|max:100',
        ]);

        $result = $this->whatsapp->notifyOwner(
            $request->message,
            $request->title
        );

        return response()->json($result);
    }

    /**
     * Reconnect WhatsApp
     */
    public function reconnect()
    {
        $result = $this->whatsapp->reconnect();
        return response()->json($result);
    }

    /**
     * Disconnect WhatsApp
     */
    public function disconnect()
    {
        $result = $this->whatsapp->disconnect();
        return response()->json($result);
    }

    /**
     * Toggle notification setting
     */
    public function toggleNotification(Request $request, NotificationSetting $setting)
    {
        $setting->update(['is_enabled' => !$setting->is_enabled]);

        return response()->json([
            'success' => true,
            'is_enabled' => $setting->is_enabled,
            'message' => $setting->is_enabled 
                ? "Notifikasi '{$setting->label}' diaktifkan" 
                : "Notifikasi '{$setting->label}' dinonaktifkan"
        ]);
    }

    /**
     * Update notification template
     */
    public function updateTemplate(Request $request, NotificationSetting $setting)
    {
        $request->validate([
            'template' => 'required|string|max:2000',
        ]);

        $setting->update(['template' => $request->template]);

        return response()->json([
            'success' => true,
            'message' => "Template '{$setting->label}' berhasil diperbarui"
        ]);
    }

    /**
     * Reset template to default
     */
    public function resetTemplate(NotificationSetting $setting)
    {
        // Re-run seeder for this type
        $defaults = $this->getDefaultTemplates();
        
        if (isset($defaults[$setting->type])) {
            $setting->update(['template' => $defaults[$setting->type]]);
            return response()->json([
                'success' => true,
                'template' => $setting->template,
                'message' => "Template '{$setting->label}' direset ke default"
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => 'Default template tidak ditemukan'
        ], 404);
    }

    /**
     * Get default templates
     */
    protected function getDefaultTemplates(): array
    {
        return [
            'manual_input' => "🔔 *⚠️ Input Manual - Inv {invoice}*\n\nKasir *{cashier}* membuat transaksi dengan *{item_count} item* input manual:\n\n{items_list}\n\n💰 *Total: Rp {total}*\n📋 Invoice: {invoice}\n⏰ Waktu: {datetime}",
            'low_stock' => "⚠️ *ALERT STOK RENDAH*\n\nDitemukan *{product_count} produk* dengan stok rendah:\n\n{products_list}\n\n📦 Segera lakukan restok!",
            'daily_report' => "📊 *LAPORAN HARIAN*\n📅 {date}\n\n💰 Total Penjualan: *Rp {total_sales}*\n🧾 Jumlah Transaksi: *{transaction_count}*\n💵 Total Tunai: Rp {cash_total}\n💳 Total Transfer: Rp {transfer_total}\n📉 Total Pengeluaran: Rp {expense_total}\n💎 Laba Bersih: *Rp {net_profit}*\n🏆 Produk Terlaris: {top_product}",
            'login_alert' => "🔐 *LOGIN ALERT*\n\nUser *{user_name}* ({role}) telah login pada:\n⏰ {datetime}\n🌐 IP: {ip_address}\n💻 Browser: {browser}",
        ];
    }


    /**
     * Store new recipient
     */
    public function storeRecipient(Request $request)
    {
        $request->validate([
            'recipient_name' => 'required|string|max:255',
            'recipient_phone' => 'required|string|max:20',
            'permissions' => 'nullable|array',
        ]);

        NotificationRecipient::create([
            'recipient_name' => $request->recipient_name,
            'recipient_phone' => $request->recipient_phone,
            'permissions' => $request->permissions ?? [],
            'is_active' => true,
        ]);

        return redirect()->back()->with('success', 'Penerima notifikasi berhasil ditambahkan');
    }

    /**
     * Update recipient
     */
    public function updateRecipient(Request $request, NotificationRecipient $recipient)
    {
        $request->validate([
            'recipient_name' => 'required|string|max:255',
            'recipient_phone' => 'required|string|max:20',
            'permissions' => 'nullable|array',
            'is_active' => 'boolean',
        ]);

        $recipient->update([
            'recipient_name' => $request->recipient_name,
            'recipient_phone' => $request->recipient_phone,
            'permissions' => $request->permissions ?? [],
            'is_active' => $request->has('is_active'),
        ]);

        return redirect()->back()->with('success', 'Data penerima berhasil diperbarui');
    }

    /**
     * Delete recipient
     */
    public function deleteRecipient(NotificationRecipient $recipient)
    {
        $recipient->delete();
        return redirect()->back()->with('success', 'Penerima berhasil dihapus');
    }
}
