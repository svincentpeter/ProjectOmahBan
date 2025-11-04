<?php

namespace App\Helpers;

use Modules\Product\Entities\ServiceMaster;
use Modules\Sale\Entities\PriceVarianceLog;

class PriceValidationHelper
{
    /**
     * Validasi harga input vs master price
     *
     * @param string $serviceName - Nama jasa (harus sama dengan di master)
     * @param int $inputPrice - Harga yang diinput kasir
     * @param int $cashierId - ID kasir
     * @return array - ['valid' => bool, 'action' => 'proceed|warning|critical', 'data' => array]
     */
    public static function validateServicePrice(string $serviceName, int $inputPrice, int $cashierId): array
    {
        // Cari jasa di master data
        $master = ServiceMaster::where('service_name', $serviceName)->where('status', 1)->first();

        // Jika jasa tidak ada di master
        if (!$master) {
            return [
                'valid' => false,
                'message' => "Jasa '{$serviceName}' tidak terdaftar di master data.",
                'action' => 'block',
            ];
        }

        $masterPrice = $master->standard_price;

        // Jika master harga = 0 (jasa custom/khusus), proceed tanpa validasi
        if ($masterPrice === 0) {
            return [
                'valid' => true,
                'variance' => null,
                'action' => 'proceed',
            ];
        }

        // Hitung deviasi
        $varianceAmount = $inputPrice - $masterPrice;
        $variancePercent = ($varianceAmount / $masterPrice) * 100;

        // Tentukan level deviasi dan action
        $varianceLevel = 'normal';
        $action = 'proceed';

        if (abs($variancePercent) > 50) {
            // Deviasi >50% = CRITICAL (perlu PIN supervisor)
            $varianceLevel = 'critical';
            $action = 'critical';
        } elseif (abs($variancePercent) > 30) {
            // Deviasi 30-50% = WARNING (perlu alasan)
            // Catatan: diubah dari 20% jadi 30% untuk lebih realistis di UMKM
            $varianceLevel = 'warning';
            $action = 'warning';
        }

        return [
            'valid' => true,
            'master_price' => $masterPrice,
            'input_price' => $inputPrice,
            'variance_amount' => $varianceAmount,
            'variance_percent' => round($variancePercent, 2),
            'variance_level' => $varianceLevel,
            'action' => $action,
        ];
    }

    /**
     * Log deviasi untuk audit trail
     * Dipanggil SETELAH transaksi selesai (saat checkout)
     *
     * @param int $saleId
     * @param int $saleDetailId
     * @param string $itemName
     * @param int $masterPrice
     * @param int $inputPrice
     * @param string $reason - Alasan dari kasir
     * @param int $cashierId
     * @return void
     */
    public static function logVariance(int $saleId, int $saleDetailId, string $itemName, int $masterPrice, int $inputPrice, string $reason, int $cashierId): void
    {
        $varianceAmount = $inputPrice - $masterPrice;
        $variancePercent = $masterPrice > 0 ? ($varianceAmount / $masterPrice) * 100 : 0;

        $varianceLevel = abs($variancePercent) > 50 ? 'critical' : (abs($variancePercent) > 30 ? 'warning' : 'minor');

        PriceVarianceLog::create([
            'sale_id' => $saleId,
            'sale_detail_id' => $saleDetailId,
            'item_name' => $itemName,
            'master_price' => $masterPrice,
            'input_price' => $inputPrice,
            'variance_amount' => $varianceAmount,
            'variance_percent' => round($variancePercent, 2),
            'variance_level' => $varianceLevel,
            'reason_provided' => $reason,
            'approval_status' => 'pending',
            'cashier_id' => $cashierId,
            'created_at' => now(),
        ]);
    }
}
