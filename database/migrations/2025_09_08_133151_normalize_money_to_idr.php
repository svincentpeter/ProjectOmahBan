<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Penting: backup DB sebelum jalan migration ini.

        DB::transaction(function () {
            // Mapping tabel & kolom yang selama ini disimpan dalam "sen".
            // Sesuaikan/kurangi/tambah daftar ini jika strukturmu berbeda.
            $targets = [
                ['table' => 'expenses',                 'columns' => ['amount']],
                ['table' => 'sales_returns',            'columns' => ['total_amount']],
                ['table' => 'sale_return_details',      'columns' => ['hpp', 'price', 'sub_total']],
                ['table' => 'purchases_returns',        'columns' => ['total_amount']],
                ['table' => 'purchase_return_details',  'columns' => ['hpp', 'price', 'sub_total']],
                // Tambahkan di sini bila ada kolom uang lain yang kamu simpan dalam sen
                // ['table' => 'some_table', 'columns' => ['some_money_col']],
            ];

            foreach ($targets as $t) {
                $table = $t['table'];
                if (!Schema::hasTable($table)) {
                    continue;
                }

                foreach ($t['columns'] as $col) {
                    if (!Schema::hasColumn($table, $col)) {
                        continue;
                    }

                    // 1) Konversi nilai: sen -> IDR (÷100)
                    // Jalankan sekali (migration sifatnya one-time).
                    DB::statement("UPDATE `{$table}` SET `{$col}` = ROUND(`{$col}` / 100) WHERE `{$col}` IS NOT NULL");

                    // 2) (Opsional) Ubah tipe ke unsigned BIGINT jika DBAL tersedia
                    // agar konsisten integer & kapasitas aman untuk nilai besar.
                    if ($this->canChangeColumnType()) {
                        try {
                            Schema::table($table, function (Blueprint $blueprint) use ($col) {
                                $blueprint->unsignedBigInteger($col)->nullable()->change();
                            });
                        } catch (\Throwable $e) {
                            // Abaikan jika gagal mengubah tipe (misal doctrine/dbal belum terpasang).
                            // Nilai tetap sudah IDR integer, jadi secara fungsional aman.
                        }
                    }
                }
            }
        });
    }

    public function down(): void
    {
        // Rollback: kembalikan ke "sen" (×100) untuk kolom yang sama.
        DB::transaction(function () {
            $targets = [
                ['table' => 'expenses',                 'columns' => ['amount']],
                ['table' => 'sales_returns',            'columns' => ['total_amount']],
                ['table' => 'sale_return_details',      'columns' => ['hpp', 'price', 'sub_total']],
                ['table' => 'purchases_returns',        'columns' => ['total_amount']],
                ['table' => 'purchase_return_details',  'columns' => ['hpp', 'price', 'sub_total']],
            ];

            foreach ($targets as $t) {
                $table = $t['table'];
                if (!Schema::hasTable($table)) {
                    continue;
                }

                foreach ($t['columns'] as $col) {
                    if (!Schema::hasColumn($table, $col)) {
                        continue;
                    }

                    DB::statement("UPDATE `{$table}` SET `{$col}` = `{$col}` * 100 WHERE `{$col}` IS NOT NULL");
                }
            }
        });
    }

    private function canChangeColumnType(): bool
    {
        // true jika doctrine/dbal ada → memungkinkan $table->change()
        return interface_exists(\Doctrine\DBAL\Driver::class) || class_exists(\Doctrine\DBAL\Connection::class);
    }
};
