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
            // Pertama, kita hapus relasi (foreign key) terlebih dahulu.
            $table->dropForeign(['customer_id']);
            // Setelah relasi dihapus, baru kita bisa hapus kolomnya.
            $table->dropColumn('customer_id');
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
            // Ini adalah kebalikan dari proses 'up'.
            // Jika suatu saat Anda butuh mengembalikan kolom ini, migrasi rollback bisa melakukannya.
            $table->foreignId('customer_id')->nullable()->constrained('customers')->onDelete('set null');
        });
    }
};
