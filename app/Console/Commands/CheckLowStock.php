<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Modules\Product\Entities\Product;
use App\Services\WhatsApp\BaileysNotificationService;

class CheckLowStock extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'stock:check-low 
                            {--notify : Send notification for low stock items}
                            {--threshold= : Override default threshold}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Check for products with low stock and optionally send notifications';

    /**
     * Execute the console command.
     */
    public function handle(BaileysNotificationService $whatsapp)
    {
        $this->info('🔍 Checking low stock products...');

        $query = Product::query()
            ->whereNull('deleted_at')
            ->whereNotNull('product_stock_alert')
            ->where('product_stock_alert', '>', 0)
            ->whereColumn('product_quantity', '<=', 'product_stock_alert')
            ->orderByRaw('product_quantity - product_stock_alert ASC');

        $lowStockProducts = $query->get([
            'id', 
            'product_code', 
            'product_name', 
            'product_quantity', 
            'product_stock_alert'
        ]);

        if ($lowStockProducts->isEmpty()) {
            $this->info('✅ No low stock products found!');
            return Command::SUCCESS;
        }

        $this->warn("⚠️  Found {$lowStockProducts->count()} products with low stock:");
        $this->newLine();

        $tableData = [];
        $criticalCount = 0;

        foreach ($lowStockProducts as $product) {
            $isCritical = $product->product_quantity <= 0;
            if ($isCritical) $criticalCount++;

            $tableData[] = [
                $product->product_code ?? '-',
                $product->product_name,
                $product->product_quantity,
                $product->product_stock_alert,
                $isCritical ? '🔴 HABIS' : '🟡 RENDAH',
            ];
        }

        $this->table(
            ['Kode', 'Nama Produk', 'Stok', 'Alert', 'Status'],
            $tableData
        );

        $this->newLine();
        $this->info("📊 Summary:");
        $this->line("   - Total Low Stock: {$lowStockProducts->count()}");
        $this->line("   - Critical (Out of Stock): {$criticalCount}");

        // Log the check
        Log::info('Low Stock Check', [
            'total_low_stock' => $lowStockProducts->count(),
            'critical_count' => $criticalCount,
            'products' => $lowStockProducts->pluck('product_name', 'product_code')->toArray()
        ]);

        // If --notify flag is set, send WhatsApp notification via Baileys
        if ($this->option('notify') && $lowStockProducts->count() > 0) {
            $this->newLine();
            $this->info('📱 Sending WhatsApp notification...');
            
            // Check if WhatsApp service is enabled and connected
            if (!$whatsapp->isEnabled()) {
                $this->warn('⚠️  WhatsApp driver is not set to "baileys". Skipping notification.');
                $this->line('   Set WHATSAPP_DRIVER=baileys in .env to enable.');
                return Command::SUCCESS;
            }

            if (!$whatsapp->isConnected()) {
                $this->warn('⚠️  WhatsApp is not connected. Please scan QR code first.');
                $this->line('   Visit: ' . url('/whatsapp/settings'));
                return Command::SUCCESS;
            }

            // Prepare products array for notification
            $productsArray = $lowStockProducts->map(function ($p) {
                return [
                    'name' => $p->product_name,
                    'quantity' => $p->product_quantity,
                    'alert' => $p->product_stock_alert,
                ];
            })->toArray();

            // Send notification
            $result = $whatsapp->sendLowStockAlert($productsArray);

            if ($result['success'] ?? false) {
                $this->info('✅ WhatsApp notification sent successfully!');
                Log::info('Low stock WhatsApp notification sent', [
                    'messageId' => $result['messageId'] ?? null,
                    'products_count' => count($productsArray)
                ]);
            } else {
                $this->error('❌ Failed to send WhatsApp notification: ' . ($result['error'] ?? 'Unknown error'));
                Log::error('Failed to send low stock notification', [
                    'error' => $result['error'] ?? 'Unknown error'
                ]);
            }
        }

        return Command::SUCCESS;
    }
}
