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
    Schema::create('stock_movements', function (Blueprint $table) {
        $table->id();
        $table->morphs('productable'); // Cara singkat membuat kolom productable_id dan productable_type
        $table->enum('type', ['in', 'out']); // Jenis pergerakan: masuk atau keluar
        $table->integer('quantity'); // Jumlah yang bergerak (untuk barang bekas, nilainya selalu 1)
        $table->string('description'); // Keterangan, contoh: "Penjualan INV-001" atau "Stok Masuk dari Supplier"
        $table->foreignId('user_id')->nullable()->constrained()->onDelete('set null'); // Siapa yang melakukan aksi
        $table->timestamps();
    });
    // SAMPAI SINI
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('stock_movements');
    }
};
