<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('sales', function (Blueprint $table) {
            // Tambahkan kolom baru 'bank_name' setelah kolom 'payment_method'.
            // Kolom ini bisa NULL karena hanya akan diisi jika metode pembayarannya adalah 'Transfer'.
            $table->string('bank_name')->nullable()->after('payment_method');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('sales', function (Blueprint $table) {
            // Jika migrasi ini di-rollback, hapus kolom 'bank_name'.
            $table->dropColumn('bank_name');
        });
    }
};
