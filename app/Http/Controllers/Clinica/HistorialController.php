<?php

namespace App\Http\Controllers\Clinica;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Paciente;
use App\Models\ExpedienteClinico;
use Illuminate\Support\Facades\DB; 
use Illuminate\Support\Facades\Auth;
use App\Models\HistorialExpediente; // Tu nuevo modelo

class HistorialController extends Controller
{
    public function index()
    {
        return view('asistente.historial.historial', [
            'paciente' => null,
            'expediente' => null
        ]);
    }

    public function verHistorial($id)
{
    $paciente = Paciente::findOrFail($id);
    $expediente = ExpedienteClinico::where('id_paciente', $id)->first();
    
    // Solo traemos el historial de cambios de datos (versiones)
    $versionesAnteriores = HistorialExpediente::where('id_paciente', $id)
        ->orderBy('fecha_modificacion', 'desc')
        ->get();

    if (!$expediente) {
        $expediente = new ExpedienteClinico(['id_paciente' => $id]);
    }

    // Ya no enviamos $historialCitas
    return view('asistente.historial.historial', compact('paciente', 'expediente', 'versionesAnteriores'));
}

    public function guardar(Request $request, $id_paciente)
    {
        $request->validate([
            'peso' => 'required|numeric',
        ]);

        // --- INICIO LÓGICA DE HISTORIAL (AUDITORÍA) ---
        
        // Buscamos los datos que tiene actualmente el paciente antes de cambiarlos
        $pacienteActual = Paciente::findOrFail($id_paciente);
        $expedienteActual = ExpedienteClinico::where('id_paciente', $id_paciente)->first();

        // Creamos el registro en la tabla de historial con la foto del "PASADO"
        HistorialExpediente::create([
            'id_paciente'               => $id_paciente,
            'peso'                      => $pacienteActual->peso, // El peso viejo
            'alergias'                  => $expedienteActual->alergias ?? 'Ninguna',
            'antecedentes_hereditarios' => $expedienteActual->antecedentes_hereditarios ?? 'Sin datos',
            'antecedentes_patologicos'  => $expedienteActual->antecedentes_patologicos ?? 'Sin datos',
            'observaciones_generales'   => $expedienteActual->observaciones_generales ?? 'Sin observaciones',
            'id_usuario'                => Auth::id(), // Quién guardó esto
            'fecha_modificacion'        => now()
        ]);

        // --- FIN LÓGICA DE HISTORIAL ---

        // 2. Ahora sí, actualizamos a los datos NUEVOS en la tabla PACIENTES
        $pacienteActual->peso = $request->peso;
        $pacienteActual->save();

        // 3. Actualizar o Crear los datos NUEVOS en la tabla EXPEDIENTE_CLINICO
        ExpedienteClinico::updateOrCreate(
            ['id_paciente' => $id_paciente],
            [
                'antecedentes_hereditarios' => $request->antecedentes_hereditarios,
                'antecedentes_patologicos'  => $request->antecedentes_patologicos,
                'alergias'                  => $request->alergias,
                'observaciones_generales'   => $request->observaciones_generales,
                'fecha_registro'            => $request->fecha_registro ?? now()->format('Y-m-d'),
            ]
        );

        return redirect()->back()->with('success', 'El expediente se ha actualizado y se guardó una copia de la versión anterior.');
    }

    // El método buscar se mantiene igual...
    public function buscar(Request $request)
    {
        $term = $request->get('q');
        $pacientes = Paciente::where('nombre', 'LIKE', '%' . $term . '%')
            ->orWhere('apellido_paterno', 'LIKE', '%' . $term . '%')
            ->orWhere('curp', 'LIKE', '%' . $term . '%')
            ->limit(8)
            ->get(['id_paciente', 'nombre', 'apellido_paterno', 'curp']);

        return response()->json($pacientes);
    }
}