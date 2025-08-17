<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        // 1) Normalisasi data sale_payments.amount yang terlanjur 100x
        // Heuristik: kalau amount >= 50x total_amount invoice yg sama, anggap salah skala => bagi 100
        DB::statement("
            UPDATE sale_payments sp
            JOIN sales s ON s.id = sp.sale_id
            SET sp.amount = sp.amount / 100
            WHERE sp.amount >= s.total_amount * 50
        ");

        // 2) Ubah semua kolom uang ke BIGINT (satuan: Rupiah, tanpa desimal)
        Schema::table('sales', function (Blueprint $table) {
            $table->bigInteger('tax_amount')->nullable()->change();
            $table->bigInteger('discount_amount')->nullable()->change();
            $table->bigInteger('shipping_amount')->nullable()->change();
            $table->bigInteger('total_amount')->nullable()->change();
            $table->bigInteger('paid_amount')->nullable()->change();
            $table->bigInteger('due_amount')->nullable()->change();
            $table->bigInteger('total_hpp')->default(0)->change();
            $table->bigInteger('total_profit')->default(0)->change();
        });

        Schema::table('sale_details', function (Blueprint $table) {
            $table->bigInteger('hpp')->default(0)->change();
            $table->bigInteger('subtotal_profit')->default(0)->change();
        });

        Schema::table('sale_payments', function (Blueprint $table) {
            $table->bigInteger('amount')->change();
        });

        Schema::table('product_seconds', function (Blueprint $table) {
            $table->bigInteger('purchase_price')->change();
            $table->bigInteger('selling_price')->change();
        });
    }

    public function down(): void
    {
        // Kembalikan ke tipe sebelumnya (kalau perlu rollback)
        Schema::table('sales', function (Blueprint $table) {
            $table->decimal('tax_amount', 18, 2)->nullable()->change();
            $table->decimal('discount_amount', 18, 2)->nullable()->change();
            $table->decimal('shipping_amount', 18, 2)->nullable()->change();
            $table->decimal('total_amount', 18, 2)->nullable()->change();
            $table->decimal('paid_amount', 18, 2)->nullable()->change();
            $table->decimal('due_amount', 18, 2)->nullable()->change();
            $table->decimal('total_hpp', 15, 2)->default(0)->change();
            $table->decimal('total_profit', 15, 2)->default(0)->change();
        });

        Schema::table('sale_details', function (Blueprint $table) {
            $table->decimal('hpp', 15, 2)->default(0)->change();
            $table->decimal('subtotal_profit', 15, 2)->default(0)->change();
        });

        Schema::table('sale_payments', function (Blueprint $table) {
            $table->decimal('amount', 18, 2)->change();
        });

        Schema::table('product_seconds', function (Blueprint $table) {
            $table->decimal('purchase_price', 15, 2)->change();
            $table->decimal('selling_price', 15, 2)->change();
        });
    }
};
