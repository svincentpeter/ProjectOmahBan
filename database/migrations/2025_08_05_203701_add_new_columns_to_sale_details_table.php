<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
{
    // BARIS KODE YANG DIGANTI MULAI DARI SINI
    Schema::table('sale_details', function (Blueprint $table) {
        // Kolom untuk "mengunci" nama, hpp, dan profit saat transaksi
        $table->string('item_name')->after('sale_id');
        $table->decimal('hpp', 15, 2)->default(0)->after('price');
        $table->decimal('subtotal_profit', 15, 2)->default(0)->after('sub_total');

        // Penanda jenis barang (Baru, Bekas, atau Jasa)
        $table->enum('source_type', ['new', 'second', 'manual'])->default('new')->after('product_id');

        // Kolom "ajaib" untuk menghubungkan ke tabel produk baru ATAU produk bekas
        $table->unsignedBigInteger('productable_id')->nullable()->after('product_id');
        $table->string('productable_type')->nullable()->after('productable_id');
    });
    // SAMPAI SINI
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('sale_details', function (Blueprint $table) {
            //
        });
    }
};
