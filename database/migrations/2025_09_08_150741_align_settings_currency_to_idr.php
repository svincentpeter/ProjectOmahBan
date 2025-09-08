<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    public function up(): void
    {
        // Tambah kolom yang mungkin belum ada
        if (Schema::hasTable('settings')) {
            Schema::table('settings', function (Blueprint $table) {
                if (!Schema::hasColumn('settings', 'default_currency_id')) {
                    $table->unsignedBigInteger('default_currency_id')->nullable()->after('id');
                }
                if (!Schema::hasColumn('settings', 'default_currency_position')) {
                    $table->string('default_currency_position', 10)->nullable()->after('default_currency_id'); // 'prefix'/'suffix'
                }
                if (!Schema::hasColumn('settings', 'thousand_separator')) {
                    $table->string('thousand_separator', 2)->nullable()->after('default_currency_position');
                }
                if (!Schema::hasColumn('settings', 'decimal_separator')) {
                    $table->string('decimal_separator', 2)->nullable()->after('thousand_separator');
                }
            });

            // Set nilai gaya Indonesia: Rp 100.000 (tanpa desimal)
            DB::table('settings')->update([
                'default_currency_position' => 'prefix',
                'thousand_separator'        => '.',
                'decimal_separator'         => ',',
                'updated_at'                => now(),
            ]);
        }

        // Kunci default_currency_id ke IDR jika ada tabel currencies
        if (Schema::hasTable('currencies') && Schema::hasTable('settings')) {
            // Cari IDR berdasarkan code / nama / simbol
            $idr = DB::table('currencies')
                ->where('code', 'IDR')
                ->orWhere('currency_name', 'like', '%Rupiah%')
                ->orWhere('symbol', 'Rp')
                ->first();

            // Jika belum ada, buat IDR
            if (! $idr) {
                $idrId = DB::table('currencies')->insertGetId([
                    'currency_name'      => 'Rupiah',
                    'code'               => 'IDR',
                    'symbol'             => 'Rp',
                    'thousand_separator' => '.',
                    'decimal_separator'  => ',',
                    'exchange_rate'      => null,
                    'created_at'         => now(),
                    'updated_at'         => now(),
                ]);
                $idr = (object) ['id' => $idrId];
            } else {
                // Pastikan properti standar untuk baris IDR
                DB::table('currencies')->where('id', $idr->id)->update([
                    'symbol'             => 'Rp',
                    'thousand_separator' => '.',
                    'decimal_separator'  => ',',
                    'updated_at'         => now(),
                ]);
            }

            // Set default_currency_id ke IDR
            DB::table('settings')->update([
                'default_currency_id' => $idr->id,
                'updated_at'          => now(),
            ]);
        }
    }

    public function down(): void
    {
        // Tidak kita drop kolom; cukup aman dibiarkan (no-op).
        // Jika perlu, Anda bisa menulis rollback sendiri.
    }
};
