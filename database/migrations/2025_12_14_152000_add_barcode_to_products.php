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
        // Add barcode column to products table if not exists
        if (!Schema::hasColumn('products', 'barcode')) {
            Schema::table('products', function (Blueprint $table) {
                $table->string('barcode', 50)->nullable()->after('product_code');
                $table->index('barcode');
            });
        }

        // Add barcode column to products_second table if not exists
        if (Schema::hasTable('products_second') && !Schema::hasColumn('products_second', 'barcode')) {
            Schema::table('products_second', function (Blueprint $table) {
                $table->string('barcode', 50)->nullable()->after('product_code');
                $table->index('barcode');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasColumn('products', 'barcode')) {
            Schema::table('products', function (Blueprint $table) {
                $table->dropIndex(['barcode']);
                $table->dropColumn('barcode');
            });
        }

        if (Schema::hasTable('products_second') && Schema::hasColumn('products_second', 'barcode')) {
            Schema::table('products_second', function (Blueprint $table) {
                $table->dropIndex(['barcode']);
                $table->dropColumn('barcode');
            });
        }
    }
};
