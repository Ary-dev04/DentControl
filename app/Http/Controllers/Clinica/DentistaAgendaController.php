<?php

namespace App\Http\Controllers\Clinica;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Cita; // Importamos el modelo
use Carbon\Carbon;   // Para manejar fechas

class DentistaAgendaController extends Controller
{
    public function index()
    {
        // Traemos las citas de hoy que no estén canceladas
        // Cargamos relaciones: paciente, servicio y tratamiento con su nombre de catálogo
        $citas = Cita::with(['paciente', 'servicio', 'tratamiento.catalogoTratamiento'])
            ->whereDate('fecha', Carbon::today())
            ->where('estatus_cita', '!=', 'cancelada')
            ->orderBy('hora', 'asc')
            ->get();

        return view('dentista.agenda.index', compact('citas'));
    }

}