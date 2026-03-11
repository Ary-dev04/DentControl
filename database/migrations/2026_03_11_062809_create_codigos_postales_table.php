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
    Schema::create('codigos_postales', function (Blueprint $table) {
        $table->id();
        // Es vital poner ->index() al código postal porque haremos miles de búsquedas sobre esta columna
        $table->string('codigo_postal', 5)->index(); 
        $table->string('asentamiento'); // Nombre de la colonia
        $table->string('tipo_asentamiento')->nullable(); // Ej. Colonia, Fraccionamiento
        $table->string('municipio');
        $table->string('estado');
        $table->string('ciudad')->nullable();
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('codigos_postales');
    }
};
