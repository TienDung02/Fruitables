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
        Schema::create('categories', function (Blueprint $table) {
            $table->id();

            // ✅ ADD: Parent category for hierarchy
            $table->unsignedBigInteger('parent_id')->nullable();

            $table->string('name');
            $table->string('slug', 191)->unique();
            $table->text('description')->nullable();
            $table->string('image')->nullable();
            $table->boolean('is_active')->default(true);

            // ✅ ADD: Sort order for custom ordering
            $table->integer('sort_order')->default(0);

            // ✅ ADD: SEO fields
            $table->string('meta_title')->nullable();
            $table->text('meta_description')->nullable();

            $table->timestamps();

            // ✅ ADD: Foreign key constraint
            $table->foreign('parent_id')->references('id')->on('categories')->onDelete('cascade');

            // ✅ ADD: Indexes for better performance
            $table->index(['parent_id', 'is_active', 'sort_order']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('categories');
    }
};
