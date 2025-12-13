<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\Midtrans\CallbackService;
use Illuminate\Http\Request;

class MidtransCallbackController extends Controller
{
    public function receive(Request $request)
    {
        try {
            $callback = new CallbackService();

            if ($callback->isSignatureKeyVerified()) {
                $callback->updateSale();

                return response()->json(
                    [
                        'success' => true,
                        'message' => 'Payment notification processed',
                    ],
                    200,
                );
            } else {
                \Log::warning('Midtrans Callback: Invalid signature');
                return response()->json(
                    [
                        'success' => false,
                        'message' => 'Invalid signature',
                    ],
                    403,
                );
            }
        } catch (\Exception $e) {
            \Log::error('Midtrans Callback Error: ' . $e->getMessage());
            return response()->json(
                [
                    'success' => false,
                    'message' => 'Error processing callback: ' . $e->getMessage(),
                ],
                500,
            );
        }
    }
}
