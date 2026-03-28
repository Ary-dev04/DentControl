<?php

namespace App\Http\Controllers\Clinica;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Cita;
use App\Models\Tratamiento;
use Carbon\Carbon;

class AgendaController extends Controller
{
    public function index()
    {
        // Traemos las citas de hoy que NO estén canceladas
        $citas = Cita::with(['paciente', 'servicio', 'tratamiento.catalogoTratamiento'])
            ->whereDate('fecha', Carbon::today())
            ->where('estatus_cita', '!=', 'cancelada')
            ->orderBy('hora', 'asc')
            ->get();

        return view('asistente.agenda.agenda', compact('citas'));
    }

    public function iniciarCita($id)
    {
        $cita = Cita::findOrFail($id);
        $cita->estatus_cita = 'enproceso';
        $cita->save();

        return response()->json(['success' => true]);
    }

    // Nueva función para procesar el cobro desde la agenda
    public function finalizarCita(Request $request, $id)
    {
        $cita = Cita::findOrFail($id);
        
        // 1. Registrar monto y finalizar cita
        $cita->monto_cobrado = $request->monto_cobrado;
        $cita->estatus_cita = 'finalizada';
        $cita->save();

        // 2. Si el asistente marcó finalizar el tratamiento completo
        if ($request->finalizar_tratamiento == 1 && $cita->id_tratamiento) {
            $tratamiento = Tratamiento::find($cita->id_tratamiento);
            if ($tratamiento) {
                $tratamiento->estatus = 'finalizado';
                $tratamiento->save();
            }
        }

        return response()->json(['success' => true]);
    }
}