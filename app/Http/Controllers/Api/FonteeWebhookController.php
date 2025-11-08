<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\FonteeNotificationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class FonteeWebhookController extends Controller
{
    public function handle(Request $request, FonteeNotificationService $service)
    {
        $payload = $request->all();
        $signature = $request->header('X-Fontee-Signature'); // sesuaikan dokumen Fontee

        $ok = $service->handleWebhook($payload, $signature);

        if (!$ok) {
            Log::warning('Fontee webhook not processed', ['payload' => $payload]);
            return response()->json(['ok' => false], 400);
        }

        return response()->json(['ok' => true]);
    }
}
