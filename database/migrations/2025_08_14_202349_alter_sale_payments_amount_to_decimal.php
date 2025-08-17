<?php

// database/migrations/2025_08_14_000001_alter_sale_payments_amount_to_decimal.php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::table('sale_payments', function (Blueprint $table) {
            $table->decimal('amount', 18, 2)->change();
        });
    }
    public function down(): void {
        Schema::table('sale_payments', function (Blueprint $table) {
            $table->bigInteger('amount')->change();
        });
    }
};

