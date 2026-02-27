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
        Schema::create('expediente_clinico', function (Blueprint $table) {
            $table->id('id_expediente');

            // Relación con paciente
            $table->unsignedBigInteger('id_paciente')->unique();
            // Antecedentes médicos
            $table->text('antecedentes_hereditarios')->nullable();
            $table->text('antecedentes_patologicos')->nullable();
            $table->text('alergias')->nullable();
            $table->text('observaciones_generales')->nullable();

            $table->date('fecha_registro')->nullable();

            $table->timestamps();

            // Relacion con paciente
            $table->foreign('id_paciente')
                  ->references('id_paciente')
                  ->on('paciente')
                  ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('expediente_clinico');
    }
};
