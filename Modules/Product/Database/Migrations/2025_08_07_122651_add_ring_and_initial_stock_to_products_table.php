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
        Schema::table('products', function (Blueprint $table) {
            // ===== KODE PERBAIKAN DIMULAI DI SINI =====
            $table->string('ring')->nullable()->after('product_size');
            $table->integer('stok_awal')->default(0)->after('product_quantity');
            // ===== KODE PERBAIKAN SELESAI DI SINI =====
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('products', function (Blueprint $table) {
            // ===== KODE PERBAIKAN DIMULAI DI SINI =====
            $table->dropColumn('ring');
            $table->dropColumn('stok_awal');
            // ===== KODE PERBAIKAN SELESAI DI SINI =====
        });
    }
};