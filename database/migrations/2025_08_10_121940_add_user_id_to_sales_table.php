    <?php

    use Illuminate\Support\Facades\Schema;
    use Illuminate\Database\Schema\Blueprint;
    use Illuminate\Database\Migrations\Migration;

    return new class extends Migration
    {
        /**
         * Jalankan migrasi.
         *
         * @return void
         */
        public function up()
        {
            // Kita periksa dulu apakah kolomnya sudah ada atau belum, agar aman jika dijalankan berulang kali.
            if (!Schema::hasColumn('sales', 'user_id')) {
                Schema::table('sales', function (Blueprint $table) {
                    // Tambahkan kolom 'user_id' setelah kolom 'reference'.
                    // 'nullable()' dan 'onDelete('set null')' penting agar jika user dihapus, data penjualan tidak ikut terhapus.
                    $table->foreignId('user_id')->nullable()->after('reference')->constrained('users')->onDelete('set null');
                });
            }
        }

        /**
         * Batalkan migrasi.
         *
         * @return void
         */
        public function down()
        {
            // Ini adalah kebalikan dari proses di atas, untuk jaga-jaga jika perlu rollback.
            if (Schema::hasColumn('sales', 'user_id')) {
                Schema::table('sales', function (Blueprint $table) {
                    $table->dropForeign(['user_id']);
                    $table->dropColumn('user_id');
                });
            }
        }
    };
    