<?php
namespace App\Http\Controllers\Clinica;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Paciente;
use App\Models\Tratamiento;
use App\Models\Cita;

class DashboardController extends Controller
{
    public function index()
{
    $user = auth()->user();
    $id_clinica = $user->id_clinica;

    $citasHoy = Cita::where('id_clinica', $id_clinica)->whereDate('fecha', now())->count();
    $totalPacientes = Paciente::where('id_clinica', $id_clinica)->count();
    $tratamientosActivos = Tratamiento::where('id_clinica', $id_clinica)->count();
    $alertas = collect();

    // AQUÍ ESTÁ EL CAMBIO:
    // Si el rol es 'dentista', busca en la carpeta dentista, si no, en asistente
    $vista = ($user->rol === 'dentista') ? 'dentista.dashboard' : 'asistente.dashboard';

    return view($vista, compact(
        'totalPacientes', 
        'tratamientosActivos', 
        'citasHoy', 
        'alertas'
    ));
}
}