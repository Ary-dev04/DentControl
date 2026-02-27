<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Usuario;
use Illuminate\Support\Facades\Hash;

class UsuarioSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        //
        Usuario::create([
            'id_clinica'        => 1, 
            'nombre'            => 'Admin',
            'apellido_paterno'  => 'Sistema',
            'nom_usuario'       => 'admin',
            'password'          => 'DentControl2026', 
            'rol'               => 'superadmin',
            'estatus'           => 'activo'
        ]);
    }
}
