<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('stock_opname_logs', function (Blueprint $table) {
            // Add updated_at if not exists
            if (!Schema::hasColumn('stock_opname_logs', 'updated_at')) {
                $table->timestamp('updated_at')->nullable()->after('created_at');
            }
            // Add deleted_at for SoftDeletes
            if (!Schema::hasColumn('stock_opname_logs', 'deleted_at')) {
                $table->softDeletes();
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('stock_opname_logs', function (Blueprint $table) {
            $table->dropSoftDeletes();
            $table->dropColumn('updated_at');
        });
    }
};
