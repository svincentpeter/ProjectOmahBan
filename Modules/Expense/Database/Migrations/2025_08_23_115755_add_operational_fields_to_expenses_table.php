<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        // Buat tabel kategori jika belum ada
        if (!Schema::hasTable('expense_categories')) {
            Schema::create('expense_categories', function (Blueprint $table) {
                $table->id();
                $table->string('category_name');
                $table->text('category_description')->nullable();
                $table->timestamps();
            });
        }

        // Lengkapi tabel expenses (buat jika belum ada)
        if (!Schema::hasTable('expenses')) {
            Schema::create('expenses', function (Blueprint $table) {
                $table->id();
                $table->foreignId('category_id')->constrained('expense_categories')->cascadeOnDelete();
                $table->date('date');                       // = expense_date
                $table->string('reference');                // EX-YYYYMMDD-0001
                $table->text('details')->nullable();        // = description
                $table->unsignedBigInteger('amount');       // nominal dalam rupiah (tanpa koma)
                $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
                $table->enum('payment_method', ['Tunai', 'Transfer'])->default('Tunai');
                $table->string('bank_name')->nullable();
                $table->string('attachment_path')->nullable();
                $table->timestamps();
                $table->softDeletes();

                $table->index(['date', 'category_id']);
            });
        } else {
            // Kalau sudah ada, tambahkan kolom-kolom yang belum ada
            Schema::table('expenses', function (Blueprint $table) {
                if (!Schema::hasColumn('expenses', 'user_id')) {
                    $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete()->after('amount');
                }
                if (!Schema::hasColumn('expenses', 'payment_method')) {
                    $table->enum('payment_method', ['Tunai', 'Transfer'])->default('Tunai')->after('user_id');
                }
                if (!Schema::hasColumn('expenses', 'bank_name')) {
                    $table->string('bank_name')->nullable()->after('payment_method');
                }
                if (!Schema::hasColumn('expenses', 'attachment_path')) {
                    $table->string('attachment_path')->nullable()->after('bank_name');
                }
                if (!Schema::hasColumn('expenses', 'deleted_at')) {
                    $table->softDeletes()->after('updated_at');
                }

                // Index bantu laporan
                $table->index(['date', 'category_id'], 'expenses_date_category_idx');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('expenses')) {
            Schema::table('expenses', function (Blueprint $table) {
                if (Schema::hasColumn('expenses', 'attachment_path')) $table->dropColumn('attachment_path');
                if (Schema::hasColumn('expenses', 'bank_name')) $table->dropColumn('bank_name');
                if (Schema::hasColumn('expenses', 'payment_method')) $table->dropColumn('payment_method');
                if (Schema::hasColumn('expenses', 'user_id')) $table->dropConstrainedForeignId('user_id');
                if (Schema::hasColumn('expenses', 'deleted_at')) $table->dropSoftDeletes();
                $table->dropIndex('expenses_date_category_idx');
            });
        }
        // Tidak menghapus expense_categories pada down karena bisa dipakai tabel lain.
    }
};
