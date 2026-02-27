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
        Schema::create('tratamiento', function (Blueprint $table) {
            $table->id('id_tratamiento');

            // Relaciones principales
            $table->unsignedBigInteger('id_paciente');
            $table->unsignedBigInteger('id_usuario');
            $table->unsignedBigInteger('id_clinica');
            $table->unsignedBigInteger('id_cat_tratamientos');

            // Información clínica
            $table->text('diagnostico_inicial')->nullable();

            $table->decimal('precio_final', 10, 2)->nullable();

            $table->date('fecha_inicio')->nullable();
            $table->date('fecha_fin')->nullable();

            $table->enum('estatus', ['curso', 'finalizado', 'pausado'])
                  ->default('curso');

            $table->timestamps();

            // Llaves foráneas
            $table->foreign('id_paciente')
                  ->references('id_paciente')
                  ->on('paciente')
                  ->onDelete('cascade');

            $table->foreign('id_usuario')
                  ->references('id_usuario')
                  ->on('usuario')
                  ->onDelete('cascade');

            $table->foreign('id_clinica')
                  ->references('id_clinica')
                  ->on('clinica')
                  ->onDelete('cascade');

            $table->foreign('id_cat_tratamientos')
                  ->references('id_cat_tratamientos')
                  ->on('catalogo_tratamientos')
                  ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tratamiento');
    }
};
