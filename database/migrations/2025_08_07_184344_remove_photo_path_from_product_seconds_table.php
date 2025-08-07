<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('product_seconds', function (Blueprint $table) {
            $table->dropColumn('photo_path');
        });
    }

    public function down()
    {
        Schema::table('product_seconds', function (Blueprint $table) {
            $table->string('photo_path')->nullable();
        });
    }
};
