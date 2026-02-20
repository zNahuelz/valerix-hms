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
        Schema::create('sales', function (Blueprint $table) {
            $table->id();
            $table->double('subtotal');
            $table->double('tax');
            $table->double('total');
            $table->double('change')->default(0);
            $table->double('money_received');
            $table->string('set', 15);
            $table->string('correlative', 15);
            $table->foreignId('clinic_id')->constrained();
            $table->foreignId('voucher_type_id')->constrained();
            $table->foreignId('patient_id')->constrained();
            $table->foreignId('payment_type_id')->constrained();
            $table->string('payment_hash')->nullable();
            $table->string('payment_image')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete();
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sales');
    }
};
