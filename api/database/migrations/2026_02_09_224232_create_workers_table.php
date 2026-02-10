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
        Schema::create('workers', function (Blueprint $table) {
            $table->id();
            $table->string('names', 30);
            $table->string('paternal_surname', 30);
            $table->string('maternal_surname', 30)->nullable();
            $table->string('dni', 15)->unique();
            $table->string('phone', 15);
            $table->string('address', 100);
            $table->date('hired_at');
            $table->foreignId('clinic_id')->constrained();
            $table->foreignId('user_id')->constrained();
            $table->string('position', 50);
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
        Schema::dropIfExists('workers');
    }
};
