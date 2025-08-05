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
    Schema::create('product_seconds', function (Blueprint $table) {
        $table->id();
        $table->string('name'); // Nama barang, contoh: "Velg Enkei RPF1 Ring 17 Bekas"
        $table->string('unique_code')->unique(); // Kode unik untuk membedakan satu barang bekas dengan lainnya
        $table->text('condition_notes')->nullable(); // Catatan kondisi, misal: "Baret halus, no peyang"
        $table->string('photo_path')->nullable(); // Tempat menyimpan path/lokasi foto barang
        $table->decimal('purchase_price', 15, 2); // Harga Beli / Modal (HPP) untuk barang ini
        $table->decimal('selling_price', 15, 2); // Harga Jual yang kita tetapkan
        $table->enum('status', ['available', 'sold'])->default('available'); // Status ketersediaan
        $table->timestamps(); // Otomatis membuat kolom created_at dan updated_at
        $table->softDeletes(); // Kolom untuk fitur hapus sementara (agar data tidak hilang permanen)
    });
    // SAMPAI SINI
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('products_second');
    }
};
