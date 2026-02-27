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
        Schema::create('notas_evolucion', function (Blueprint $table) {
            $table->id('id_nota');

            $table->unsignedBigInteger('id_tratamiento');
            $table->unsignedBigInteger('id_usuario');

            $table->date('fecha')->nullable();
            $table->time('hora')->nullable();

            $table->text('nota_texto')->nullable();
            $table->text('indicaciones')->nullable();

            $table->timestamps();

            //llaves foraneas
            $table->foreign('id_tratamiento')
                  ->references('id_tratamiento')
                  ->on('tratamiento')
                  ->onDelete('cascade');

            $table->foreign('id_usuario')
                  ->references('id_usuario')
                  ->on('usuario')
                  ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('notas_evolucion');
    }
};
