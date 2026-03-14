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
        Schema::create('paciente', function (Blueprint $table) {
            $table->id('id_paciente');

            $table->unsignedBigInteger('id_clinica');
            // Datos personales
            $table->string('nombre');
            $table->string('apellido_paterno');
            $table->string('apellido_materno')->nullable();

            $table->date('fecha_nacimiento')->nullable();
            $table->enum('sexo', ['hombre', 'mujer'])->nullable();
            
            $table->string('email')->unique();
            $table->string('telefono')->nullable();
            $table->string('curp')->nullable()->unique();
            $table->string('ocupacion')->nullable();
            $table->decimal('peso', 5, 2)->nullable();
            // Dirección
            $table->string('calle')->nullable();
            $table->string('num_ext')->nullable();
            $table->string('num_int')->nullable();
            $table->string('colonia')->nullable();
            $table->string('ciudad')->nullable();
            $table->string('estado')->nullable();

            $table->enum('estatus', ['activo', 'baja'])->default('activo');

            $table->timestamps();
            

            //  Relación con clínica
            $table->foreign('id_clinica')
                  ->references('id_clinica')
                  ->on('clinica')
                  ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('paciente');
    }
};
