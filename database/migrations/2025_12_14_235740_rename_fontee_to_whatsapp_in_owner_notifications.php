<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Rename fontee_* columns to whatsapp_* for semantic clarity
     */
    public function up(): void
    {
        Schema::table('owner_notifications', function (Blueprint $table) {
            $table->renameColumn('fontee_message_id', 'whatsapp_message_id');
            $table->renameColumn('fontee_status', 'whatsapp_status');
            $table->renameColumn('fontee_sent_at', 'whatsapp_sent_at');
            $table->renameColumn('fontee_error_message', 'whatsapp_error_message');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('owner_notifications', function (Blueprint $table) {
            $table->renameColumn('whatsapp_message_id', 'fontee_message_id');
            $table->renameColumn('whatsapp_status', 'fontee_status');
            $table->renameColumn('whatsapp_sent_at', 'fontee_sent_at');
            $table->renameColumn('whatsapp_error_message', 'fontee_error_message');
        });
    }
};
