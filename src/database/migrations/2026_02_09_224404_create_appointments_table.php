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
        Schema::create('appointments', function (Blueprint $table) {
            $table->id();
            $table->date('date');
            $table->time('time');
            $table->enum('status', ['PENDIENTE', 'ATENDIDO', 'REPROGRAMADO', 'CANCELADO', 'NO_ASISTIO'])->default('PENDIENTE');
            $table->boolean('is_remote')->default(false);
            $table->integer('duration');
            $table->foreignId('appointment_type_id')->constrained();
            $table->date('rescheduling_date')->nullable();
            $table->time('rescheduling_time')->nullable();
            $table->foreignId('clinic_id')->nullable()->constrained();
            $table->foreignId('treatment_id')->nullable()->constrained();
            $table->foreignId('patient_id')->constrained();
            $table->foreignId('doctor_id')->constrained();
            $table->foreignId('nurse_id')->nullable()->constrained();
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
        Schema::dropIfExists('appointments');
    }
};
