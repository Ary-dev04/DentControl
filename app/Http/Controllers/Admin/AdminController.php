<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Clinica;
use App\Models\Usuario;
use App\Models\Paciente;

class AdminController extends Controller
{
    public function index()
    {
        // Obtenemos conteos reales de la base de datos
        //$totalClinicas = Clinica::count();
        $totalClinicasActivas = Clinica::where('estatus', 'activo')->count();
        //$totalUsuarios = Usuario::count();
        $totalUsuariosActivos = Usuario::where('estatus', 'activo')->count();
        $totalPacientes = Paciente::count(); // Para la card de "Accesos/Pacientes"
        
        // Aquí podrías traer las últimas clínicas registradas para una tabla
        $ultimasClinicas = Clinica::orderBy('created_at', 'desc')->take(5)->get();

        return view('admin.dashboard', compact('totalClinicasActivas', 'totalUsuariosActivos', 'totalPacientes', 'ultimasClinicas'));
    }
}