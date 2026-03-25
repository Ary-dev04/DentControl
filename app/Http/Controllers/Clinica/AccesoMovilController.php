<?php

namespace App\Http\Controllers\Clinica;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Paciente;
use App\Models\AccesoMovil;
use App\Mail\AccesoMovilMail;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

class AccesoMovilController extends Controller
{
    public function index(Request $request)
    {
        $paciente = null;
    if ($request->has('id_paciente')) {
        // Cargamos al paciente y sus tratamientos CON su respectivo catálogo
        $paciente = Paciente::with(['tratamientos.catalogoTratamiento', 'accesoMovil'])
                            ->find($request->id_paciente);
    }

        // Esta es la ruta de tu vista: asistente/app/acceso.blade.php
        return view('asistente.app.acceso', compact('paciente'));
    }

    public function habilitarAcceso(Request $request)
{
    // 1. Validamos solo el ID del paciente (ya no el tratamiento manual)
    // Nota: cambié 'pacientes' por 'paciente' según tu descripción de tabla
    $request->validate([
        'id_paciente' => 'required|exists:paciente,id_paciente',
    ]);

    $paciente = Paciente::findOrFail($request->id_paciente);

    // 2. Lógica de selección de correo (Paciente > Tutor)
    $correoDestino = $paciente->email ?: $paciente->email_tutor;

    if (!$correoDestino) {
        return redirect()->back()->with('error', 'El paciente no tiene correo registrado ni se encontró correo de un tutor.');
    }

    // 3. Generar Usuario Único
    $usuario = strtolower(explode(' ', $paciente->nombre)[0]) . $paciente->id_paciente . rand(10, 99);
    
    // 4. Generar Contraseña Temporal Plana
    $passwordPlano = \Illuminate\Support\Str::random(8); 

    // 5. Guardar o actualizar en la tabla acceso_movil
    \App\Models\AccesoMovil::updateOrCreate(
        ['id_paciente' => $paciente->id_paciente],
        [
            'usuario_movil' => $usuario,
            'password'      => \Illuminate\Support\Facades\Hash::make($passwordPlano),
            'estatus'       => 'temporal',
            'updated_at'    => now()
        ]
    );

    // 6. Envío del correo
    try {
        \Illuminate\Support\Facades\Mail::to($correoDestino)->send(new \App\Mail\AccesoMovilMail($usuario, $passwordPlano, $paciente->nombre));
        
        // Mensaje personalizado según a quién se le envió
        $destinoMsg = $paciente->email ? "al paciente" : "al tutor (" . $paciente->nombre_tutor . ")";
        
        return redirect()->back()->with('success', "¡Acceso habilitado! Credenciales enviadas $destinoMsg a: $correoDestino");
    } catch (\Exception $e) {
        return redirect()->back()->with('error', 'Acceso creado en sistema, pero falló el envío del correo: ' . $e->getMessage());
    }
}


    public function buscarPacientesAcceso(Request $request)
{
    $q = $request->get('q');

    // Solo buscamos pacientes que tengan al menos un tratamiento activo
    $pacientes = Paciente::where(function($query) use ($q) {
            $query->where('nombre', 'LIKE', "%$q%")
                  ->orWhere('apellido_paterno', 'LIKE', "%$q%")
                  ->orWhere('curp', 'LIKE', "%$q%");
        })
        ->whereHas('tratamientos', function($query) {
            $query->where('estatus', 'curso'); // Solo los que están en curso
        })
        ->select('id_paciente', 'nombre', 'apellido_paterno', 'email', 'curp')
        ->take(10)
        ->get();

    return response()->json($pacientes);
}
}