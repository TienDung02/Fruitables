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
        Schema::create('currencies', function (Blueprint $table) {
            $table->id();

            $table->string('code', 3)->unique();        // VND, USD, EUR
            $table->string('name');                     // Vietnamese Dong
            $table->string('symbol', 10);               // ₫, $, €
            $table->string('locale', 5)->nullable();    // vi, en, ru, ja

            // Tỷ giá so với base currency (VND)
            $table->decimal('exchange_rate', 15, 6)
                ->comment('Rate compared to base currency');

            $table->unsignedTinyInteger('decimal_places')
                ->default(0)
                ->comment('Number of decimal digits');

            $table->enum('position', ['left', 'right'])
                ->default('right')
                ->comment('Symbol position');

            $table->boolean('is_default')
                ->default(false)
                ->comment('Default currency');

            $table->boolean('is_active')
                ->default(true)
                ->comment('Is currency active');

            $table->timestamps();
            $table->softDeletes();
        });
    }


    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('currencies');
    }
};
