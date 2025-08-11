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
        Schema::table('reviews', function (Blueprint $table) {
            // Drop the old columns
            $table->dropColumn(['customer_name', 'customer_email']);
            
            // Add user_id foreign key
            $table->foreignId('user_id')->nullable()->constrained()->onDelete('cascade')->after('product_id');
            
            // Add index for user_id
            $table->index('user_id');
            
            // Add unique constraint to prevent duplicate reviews from same user for same product
            $table->unique(['product_id', 'user_id'], 'unique_user_product_review');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('reviews', function (Blueprint $table) {
            // Drop the unique constraint and foreign key
            $table->dropUnique('unique_user_product_review');
            $table->dropForeign(['user_id']);
            $table->dropColumn('user_id');
            
            // Add back the old columns
            $table->string('customer_name')->after('product_id');
            $table->string('customer_email')->after('customer_name');
        });
    }
};
