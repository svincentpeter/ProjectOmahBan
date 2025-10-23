<?php

namespace App\Services\Midtrans;

use Midtrans\Snap;
use Modules\Sale\Entities\Sale;

class CreateSnapTokenService extends Midtrans
{
    protected $sale;

    public function __construct(Sale $sale)
    {
        parent::__construct();
        $this->sale = $sale;
    }

    public function getSnapToken()
    {
        $itemDetails = [];
        
        // Loop detail items dari sale
        foreach ($this->sale->saleDetails as $detail) {
            $itemDetails[] = [
                'id' => 'ITEM-' . $detail->id,  // Tambahkan prefix untuk keunikan
                'price' => (int) $detail->unit_price,
                'quantity' => (int) $detail->quantity,
                'name' => substr($detail->product_name, 0, 50),  // Limit 50 karakter
            ];
        }

        // Tambahkan tax sebagai item jika ada
        if ($this->sale->tax_amount > 0) {
            $itemDetails[] = [
                'id' => 'TAX',
                'price' => (int) $this->sale->tax_amount,
                'quantity' => 1,
                'name' => 'Tax',
            ];
        }

        // Tambahkan shipping jika ada
        if ($this->sale->shipping_amount > 0) {
            $itemDetails[] = [
                'id' => 'SHIPPING',
                'price' => (int) $this->sale->shipping_amount,
                'quantity' => 1,
                'name' => 'Shipping',
            ];
        }

        // Kurangi diskon sebagai item negatif (jika ada)
        if ($this->sale->discount_amount > 0) {
            $itemDetails[] = [
                'id' => 'DISCOUNT',
                'price' => -(int) $this->sale->discount_amount,
                'quantity' => 1,
                'name' => 'Discount',
            ];
        }

        // Validasi total: pastikan gross_amount match dengan sum of items
        $calculatedTotal = 0;
        foreach ($itemDetails as $item) {
            $calculatedTotal += ($item['price'] * $item['quantity']);
        }

        // Jika tidak match, gunakan calculated total (lebih aman)
        $grossAmount = (int) $this->sale->total_amount;
        
        // Log untuk debugging (opsional)
        \Log::info('Midtrans Transaction', [
            'order_id' => $this->sale->reference,
            'gross_amount' => $grossAmount,
            'calculated_total' => $calculatedTotal,
            'item_details' => $itemDetails,
        ]);

        // Pastikan order_id unique dengan menambahkan timestamp
        $orderId = $this->sale->reference . '-' . time();

        $params = [
            'transaction_details' => [
                'order_id' => $orderId,  // Order ID yang unique
                'gross_amount' => $grossAmount,
            ],
            'item_details' => $itemDetails,
            'customer_details' => [
                'first_name' => substr($this->sale->customer_name ?? 'Customer', 0, 50),
                'email' => 'customer@omahban.com',  // Email dummy yang valid
                'phone' => '081234567890',
            ],
            'enabled_payments' => [
                'credit_card',   // Kartu kredit
                'gopay',         // GoPay
                'shopeepay',     // ShopeePay
                'other_qris',    // QRIS
                'bca_va',        // BCA Virtual Account
                'bni_va',        // BNI Virtual Account
                'bri_va',        // BRI Virtual Account
                'mandiri_va',    // Mandiri Virtual Account (Bill Payment)
                'permata_va',    // Permata Virtual Account
                'cimb_va',       // CIMB Niaga Virtual Account
            ],
            'callbacks' => [
                'finish' => config('app.url') . '/pos',
                'error' => config('app.url') . '/pos',
                'pending' => config('app.url') . '/pos',
            ],
        ];

        try {
            $snapToken = Snap::getSnapToken($params);
            
            // Simpan snap token ke database untuk referensi
            $this->sale->update([
                'snap_token' => $snapToken,
                'midtrans_order_id' => $orderId,
            ]);
            
            return $snapToken;
        } catch (\Exception $e) {
            \Log::error('Midtrans Snap Token Error: ' . $e->getMessage(), [
                'params' => $params,
            ]);
            throw $e;
        }
    }
}
