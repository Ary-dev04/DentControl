<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up()
    {
        // 1. Usamos SQL puro para permitir que email sea NULL y quitar lo obligatorio
        DB::statement('ALTER TABLE paciente MODIFY email VARCHAR(255) NULL');

        Schema::table('paciente', function (Blueprint $table) {
            // 2. Agregamos email_tutor después de telefono_tutor
            $table->string('email_tutor', 255)->nullable()->after('telefono_tutor');

            // 3. Agregamos grado_estudio después de ocupacion
            $table->string('grado_estudio', 100)->nullable()->after('ocupacion');
        });
    }

    public function down()
    {
        Schema::table('paciente', function (Blueprint $table) {
            $table->dropColumn(['email_tutor', 'grado_estudio']);
            // Regresamos el email a NO NULL (Ojo: esto fallará si ya tienes datos nulos)
            DB::statement('ALTER TABLE paciente MODIFY email VARCHAR(255) NOT NULL');
        });
    }
};