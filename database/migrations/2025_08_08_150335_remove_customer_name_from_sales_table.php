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
        Schema::table('sales', function (Blueprint $table) {
            // Perintah untuk MENGHAPUS kolom customer_name
            $table->dropColumn('customer_name');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('sales', function (Blueprint $table) {
            // Perintah untuk MENGEMBALIKAN kolom (jika diperlukan)
            $table->string('customer_name')->after('reference');
        });
    }
};