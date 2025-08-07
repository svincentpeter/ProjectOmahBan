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
            // Menambahkan kolom-kolom baru setelah kolom brand_id
            $table->string('size')->nullable()->after('brand_id');
            $table->string('ring')->nullable()->after('size');
            $table->integer('product_year')->nullable()->after('ring');
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
            $table->dropColumn(['size', 'ring', 'product_year']);
        });
    }
};
