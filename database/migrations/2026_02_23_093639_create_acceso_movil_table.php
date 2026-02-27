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
        Schema::create('acceso_movil', function (Blueprint $table) {
            $table->id('id_acceso');

            $table->unsignedBigInteger('id_paciente')->unique();

            $table->string('usuario_movil')->unique();
            $table->string('password');

            $table->string('token')->nullable()->unique();
            $table->dateTime('fecha_expiracion')->nullable();

            $table->enum('estatus', ['activo', 'expirado', 'temporal'])
                  ->default('temporal');

            $table->timestamps();

            //relacion con paciente
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
        Schema::dropIfExists('acceso_movil');
    }
};
