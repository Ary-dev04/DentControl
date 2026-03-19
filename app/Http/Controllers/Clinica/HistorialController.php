<?php

namespace App\Http\Controllers\Clinica;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Paciente;
use App\Models\ExpedienteClinico;

class HistorialController extends Controller
{
    // Este método es para el botón del MENÚ (acceso general)
    public function index()
    {
        return view('asistente.historial.historial', [
            'paciente' => null,
            'expediente' => null
        ]);
    }

    // Este método es para el botón VER de la TABLA (acceso directo)
    public function verHistorial($id)
    {
        $paciente = Paciente::findOrFail($id);
        $expediente = ExpedienteClinico::where('id_paciente', $id)->first();
        
        if (!$expediente) {
            $expediente = new ExpedienteClinico();
            $expediente->id_paciente = $id;
        }

        return view('asistente.historial.historial', compact('paciente', 'expediente'));
    }

    public function buscar(Request $request)
{
    // Obtenemos el término de búsqueda (ej: "Mari")
    $term = $request->get('q');

    // Buscamos en la base de datos
    $pacientes = Paciente::where('nombre', 'LIKE', '%' . $term . '%')
        ->orWhere('apellido_paterno', 'LIKE', '%' . $term . '%')
        ->orWhere('curp', 'LIKE', '%' . $term . '%')
        ->limit(8) // Limitamos a 8 resultados para que sea rápido
        ->get(['id_paciente', 'nombre', 'apellido_paterno', 'curp']); // Solo pedimos lo necesario

    // Devolvemos la respuesta en formato JSON para el JavaScript
    return response()->json($pacientes);
}
}