<?php

namespace App\Services\Inventory;

use Illuminate\Support\Facades\DB;
use Modules\Product\Entities\Product;
use Modules\Product\Entities\ProductSecond;

class StockService
{
    public function assertAndLockNewProduct(int $productId, int $qty): Product
    {
        $p = Product::lockForUpdate()->findOrFail($productId);
        if ($p->product_quantity < $qty) {
            throw new \RuntimeException("Stok {$p->product_name} tidak mencukupi.");
        }
        return $p;
    }

    public function decrementNew(Product $p, int $qty, string $reference, ?int $userId): void
    {
        $p->decrement('product_quantity', $qty);
        DB::table('stock_movements')->insert([
            'productable_type' => Product::class,
            'productable_id'   => $p->id,
            'type'             => 'out',
            'quantity'         => $qty,
            'description'      => "Sale #{$reference}",
            'user_id'          => $userId,
            'created_at'       => now(),
            'updated_at'       => now(),
        ]);
    }

    public function markSecondAsSold(ProductSecond $s, string $reference, ?int $userId): void
    {
        if (strtolower((string) $s->status) !== 'available') {
            throw new \RuntimeException("Produk bekas {$s->name} sudah terjual/tidak tersedia.");
        }
        $s->update(['status' => 'sold']);
        DB::table('stock_movements')->insert([
            'productable_type' => ProductSecond::class,
            'productable_id'   => $s->id,
            'type'             => 'out',
            'quantity'         => 1,
            'description'      => "Sale (second) #{$reference}",
            'user_id'          => $userId,
            'created_at'       => now(),
            'updated_at'       => now(),
        ]);
    }
}
