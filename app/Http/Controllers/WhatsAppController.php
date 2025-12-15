<?php

namespace App\Http\Controllers;

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

        return view('whatsapp.settings', [
            'status' => $status,
            'qrData' => $qrData,
            'driver' => config('whatsapp.driver'),
            'ownerPhone' => config('whatsapp.owner_phone'),
            'baileysUrl' => config('whatsapp.baileys.base_url'),
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
}
