<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('products', function (Blueprint $table) {
            // Kita asumsikan nama kolom lama adalah 'category_id' dari proyek asli.
            // Jika kolom itu ada, kita akan menghapusnya untuk diganti dengan yang baru yang lebih standar.
            // Jika tidak ada, baris ini akan diabaikan dengan aman.
            if (Schema::hasColumn('products', 'category_id')) {
                // Hapus foreign key constraint lama terlebih dahulu jika ada
                // Nama constraint bisa bervariasi, kita coba nama yang umum
                try {
                    $table->dropForeign(['category_id']);
                } catch (\Exception $e) {
                    // Lanjutkan jika constraint tidak ditemukan
                }
                $table->dropColumn('category_id');
            }

            // Tambahkan kolom baru yang sesuai dengan kode kita
            // Pastikan kolom ini belum ada untuk menghindari error
            if (!Schema::hasColumn('products', 'product_category_id')) {
                $table->foreignId('product_category_id')->nullable()->constrained('categories')->onDelete('set null')->after('product_code');
            }
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('products', function (Blueprint $table) {
            // Logika untuk membatalkan perubahan jika diperlukan
            if (Schema::hasColumn('products', 'product_category_id')) {
                try {
                    $table->dropForeign(['product_category_id']);
                } catch (\Exception $e) {
                    // Lanjutkan jika constraint tidak ditemukan
                }
                $table->dropColumn('product_category_id');
            }
        });
    }
};