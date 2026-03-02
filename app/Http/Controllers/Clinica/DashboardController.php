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

    // Datos comunes
    $citasHoy = Cita::where('id_clinica', $id_clinica)->whereDate('fecha', now())->count();

    if ($user->rol === 'dentista') {
        return view('dentista.dashboard', [
            'totalPacientes' => Paciente::where('id_clinica', $id_clinica)->count(),
            'tratamientosActivos' => Tratamiento::where('id_clinica', $id_clinica)->count(),
            'citasHoy' => $citasHoy,
            'alertas' => collect()
        ]);
    }

    if ($user->rol === 'asistente') {
        return view('asistente.dashboard', [
            'citasHoy' => $citasHoy,
            'alertas' => collect()
            
            // Otros datos específicos para el asistente
        ]);
    }

    return redirect()->route('login');
    }
}