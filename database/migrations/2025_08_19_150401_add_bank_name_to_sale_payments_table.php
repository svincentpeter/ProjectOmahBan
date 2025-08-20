<?php

// database/migrations/2025_08_18_000000_add_bank_name_to_sale_payments_table.php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::table('sale_payments', function (Blueprint $table) {
            if (!Schema::hasColumn('sale_payments','bank_name')) {
                $table->string('bank_name')->nullable()->after('payment_method');
            }
        });
    }
    public function down(): void {
        Schema::table('sale_payments', function (Blueprint $table) {
            if (Schema::hasColumn('sale_payments','bank_name')) {
                $table->dropColumn('bank_name');
            }
        });
    }
};
