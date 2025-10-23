<?php

namespace App\Services\Midtrans;

use Midtrans\Notification;
use Modules\Sale\Entities\Sale;
use Modules\Sale\Entities\SalePayment;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class CallbackService extends Midtrans
{
    protected $notification;
    protected $sale;
    protected $serverKey;

    public function __construct()
    {
        parent::__construct();
        $this->serverKey = config('midtrans.server_key');
        $this->_handleNotification();
    }

    protected function _handleNotification()
    {
        try {
            $notification = new Notification();
            $this->notification = $notification;

            // Extract reference dari order_id
            // Format order_id dari Midtrans: OB2-00072-1761057126 (reference-timestamp)
            $orderId = $notification->order_id;
            
            // Split berdasarkan '-' dan ambil 2 part pertama sebagai reference
            $parts = explode('-', $orderId);
            
            if (count($parts) >= 3) {
                // Format: PREFIX-NUMBER-TIMESTAMP -> ambil PREFIX-NUMBER
                $reference = $parts[0] . '-' . $parts[1];
            } else {
                // Fallback jika format berbeda
                $reference = $orderId;
            }

            // Cari sale berdasarkan reference ATAU midtrans_transaction_id
            $this->sale = Sale::where('reference', $reference)
                            ->orWhere('midtrans_transaction_id', $orderId)
                            ->first();

            if (!$this->sale) {
                Log::error('Midtrans Callback: Sale not found', [
                    'order_id' => $orderId,
                    'extracted_reference' => $reference,
                    'parts' => $parts,
                ]);
                return;
            }

            Log::info('Midtrans Callback Received', [
                'order_id' => $orderId,
                'reference' => $reference,
                'sale_id' => $this->sale->id,
                'transaction_status' => $notification->transaction_status,
                'payment_type' => $notification->payment_type,
                'fraud_status' => $notification->fraud_status ?? 'N/A',
            ]);

        } catch (\Exception $e) {
            Log::error('Midtrans Callback Exception: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString()
            ]);
        }
    }

    public function isSignatureKeyVerified()
    {
        try {
            $localSignature = $this->_createLocalSignatureKey();
            $notificationSignature = $this->notification->signature_key;

            Log::info('Signature Verification', [
                'local' => $localSignature,
                'notification' => $notificationSignature,
                'match' => $localSignature === $notificationSignature,
            ]);

            return ($localSignature === $notificationSignature);
        } catch (\Exception $e) {
            Log::error('Signature Verification Error: ' . $e->getMessage());
            return false;
        }
    }

    protected function _createLocalSignatureKey()
    {
        $orderId = $this->notification->order_id;
        $statusCode = $this->notification->status_code;
        $grossAmount = $this->notification->gross_amount;
        $serverKey = $this->serverKey;
        
        $input = $orderId . $statusCode . $grossAmount . $serverKey;
        $signature = openssl_digest($input, 'sha512');
        
        return $signature;
    }

    public function isSuccess()
    {
        $statusCode = $this->notification->status_code;
        $transactionStatus = $this->notification->transaction_status;
        $fraudStatus = $this->notification->fraud_status ?? 'accept';

        // Status 200 dengan transaction_status 'capture' atau 'settlement' = sukses
        return ($statusCode == '200' 
                && $fraudStatus == 'accept' 
                && ($transactionStatus == 'capture' || $transactionStatus == 'settlement'));
    }

    public function isExpire()
    {
        return ($this->notification->transaction_status == 'expire');
    }

    public function isCancelled()
    {
        return ($this->notification->transaction_status == 'cancel');
    }

    public function isPending()
    {
        return ($this->notification->transaction_status == 'pending');
    }

    public function updateSale()
    {
        if (!$this->sale) {
            Log::warning('UpdateSale: Sale not found, skipping update');
            return false;
        }

        DB::beginTransaction();
        try {
            $transactionStatus = $this->notification->transaction_status;
            $paymentType = $this->notification->payment_type;
            $transactionId = $this->notification->transaction_id;
            $grossAmount = (int) $this->notification->gross_amount;

            // Map payment_type ke enum database
            $paymentTypeMap = [
                'credit_card' => 'credit_card',
                'gopay' => 'gopay',
                'shopeepay' => 'shopeepay',
                'qris' => 'qris',
                'bank_transfer' => 'bank_transfer',
                'echannel' => 'bank_transfer',
                'permata' => 'bank_transfer',
                'bca_va' => 'bank_transfer',
                'bni_va' => 'bank_transfer',
                'bri_va' => 'bank_transfer',
                'other_va' => 'bank_transfer',
                'cstore' => 'other',
            ];

            $mappedPaymentType = $paymentTypeMap[$paymentType] ?? 'other';

            if ($this->isSuccess()) {
                // PAYMENT SUKSES
                $this->sale->payment_status = 'Paid';
                $this->sale->status = 'Completed';
                $this->sale->paid_at = now();
                $this->sale->midtrans_transaction_id = $transactionId;
                $this->sale->midtrans_payment_type = $mappedPaymentType;
                $this->sale->paid_amount = $grossAmount;
                $this->sale->due_amount = 0;
                $this->sale->save();

                // Buat record SalePayment jika belum ada
                $existingPayment = SalePayment::where('sale_id', $this->sale->id)
                                              ->where('reference', 'LIKE', '%MIDTRANS%')
                                              ->first();

                if (!$existingPayment) {
                    SalePayment::create([
                        'sale_id' => $this->sale->id,
                        'date' => now()->toDateString(),
                        'reference' => 'MIDTRANS/' . $transactionId,
                        'amount' => $grossAmount,
                        'payment_method' => 'Midtrans - ' . ucfirst($paymentType),
                        'note' => 'Paid via Midtrans ' . ucfirst($paymentType),
                    ]);
                }

                // Recalculate payment status (jika method ada)
                if (method_exists($this->sale, 'recalcPaymentAndStatus')) {
                    $this->sale->recalcPaymentAndStatus();
                }

                Log::info('Sale Updated to PAID', [
                    'sale_id' => $this->sale->id,
                    'reference' => $this->sale->reference,
                    'transaction_id' => $transactionId,
                    'payment_type' => $paymentType,
                ]);

            } elseif ($this->isPending()) {
                // PAYMENT PENDING
                $this->sale->payment_status = 'Unpaid';
                $this->sale->status = 'Pending';
                $this->sale->midtrans_payment_type = $mappedPaymentType;
                $this->sale->save();

                Log::info('Sale Status: PENDING', [
                    'sale_id' => $this->sale->id,
                ]);

            } elseif ($this->isExpire()) {
                // PAYMENT EXPIRED
                $this->sale->payment_status = 'Unpaid';
                $this->sale->snap_token = null; // Reset token agar bisa generate ulang
                $this->sale->midtrans_transaction_id = null;
                $this->sale->save();

                Log::info('Sale Status: EXPIRED', [
                    'sale_id' => $this->sale->id,
                ]);
                
            } elseif ($this->isCancelled()) {
                // PAYMENT CANCELLED
                $this->sale->payment_status = 'Unpaid';
                $this->sale->snap_token = null;
                $this->sale->midtrans_transaction_id = null;
                $this->sale->save();

                Log::info('Sale Status: CANCELLED', [
                    'sale_id' => $this->sale->id,
                ]);
            }

            DB::commit();
            return true;

        } catch (\Throwable $e) {
            DB::rollBack();
            Log::error('Midtrans UpdateSale Error: ' . $e->getMessage(), [
                'sale_id' => $this->sale->id ?? null,
                'trace' => $e->getTraceAsString(),
            ]);
            return false;
        }
    }

    public function getSale()
    {
        return $this->sale;
    }

    public function getNotification()
    {
        return $this->notification;
    }
}
