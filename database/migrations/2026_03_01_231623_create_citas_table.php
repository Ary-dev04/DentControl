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
        Schema::create('citas', function (Blueprint $table) {
            $table->id('id_cita');

            $table->unsignedBigInteger('id_paciente');
            $table->unsignedBigInteger('id_usuario');
            $table->unsignedBigInteger('id_cat_servicio');
            $table->unsignedBigInteger('id_tratamiento')->nullable();
            $table->unsignedBigInteger('id_clinica');
            
            $table->date('fecha');
            $table->time('hora');
            $table->string('motivo_consulta')->nullable();

            $table->timestamps();
            //llaves foraneas
            $table->foreign('id_paciente')
                ->references('id_paciente')
                ->on('paciente')
                ->onDelete('cascade');

            $table->foreign('id_usuario')
                ->references('id_usuario')
                ->on('usuario')
                ->onDelete('cascade');

            $table->foreign('id_tratamiento')
                ->references('id_tratamiento')
                ->on('tratamiento')
                ->onDelete('cascade');

            $table->foreign('id_cat_servicio')
                ->references('id_cat_servicio')
                ->on('catalogo_servicios')
                ->onDelete('cascade');
            
            $table->foreign('id_clinica')
                ->references('id_clinica')->on('clinica')
                ->onDelete('cascade');
                    
            $table->unique(['id_usuario', 'fecha', 'hora']);    
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('citas');
    }
};