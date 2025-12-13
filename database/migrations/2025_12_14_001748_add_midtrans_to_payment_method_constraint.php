<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Menambahkan 'Midtrans' ke allowed payment methods dalam check constraint
     */
    public function up(): void
    {
        // Drop constraint lama (jika ada)
        DB::statement("ALTER TABLE sales DROP CONSTRAINT IF EXISTS chk_sales_payment_method");
        
        // Buat constraint baru dengan Midtrans
        DB::statement("ALTER TABLE sales ADD CONSTRAINT chk_sales_payment_method CHECK (payment_method IN ('Tunai', 'Transfer', 'QRIS', 'Midtrans', 'Cash'))");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Drop constraint dengan Midtrans
        DB::statement("ALTER TABLE sales DROP CONSTRAINT IF EXISTS chk_sales_payment_method");
        
        // Restore constraint tanpa Midtrans
        DB::statement("ALTER TABLE sales ADD CONSTRAINT chk_sales_payment_method CHECK (payment_method IN ('Tunai', 'Transfer', 'QRIS', 'Cash'))");
    }
};
