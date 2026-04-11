<?php

namespace App\Http\Controllers\Dentista;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Paciente;
use App\Models\Tratamiento;
use App\Models\NotasEvolucion; 
use Illuminate\Support\Facades\Auth;

class TratamientoController extends Controller
{
    public function index()
    {
        $pacientes = Paciente::where('id_clinica', Auth::user()->id_clinica)
        ->has('tratamientos') // Esta es la clave: filtra por relación
        ->get();
        return view('dentista.tratamientos.tratamientos', compact('pacientes'));
    }

public function buscarPaciente(Request $request)
{
    // NO uses strtolower aquí, deja que llegue tal cual
    $query = $request->get('q'); 
    
    $pacientes = Paciente::where('id_clinica', Auth::user()->id_clinica)
        ->where(function($q) use ($query) {
            // Usamos un LIKE simple que en la mayoría de servers ignora mayúsculas
            $q->where('nombre', 'LIKE', "%{$query}%")
              ->orWhere('apellido_paterno', 'LIKE', "%{$query}%")
              ->orWhere('curp', 'LIKE', "%{$query}%");
        })
        ->limit(5)
        ->get();

    return response()->json($pacientes);
}

    public function obtenerTratamientos($id_paciente)
    {
        $tratamientos = Tratamiento::where('id_paciente', $id_paciente)
            ->join('catalogo_tratamientos', 'tratamiento.id_cat_tratamientos', '=', 'catalogo_tratamientos.id_cat_tratamientos')
            ->select(
                'tratamiento.id_tratamiento', 
                'tratamiento.created_at', 
                'catalogo_tratamientos.nombre as nombre_tratamiento'
            )
            ->orderBy('tratamiento.created_at', 'desc')
            ->get();
            
        return response()->json($tratamientos);
    }

    public function detalleTratamiento($id)
    {
        $tratamiento = Tratamiento::findOrFail($id);
        
        // Al traer las notas, Eloquent ya incluye el campo 'indicaciones'
        $notas = NotasEvolucion::where('id_tratamiento', $id)
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json([
            'tratamiento' => $tratamiento,
            'notas' => $notas
        ]);
    }

    /**
     * Procesa la actualización de la info base y/o la nueva nota e indicaciones
     */
    public function actualizar(Request $request)
    {
        // 1. Validación: Agregamos 'indicaciones'
        $request->validate([
            'id_tratamiento'     => 'required|exists:tratamiento,id_tratamiento',
            'nota_texto'         => 'nullable|string|min:3',
            'indicaciones'       => 'nullable|string', // Nuevo campo validado
            'diagnostico_inicial'=> 'nullable|string',
            'fecha_inicio'       => 'nullable|date',
            'fecha_fin'          => 'nullable|date',
            'precio_estimado'    => 'nullable|numeric',
        ]);

        $tratamiento = Tratamiento::findOrFail($request->id_tratamiento);

        // 2. Actualizar información base del tratamiento
        $tratamiento->update([
            'diagnostico_inicial' => $request->diagnostico_inicial,
            'fecha_inicio'        => $request->fecha_inicio,
            'fecha_fin'           => $request->fecha_fin,
            'precio_estimado'     => $request->precio_estimado,
        ]);

        // 3. Crear la nota SI hay evolución O SI hay indicaciones
        if ($request->filled('nota_texto') || $request->filled('indicaciones')) {
            NotasEvolucion::create([
                'id_tratamiento' => $tratamiento->id_tratamiento,
                'id_usuario'     => Auth::id(),
                'fecha'          => now()->toDateString(),
                'hora'           => now()->toTimeString(),
                'nota_texto'     => $request->nota_texto,
                'indicaciones'   => $request->indicaciones // Guardamos el nuevo campo
            ]);
            $mensaje = 'Datos actualizados, evolución e indicaciones registradas.';
        } else {
            $mensaje = 'La información base del tratamiento ha sido actualizada.';
        }

        // 4. Redirección con toda la memoria necesaria para que el JS haga su magia
        return redirect()->back()->with([
            'success' => $mensaje,
            'last_id' => $request->id_tratamiento,
            'last_paciente_id' => $tratamiento->id_paciente
        ])->withInput();
    }
}