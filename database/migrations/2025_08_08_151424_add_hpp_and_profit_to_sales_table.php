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
            // Tambahkan kolom untuk total HPP dan total Profit
            $table->decimal('total_hpp', 15, 2)->default(0.00)->after('total_amount');
            $table->decimal('total_profit', 15, 2)->default(0.00)->after('total_hpp');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('sales', function (Blueprint $table) {
            // Hapus kolom jika migrasi dibatalkan
            $table->dropColumn(['total_hpp', 'total_profit']);
        });
    }
};