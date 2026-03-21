<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
{
    Schema::create('historial_expedientes', function (Blueprint $table) {
        $table->id('id_historial_exp');
        $table->unsignedBigInteger('id_paciente');
        $table->decimal('peso', 10, 2)->nullable();
        $table->text('alergias')->nullable();
        $table->text('antecedentes_hereditarios')->nullable();
        $table->text('antecedentes_patologicos')->nullable();
        $table->text('observaciones_generales')->nullable(); // Notas de esa fecha
        $table->unsignedBigInteger('id_usuario'); // Quién hizo el cambio
        $table->timestamp('fecha_modificacion')->useCurrent();
        
        // Relación con pacientes
        $table->foreign('id_paciente')->references('id_paciente')->on('paciente')->onDelete('cascade');
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('historial_expedientes');
    }
};
