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
        Schema::create('clinica', function (Blueprint $table) {
            $table->id('id_clinica');

            $table->string('nombre');
            $table->string('rfc')->nullable()->unique();
            //DIRECCION
            $table->string('calle')->nullable();
            $table->string('numero_ext')->nullable();
            $table->string('numero_int')->nullable();
            $table->string('colonia')->nullable();
            $table->string('ciudad')->nullable();
            $table->string('estado')->nullable();
            $table->string('codigo_postal')->nullable();

            $table->string('telefono')->nullable();
            $table->string('logo_ruta')->nullable();

            $table->enum('estatus', ['activo', 'baja'])->default('activo');

            $table->timestamps(); // created_at y updated_at
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('clinica');
    }
};
