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
        Schema::create('sale_returns', function (Blueprint $table) {
            $table->id();
            $table->string('reference')->unique();
            $table->foreignId('sale_id')->constrained('sales')->onDelete('cascade');
            $table->foreignId('customer_id')->nullable()->constrained('customers')->onDelete('set null');
            $table->date('date');
            $table->enum('status', ['Pending', 'Approved', 'Rejected', 'Completed'])->default('Pending');
            $table->bigInteger('total_amount')->default(0);
            $table->bigInteger('refund_amount')->default(0);
            $table->enum('refund_method', ['Cash', 'Credit', 'Store Credit'])->default('Cash');
            $table->text('reason')->nullable();
            $table->text('note')->nullable();
            $table->foreignId('created_by')->constrained('users')->onDelete('cascade');
            $table->foreignId('approved_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamp('approved_at')->nullable();
            $table->timestamps();
            $table->softDeletes();

            // Indexes
            $table->index(['date', 'status']);
            $table->index('sale_id');
        });

        Schema::create('sale_return_details', function (Blueprint $table) {
            $table->id();
            $table->foreignId('sale_return_id')->constrained('sale_returns')->onDelete('cascade');
            $table->foreignId('sale_detail_id')->nullable()->constrained('sale_details')->onDelete('set null');
            $table->string('product_name');
            $table->string('product_code')->nullable();
            $table->integer('quantity');
            $table->bigInteger('unit_price');
            $table->bigInteger('sub_total');
            $table->string('source_type')->default('new'); // new, second, service, manual
            $table->text('reason')->nullable();
            $table->enum('condition', ['good', 'damaged', 'defective'])->default('good');
            $table->boolean('restock')->default(true);
            $table->string('productable_type')->nullable(); // For polymorphic relation
            $table->unsignedBigInteger('productable_id')->nullable();
            $table->timestamps();

            // Indexes
            $table->index('sale_return_id');
            $table->index(['productable_type', 'productable_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sale_return_details');
        Schema::dropIfExists('sale_returns');
    }
};
