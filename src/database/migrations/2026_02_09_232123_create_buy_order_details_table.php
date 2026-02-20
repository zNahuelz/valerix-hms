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
        Schema::create('buy_order_details', function (Blueprint $table) {
            $table->id();
            $table->foreignId('buy_order_id')->constrained();
            $table->foreignId('medicine_id')->constrained();
            $table->integer('amount');
            $table->double('unit_price');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('buy_order_details');
    }
};
