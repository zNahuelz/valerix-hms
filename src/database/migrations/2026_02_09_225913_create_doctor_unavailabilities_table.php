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
        Schema::create('doctor_unavailabilities', function (Blueprint $table) {
            $table->id();
            $table->foreignId('doctor_id')->constrained();
            $table->dateTime('start_datetime');
            $table->dateTime('end_datetime');
            $table->enum('reason', ['VACACIONES', 'ENFERMEDAD', 'DIA_LIBRE', 'MATERNIDAD', 'NO_ESPECIFICADO', 'DESPEDIDO'])->default('NO_ESPECIFICADO');
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
        Schema::dropIfExists('doctor_unavailabilities');
    }
};
