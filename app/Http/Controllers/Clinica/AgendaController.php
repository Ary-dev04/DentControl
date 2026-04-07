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
        try {
            $cita = Cita::findOrFail($id);
            $cita->estatus_cita = 'enproceso';
            
            // CAPTURAMOS LA HORA DE INICIO REAL
            $cita->hora_inicio_real = Carbon::now()->format('H:i:s');
            
            $cita->save();

            return response()->json(['success' => true]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()]);
        }
    }

    // Nueva función para procesar el cobro desde la agenda
 public function finalizarCita(Request $request, $id)
{
    try {
        $cita = Cita::findOrFail($id);
        
        // 1. Capturamos el momento actual (Fin de la cita)
        $ahora = Carbon::now();
        $horaFinal = $ahora->format('H:i:s');

        // 2. Calculamos la duración real
        // Si por alguna razón no se marcó el inicio, la duración real será 0 o mantenemos la previa
        $duracionReal = $cita->duracion; // Valor por defecto
        
        if ($cita->hora_inicio_real) {
            $inicio = Carbon::parse($cita->hora_inicio_real);
            // diffInMinutes calcula la diferencia absoluta en minutos
            $duracionReal = $inicio->diffInMinutes($ahora);
        }

        // 3. Registrar monto, estatus, horas y SOBRESCRIBIR duración
        $cita->monto_cobrado = $request->monto_cobrado;
        $cita->estatus_cita = 'finalizada';
        $cita->hora_final_real = $horaFinal;
        $cita->duracion = $duracionReal; // Aquí se sobrescribe el estimado por el real
        
        $cita->save();

        // 4. Si el asistente marcó finalizar el tratamiento completo
        if ($request->finalizar_tratamiento == 1 && $cita->id_tratamiento) {
            $tratamiento = Tratamiento::find($cita->id_tratamiento);
            if ($tratamiento) {
                $tratamiento->estatus = 'finalizado';
                $tratamiento->save();
            }
        }

        return response()->json([
            'success' => true, 
            'duracion_real' => $duracionReal
        ]);

    } catch (\Exception $e) {
        return response()->json([
            'success' => false, 
            'message' => $e->getMessage()
        ]);
    }
}

    public function cancelar($id)
{
    try {
        $cita = Cita::findOrFail($id);
        $cita->estatus_cita = 'cancelada';
        $cita->save();

        return response()->json(['success' => true]);
    } catch (\Exception $e) {
        return response()->json(['success' => false, 'message' => $e->getMessage()]);
    }
}
}