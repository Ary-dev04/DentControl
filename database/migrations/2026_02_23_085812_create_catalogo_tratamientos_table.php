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
        Schema::create('catalogo_tratamientos', function (Blueprint $table) {
            $table->id('id_cat_tratamientos');

            $table->unsignedBigInteger('id_clinica');

            $table->string('nombre');
            $table->text('descripcion')->nullable();
            $table->integer('duracion_sugerido_sesion')->nullable();
            $table->decimal('precio_sugerido', 10, 2)->nullable();
            $table->enum('estatus', ['activo', 'baja'])->default('activo');

            $table->timestamps();

            // Relación con clínica
            $table->foreign('id_clinica')
                  ->references('id_clinica')
                  ->on('clinica')
                  ->onDelete('cascade');
            
            $table->unique(['id_clinica', 'nombre']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('catalogo_tratamientos');
    }
};
