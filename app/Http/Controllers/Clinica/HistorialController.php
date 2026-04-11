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
        $pacientesBusqueda = Paciente::where('id_clinica', auth()->user()->id_clinica)->get();
        return view('asistente.historial.historial', [
            'paciente' => null,
            'expediente' => null,
            'pacientesBusqueda' => $pacientesBusqueda
        ]);
    }

    public function verHistorial($id)
{
    $pacientesBusqueda = Paciente::where('id_clinica', auth()->user()->id_clinica)->get();
    // Cargamos al paciente con todas sus relaciones necesarias para los modales
    $paciente = Paciente::with([
        'tratamientos.catalogoTratamiento', // Para el nombre del tratamiento
        'tratamientos.citas',               // Para sumar pagos por tratamiento
        'citas.servicio'            // Para ver qué servicio se hizo en cada cita
    ])->findOrFail($id);

    $expediente = ExpedienteClinico::where('id_paciente', $id)->first();
    
    $versionesAnteriores = HistorialExpediente::where('id_paciente', $id)
        ->orderBy('fecha_modificacion', 'desc')
        ->get();

    // --- NUEVO: Cargar Notas de Evolución ---
    // Obtenemos todas las notas de los tratamientos de este paciente
    $id_tratamientos = $paciente->tratamientos->pluck('id_tratamiento');
    $notas = \App\Models\NotasEvolucion::with(['usuario', 'tratamiento.catalogoTratamiento'])
        ->whereIn('id_tratamiento', $id_tratamientos)
        ->orderBy('fecha', 'desc')
        ->orderBy('hora', 'desc')
        ->get();
    
    // --- NUEVO: Buscar si hay una cita activa para el modal ---
    $citaActiva = \App\Models\Cita::where('id_paciente', $id)
        ->where('estatus_cita', 'enproceso')
        ->first();

    if (!$expediente) {
        $expediente = new ExpedienteClinico(['id_paciente' => $id]);
    }

    return view('asistente.historial.historial', compact('paciente', 'expediente', 'versionesAnteriores', 'notas', 'citaActiva', 'pacientesBusqueda'));
}

   public function guardar(Request $request, $id_paciente)
{
    // 1. VALIDACIÓN (Mantenemos tus reglas y mensajes)
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
        'regex' => 'El campo :attribute contiene caracteres especiales no permitidos.'
    ]);

    $pacienteActual = Paciente::findOrFail($id_paciente);
    $expedienteActual = ExpedienteClinico::firstOrCreate(['id_paciente' => $id_paciente]);

    // --- NUEVO: PREPARAR LOS DATOS ENTRANTES PARA COMPARACIÓN ---
    // Limpiamos con trim() para evitar que espacios accidentales cuenten como cambios
    $nuevoPeso = $request->peso;
    $nuevaAlergia = $request->filled('alergias') ? trim($request->alergias) : 'Ninguna';
    $nuevoHereditario = $request->filled('antecedentes_hereditarios') ? trim($request->antecedentes_hereditarios) : 'Sin datos';
    $nuevoPatologico = $request->filled('antecedentes_patologicos') ? trim($request->antecedentes_patologicos) : 'Sin datos';
    $nuevaObservacion = $request->filled('observaciones_generales') ? trim($request->observaciones_generales) : null;

    // --- NUEVO: COMPARACIÓN LÓGICA (Atiende la observación del historial) ---
    // Comparamos lo que ya tenemos en la base de datos contra lo que viene del formulario
    $huboCambios = (
        (float)$pacienteActual->peso !== (float)$nuevoPeso ||
        ($expedienteActual->alergias ?? 'Ninguna') !== $nuevaAlergia ||
        ($expedienteActual->antecedentes_hereditarios ?? 'Sin datos') !== $nuevoHereditario ||
        ($expedienteActual->antecedentes_patologicos ?? 'Sin datos') !== $nuevoPatologico ||
        ($expedienteActual->observaciones_generales) !== $nuevaObservacion
    );

    // RESULTADO ESPERADO: Si no hubo cambios, no guardamos nada y avisamos al usuario
    if (!$huboCambios) {
        return redirect()->back()->with('info', 'No se realizaron cambios en el expediente clínico.');
    }

    // --- SI LLEGAMOS AQUÍ, ES QUE SÍ HUBO CAMBIOS REALES ---

    DB::transaction(function () use ($id_paciente, $pacienteActual, $expedienteActual, $nuevoPeso, $nuevaAlergia, $nuevoHereditario, $nuevoPatologico, $nuevaObservacion) {
        
        // 1. Guardamos la versión ANTERIOR (Auditoría)
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

        // 2. Actualizamos el peso en Pacientes
        $pacienteActual->update(['peso' => $nuevoPeso]);

        // 3. Actualizamos el Expediente con los datos nuevos
        $expedienteActual->update([
            'alergias'                  => $nuevaAlergia,
            'antecedentes_hereditarios' => $nuevoHereditario,
            'antecedentes_patologicos'  => $nuevoPatologico,
            'observaciones_generales'   => $nuevaObservacion,
            'fecha_registro'            => now()->format('Y-m-d'),
        ]);
    });

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

    public function guardarNota(Request $request)
{
    $request->validate([
        'id_tratamiento' => 'required|exists:tratamiento,id_tratamiento',
        'nota_texto' => 'required|string|min:5',
        'indicaciones' => 'nullable|string'
    ]);

    try {
        \App\Models\NotasEvolucion::create([
            'id_tratamiento' => $request->id_tratamiento,
            'id_usuario'     => Auth::id(), // El dentista logueado
            'fecha'          => now()->format('Y-m-d'),
            'hora'           => now()->format('H:i:s'),
            'nota_texto'     => $request->nota_texto,
            'indicaciones'   => $request->indicaciones,
        ]);

        return redirect()->back()->with('success', 'Nota de evolución guardada correctamente.');
    } catch (\Exception $e) {
        return redirect()->back()->with('error', 'Error al guardar la nota: ' . $e->getMessage());
    }
}

public function actualizarPrecioTratamiento(Request $request, $id)
{
    // Verificación de seguridad por rol
    if (auth()->user()->rol !== 'dentista') {
        return response()->json([
            'success' => false, 
            'message' => 'Solo el dentista puede asignar presupuestos.'
        ], 403);
    }

    $request->validate([
        'precio_estimado' => 'required|numeric|min:0'
    ]);

    try {
        // Buscamos el tratamiento por su ID
        $tratamiento = \App\Models\Tratamiento::findOrFail($id);
        $tratamiento->precio_estimado = $request->precio_estimado;
        $tratamiento->save();

        return response()->json(['success' => true]);
    } catch (\Exception $e) {
        return response()->json([
            'success' => false, 
            'message' => 'Error al actualizar la base de datos.'
        ], 500);
    }
}
}