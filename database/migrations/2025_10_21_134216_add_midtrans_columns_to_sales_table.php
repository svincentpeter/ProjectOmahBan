<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::table('sales', function (Blueprint $table) {
            $table->string('snap_token', 100)->nullable()->after('payment_method');
            $table->string('midtrans_transaction_id', 100)->nullable()->after('snap_token');
            $table
                ->enum('midtrans_payment_type', ['gopay', 'shopeepay', 'qris', 'bank_transfer', 'credit_card', 'other'])
                ->nullable()
                ->after('midtrans_transaction_id');
            $table->timestamp('paid_at')->nullable()->after('payment_status');
        });
    }

    public function down()
    {
        Schema::table('sales', function (Blueprint $table) {
            $table->dropColumn(['snap_token', 'midtrans_transaction_id', 'midtrans_payment_type', 'paid_at']);
        });
    }
};
