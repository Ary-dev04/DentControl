<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('paciente', function (Blueprint $table) {
            // Agregamos los campos después de la columna 'estado'
            $table->string('nombre_tutor', 100)->nullable()->after('estado');
            $table->string('parentesco_tutor', 50)->nullable()->after('nombre_tutor');
            $table->string('telefono_tutor', 10)->nullable()->after('parentesco_tutor');
        });
    }

    public function down(): void
    {
        Schema::table('paciente', function (Blueprint $table) {
            // Esto permite deshacer el cambio si algo sale mal
            $table->dropColumn(['nombre_tutor', 'parentesco_tutor', 'telefono_tutor']);
        });
    }
};