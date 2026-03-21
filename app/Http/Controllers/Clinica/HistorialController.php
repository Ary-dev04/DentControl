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
    // 1. VALIDACIÓN ESTRICTA (Atiende las observaciones 2 y 3)
    $reglaClinica = 'nullable|regex:/^[a-zA-ZñÑáéíóúÁÉÍÓÚ\s\-\(\)\.]+$/u';

    $request->validate([
        'peso' => 'required|numeric|min:1|max:250', 
        'alergias' => $reglaClinica,
        'antecedentes_hereditarios' => $reglaClinica,
        'antecedentes_patologicos' => $reglaClinica,
        'observaciones_generales' => 'nullable'
    ], [
        'peso.max' => 'El peso ingresado excede el límite lógico permitido.',
        'peso.min' => 'El peso debe ser mayor a 0.',
        'regex' => 'El campo :attribute contiene caracteres especiales no permitidos (solo letras, espacios, guiones y paréntesis).'
    ]);

    // --- INICIO LÓGICA DE HISTORIAL (AUDITORÍA) ---
    
    $pacienteActual = Paciente::findOrFail($id_paciente);
    $expedienteActual = ExpedienteClinico::firstOrCreate(['id_paciente' => $id_paciente]);

    // Guardamos la versión ACTUAL antes de que sea reemplazada por la nueva
    HistorialExpediente::create([
        'id_paciente'               => $id_paciente,
        'peso'                      => $pacienteActual->peso, 
        'alergias'                  => $expedienteActual->alergias ?? 'Ninguna',
        'antecedentes_hereditarios' => $expedienteActual->antecedentes_hereditarios ?? 'Sin datos',
        'antecedentes_patologicos'  => $expedienteActual->antecedentes_patologicos ?? 'Sin datos',
        'observaciones_generales'   => $expedienteActual->observaciones_generales ?? 'Sin observaciones',
        'id_usuario'                => Auth::id(),
        'fecha_modificacion'        => now()
    ]);

    // --- FIN LÓGICA DE HISTORIAL ---

    // 2. Actualizamos el peso en la tabla PACIENTES
    $pacienteActual->update([
        'peso' => $request->peso
    ]);

    // 3. Actualizamos con los datos NUEVOS (Manejando vacíos por defecto)
    $expedienteActual->update([
        // Si el asistente borra todo en alergias, se guarda "Ninguna"
        'alergias'                  => $request->filled('alergias') ? $request->alergias : 'Ninguna',
        
        // Si deja vacíos los antecedentes, guardamos "Sin datos" para mantener el orden clínico
        'antecedentes_hereditarios' => $request->filled('antecedentes_hereditarios') ? $request->antecedentes_hereditarios : 'Sin datos',
        'antecedentes_patologicos'  => $request->filled('antecedentes_patologicos') ? $request->antecedentes_patologicos : 'Sin datos',
        
        'observaciones_generales'   => $request->observaciones_generales,
        'fecha_registro'            => now()->format('Y-m-d'),
    ]);

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