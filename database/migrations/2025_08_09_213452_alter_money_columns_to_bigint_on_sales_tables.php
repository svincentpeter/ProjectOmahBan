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
    Schema::table('sales', function (Blueprint $table) {
        $table->bigInteger('tax_amount')->change();
        $table->bigInteger('discount_amount')->change();
        $table->bigInteger('shipping_amount')->change();
        $table->bigInteger('total_amount')->change();
        $table->bigInteger('paid_amount')->change();
        $table->bigInteger('due_amount')->change();
    });

    Schema::table('sale_details', function (Blueprint $table) {
        $table->bigInteger('price')->change();
        $table->bigInteger('unit_price')->change();
        $table->bigInteger('sub_total')->change();
        $table->bigInteger('product_discount_amount')->change();
        $table->bigInteger('product_tax_amount')->change();
    });

    Schema::table('sale_payments', function (Blueprint $table) {
        $table->bigInteger('amount')->change();
    });
}

public function down(): void
{
    Schema::table('sales', function (Blueprint $table) {
        $table->integer('tax_amount')->change();
        $table->integer('discount_amount')->change();
        $table->integer('shipping_amount')->change();
        $table->integer('total_amount')->change();
        $table->integer('paid_amount')->change();
        $table->integer('due_amount')->change();
    });

    Schema::table('sale_details', function (Blueprint $table) {
        $table->integer('price')->change();
        $table->integer('unit_price')->change();
        $table->integer('sub_total')->change();
        $table->integer('product_discount_amount')->change();
        $table->integer('product_tax_amount')->change();
    });

    Schema::table('sale_payments', function (Blueprint $table) {
        $table->integer('amount')->change();
    });
}

};
