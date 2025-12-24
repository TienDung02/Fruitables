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
        Schema::create('order_items', function (Blueprint $table) {
            $table->id();
            $table->string('order_id', 50); // Khóa ngoại đến bảng orders
            $table->unsignedBigInteger('productVariant_id');
            $table->integer('quantity');
            $table->decimal('price', 10, 2); // Giá tại thời điểm đặt hàng
            $table->timestamps();

            // Foreign key constraints
            $table->foreign('order_id')->references('id')->on('orders')->onDelete('cascade');
            $table->foreign('productVariant_id')->references('id')->on('product_variants')->onDelete('cascade');

            // Indexes
            $table->index('order_id');
            $table->index('productVariant_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('order_items');
    }
};
