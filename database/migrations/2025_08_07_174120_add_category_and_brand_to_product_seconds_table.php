<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('product_seconds', function (Blueprint $table) {
            // Menambahkan kolom untuk relasi ke tabel categories dan brands
            $table->foreignId('category_id')->nullable()->constrained('categories')->onDelete('set null')->after('unique_code');
            $table->foreignId('brand_id')->nullable()->constrained('brands')->onDelete('set null')->after('category_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('product_seconds', function (Blueprint $table) {
            // Perintah untuk membatalkan migrasi
            $table->dropForeign(['category_id']);
            $table->dropForeign(['brand_id']);
            $table->dropColumn(['category_id', 'brand_id']);
        });
    }
};
