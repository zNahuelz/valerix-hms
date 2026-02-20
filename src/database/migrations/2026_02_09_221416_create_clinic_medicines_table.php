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
        Schema::create('clinic_medicines', function (Blueprint $table) {
            $table->id();
            $table->foreignId('clinic_id')->constrained();
            $table->foreignId('medicine_id')->constrained();
            $table->double('buy_price');
            $table->double('sell_price');
            $table->double('tax');
            $table->double('profit');
            $table->integer('stock');
            $table->boolean('salable')->default(true);
            $table->foreignId('last_sold_by')->nullable()->constrained('suppliers')->nullOnDelete();
            $table->unique(['clinic_id', 'medicine_id']);
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('clinic_medicines');
    }
};
