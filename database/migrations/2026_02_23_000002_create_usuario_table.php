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
        Schema::create('usuario', function (Blueprint $table) {
            $table->id('id_usuario');

            $table->unsignedBigInteger('id_clinica');

            // Datos personales
            $table->string('nombre');
            $table->string('apellido_paterno');
            $table->string('apellido_materno');
            $table->string('email')->unique();
            $table->string('telefono', 10)->unique();

            // Datos profesionales
            $table->string('cedula_profesional')->nullable()->unique();
            //LOGIN
            $table->string('nom_usuario')->unique();
            $table->string('password');
            $table->rememberToken();

            $table->enum('rol', ['superadmin', 'dentista', 'asistente']);
            $table->enum('estatus', ['activo', 'baja'])->default('activo');
            $table->timestamps();
            //Relacion con clinica
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
        Schema::dropIfExists('usuario');
    }
};
