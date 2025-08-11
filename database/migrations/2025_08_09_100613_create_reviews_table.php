<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('reviews', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained()->onDelete('cascade');
            $table->string('customer_name');
            $table->string('customer_email');
            $table->integer('rating')->comment('Rating from 1 to 5 stars');
            $table->text('comment')->nullable();
            $table->boolean('is_approved')->default(false);
            $table->timestamp('reviewed_at')->nullable();
            $table->timestamps();
            
            // Indexes for better performance
            $table->index(['product_id', 'is_approved']);
            $table->index('rating');
            $table->index('reviewed_at');
        });
        
        // Add check constraint for rating using raw SQL
        DB::statement('ALTER TABLE reviews ADD CONSTRAINT chk_rating CHECK (rating >= 1 AND rating <= 5)');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('reviews');
    }
};
