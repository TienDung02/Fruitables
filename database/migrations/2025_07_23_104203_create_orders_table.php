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
        Schema::create('orders', function (Blueprint $table) {
            $table->string('id', 50)->primary();
            $table->unsignedBigInteger('user_id')->nullable(); // Cho phép null cho guest checkout
            $table->enum('status', ['pending', 'confirmed', 'processing', 'shipped', 'delivered', 'cancelled'])->default('pending');
            $table->decimal('subtotal', 10, 2);
            $table->decimal('shipping_cost', 8, 2)->default(0);
            $table->decimal('total', 10, 2);
            $table->enum('payment_method', ['cod', 'momo', 'card'])->default('cod');
            $table->enum('payment_status', ['pending', 'paid', 'failed', 'refunded'])->default('pending');
            $table->string('shipping_method', 100)->nullable(); // Giới hạn độ dài
            $table->json('customer_info'); // Lưu thông tin khách hàng dưới dạng JSON
            $table->text('notes')->nullable();
            $table->string('payment_request_id', 100)->nullable(); // Giới hạn độ dài
            $table->json('payment_data')->nullable(); // Dữ liệu trả về từ payment gateway
            $table->string('transaction_id', 100)->nullable(); // Giới hạn độ dài
            $table->timestamp('paid_at')->nullable();
            $table->timestamps();

            // Foreign key constraint
            $table->foreign('user_id')->references('id')->on('users')->onDelete('set null');

            // Indexes
            $table->index(['user_id', 'status']);
            $table->index('payment_status');
            $table->index('created_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
