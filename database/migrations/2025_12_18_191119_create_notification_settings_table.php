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
        Schema::create('notification_settings', function (Blueprint $table) {
            $table->id();
            $table->string('type')->unique()->comment('Unique identifier: manual_input, low_stock, daily_report, etc');
            $table->string('label')->comment('Human readable name');
            $table->text('description')->nullable()->comment('Description of this notification type');
            $table->string('icon')->default('bi-bell')->comment('Bootstrap icon class');
            $table->boolean('is_enabled')->default(true)->comment('Whether this notification type is active');
            $table->text('template')->comment('Message template with {placeholders}');
            $table->json('placeholders')->nullable()->comment('Available placeholders for this template');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('notification_settings');
    }
};
