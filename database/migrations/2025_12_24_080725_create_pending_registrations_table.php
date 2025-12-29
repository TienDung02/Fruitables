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
        Schema::create('pending_registrations', function (Blueprint $table) {
            $table->id();

            $table->string('email', 191);
            $table->string('type', 50); // 🔥 register | reset_password

            $table->string('username')->nullable();
            $table->string('password')->nullable();
            $table->unsignedBigInteger('user_id')->nullable();

            $table->string('token', 191)->unique();
            $table->boolean('used')->default(false);
            $table->string('step')->default('email');
            $table->timestamp('expires_at');

            $table->timestamps();

            $table->unique(['email', 'type']); // 🔥 QUAN TRỌNG
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pending_registrations');
    }
};
