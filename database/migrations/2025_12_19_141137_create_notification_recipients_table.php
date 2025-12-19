<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('notification_recipients', function (Blueprint $table) {
            $table->id();
            $table->string('recipient_name'); // Label: "Pak Budi", "Gudang"
            $table->string('recipient_phone');
            $table->json('permissions')->nullable(); // ["manual_input", "low_stock"]
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('notification_recipients');
    }
};
