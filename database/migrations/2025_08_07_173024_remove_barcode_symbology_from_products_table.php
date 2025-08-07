<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('products', function (Blueprint $table) {
            // Perintah untuk menghapus kolom
            $table->dropColumn('product_barcode_symbology');
        });
    }

    public function down()
    {
        Schema::table('products', function (Blueprint $table) {
            // Perintah untuk mengembalikan kolom jika migrasi dibatalkan
            $table->string('product_barcode_symbology')->nullable()->after('product_code');
        });
    }
};