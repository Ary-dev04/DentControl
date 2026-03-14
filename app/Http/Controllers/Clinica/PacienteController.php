<?php

namespace App\Http\Controllers\Clinica;

use App\Http\Controllers\Controller;
use App\Models\Paciente;
use App\Models\CatalogoServicio;
use App\Models\CatalogoTratamiento;
use App\Models\Tratamiento;
use App\Models\Cita;
// Si tienes los modelos creados, úsalos. Si no, usamos DB::table
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class PacienteController extends Controller
{
    public function index()
    {
        $id_clinica = Auth::user()->id_clinica;

        $pacientes = Paciente::where('id_clinica', $id_clinica)
            ->where('estatus', 'activo')
            ->get();

        $catServicios = CatalogoServicio::where('id_clinica', $id_clinica)->where('estatus', 'activo')->get();
        $catTratamientos = CatalogoTratamiento::where('id_clinica', $id_clinica)->where('estatus', 'activo')->get();

        return view('asistente.pacientes.index', compact('pacientes', 'catServicios', 'catTratamientos'));
    }

    public function obtenerDuracion(Request $request)
    {
        $tipo = $request->query('tipo');
        $id = $request->query('id');

        if ($tipo === 'tratamiento') {
            $item = DB::table('catalogo_tratamientos')->where('id_cat_tratamientos', $id)->first();
            $duracion = $item ? $item->duracion_sugerido_sesion : 0;
        } else {
            $item = DB::table('catalogo_servicios')->where('id_cat_servicio', $id)->first();
            $duracion = $item ? $item->duracion : 0;
        }

        return response()->json(['duracion' => $duracion]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nombre'           => 'required|string|max:50|regex:/^[a-zA-ZáéíóúÁÉÍÓÚñÑ ]+$/',
            'apellido_paterno' => 'required|string|max:50|regex:/^[a-zA-ZáéíóúÁÉÍÓÚñÑ ]+$/',
            'apellido_materno' => 'required|string|max:50|regex:/^[a-zA-ZáéíóúÁÉÍÓÚñÑ ]+$/',
            'fecha_nacimiento' => 'required|date|after:1900-01-01|before:today',
            'sexo'             => 'required|in:hombre,mujer',
            'email'            => 'required|email|unique:paciente,email|max:100',
            'telefono'         => 'required|digits:10',
            'curp'             => ['required', 'string', 'size:18', 'unique:paciente,curp', 'regex:/^[A-Z]{4}[0-9]{6}[H,M][A-Z]{5}[0-9,A-Z][0-9]$/'],
            'ocupacion'        => 'nullable|string|max:255',
            'peso'             => 'required|numeric|between:0.5,500',
            'calle'            => 'required|string|max:255',
            'num_ext'          => 'required|alpha_num|max:10',
            'num_int'          => 'nullable|alpha_num|max:10',
            'colonia'          => 'required|string|max:100',
            'ciudad'           => 'required|string|max:100',
            'estado'           => 'required|string|max:100',
            'codigo_postal'    => 'required|digits:5',
            'fecha_cita'       => 'required|date|after:now',
            'duracion'         => 'required|integer|min:5|max:480',
            'motivo_consulta'  => 'required|string|max:255',
            'tipo_atencion'    => 'required|in:tratamiento,servicio',
            'alergias'         => 'required|string|max:500',
        ]);

        $id_clinica = Auth::user()->id_clinica;
        $id_usuario = Auth::id();

        try {
            return DB::transaction(function () use ($request, $validated, $id_clinica, $id_usuario) {
                
                // 1. Crear Paciente
                $paciente = Paciente::create([
                    'id_clinica'       => $id_clinica,
                    'nombre'           => $validated['nombre'],
                    'apellido_paterno' => $validated['apellido_paterno'],
                    'apellido_materno' => $validated['apellido_materno'],
                    'fecha_nacimiento' => $validated['fecha_nacimiento'],
                    'sexo'             => $validated['sexo'],
                    'email'            => $validated['email'],
                    'telefono'         => $validated['telefono'],
                    'curp'             => $validated['curp'],
                    'ocupacion'        => $validated['ocupacion'],
                    'peso'             => $validated['peso'],
                    'calle'            => $validated['calle'],
                    'num_ext'          => $validated['num_ext'],
                    'num_int'          => $validated['num_int'],
                    'colonia'          => $validated['colonia'],
                    'ciudad'           => $validated['ciudad'],
                    'estado'           => $validated['estado'],
                    'codigo_postal'    => $validated['codigo_postal'],
                    'estatus'          => 'activo',
                ]);

                // 2. Crear Expediente Clínico (Base para el historial)
                DB::table('expediente_clinico')->insert([
                    'id_paciente'      => $paciente->id_paciente,
                    'fecha_registro'   => now()->format('Y-m-d'),
                    'alergias'         => $validated['alergias'],
                    'created_at'       => now(),
                ]);

                $dt = new \DateTime($validated['fecha_cita']);
                $fecha = $dt->format('Y-m-d');
                $hora  = $dt->format('H:i:s');

                $id_tratamiento_rel = null;
                $id_cat_servicio_rel = null;

                // 3. Lógica de Atención
                if ($validated['tipo_atencion'] === 'tratamiento') {
                    $nuevoTratamiento = Tratamiento::create([
                        'id_paciente'         => $paciente->id_paciente,
                        'id_usuario'          => $id_usuario,
                        'id_clinica'          => $id_clinica,
                        'id_cat_tratamientos' => $request->id_cat_tratamiento,
                        'diagnostico_inicial' => $validated['motivo_consulta'],
                        'fecha_inicio'        => $fecha,
                        'estatus'             => 'curso',
                    ]);
                    $id_tratamiento_rel = $nuevoTratamiento->id_tratamiento;

                    // Primera Nota de Evolución para el historial
                    DB::table('notas_evolucion')->insert([
                        'id_tratamiento' => $id_tratamiento_rel,
                        'id_usuario'     => $id_usuario,
                        'fecha'          => $fecha,
                        'hora'           => $hora,
                        'nota_texto'     => 'Registro inicial de tratamiento: ' . $validated['motivo_consulta'],
                        'created_at'     => now(),
                    ]);
                } else {
                    $id_cat_servicio_rel = $request->id_cat_servicio;
                }

                // 4. Crear Cita
                DB::table('citas')->insert([
                    'id_paciente'     => $paciente->id_paciente,
                    'id_usuario'      => $id_usuario,
                    'id_clinica'      => $id_clinica,
                    'id_cat_servicio' => $id_cat_servicio_rel,
                    'id_tratamiento'  => $id_tratamiento_rel,
                    'fecha'           => $fecha,
                    'hora'            => $hora,
                    'motivo_consulta' => $validated['motivo_consulta'],
                    'duracion'        => $validated['duracion'],
                    'estatus_cita'    => 'programada',
                    'created_at'      => now(),
                ]);

                return redirect()->back()->with('success', 'Paciente registrado con expediente y cita programada.');
            });
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Error al registrar: ' . $e->getMessage());
        }
    }

    public function storeCitaExistente(Request $request)
    {
        $validated = $request->validate([
            'id_paciente'     => 'required|exists:paciente,id_paciente',
            'fecha_cita'      => 'required|date|after:now',
            'duracion'        => 'required|integer|min:5|max:480',
            'motivo_consulta' => 'required|string|max:255',
            'tipo_atencion'   => 'required|in:tratamiento,servicio',
        ]);

        $id_clinica = Auth::user()->id_clinica;
        $id_usuario = Auth::id();

        try {
            DB::transaction(function () use ($request, $validated, $id_clinica, $id_usuario) {
                
                $dt = new \DateTime($validated['fecha_cita']);
                $fecha = $dt->format('Y-m-d');
                $hora  = $dt->format('H:i:s');

                $id_tratamiento_rel = null;
                $id_cat_servicio_rel = null;

                if ($validated['tipo_atencion'] === 'tratamiento') {
                    // Se asume que id_cat_tratamiento viene del select del modal
                    $id_tratamiento_rel = $request->id_cat_tratamiento;

                    // Agregamos una nota de evolución indicando que se programó un seguimiento
                    DB::table('notas_evolucion')->insert([
                        'id_tratamiento' => $id_tratamiento_rel,
                        'id_usuario'     => $id_usuario,
                        'fecha'          => $fecha,
                        'hora'           => $hora,
                        'nota_texto'     => 'Cita de seguimiento programada: ' . $validated['motivo_consulta'],
                        'created_at'     => now(),
                    ]);
                } else {
                    $id_cat_servicio_rel = $request->id_cat_servicio;
                }

                DB::table('citas')->insert([
                    'id_paciente'     => $validated['id_paciente'],
                    'id_usuario'      => $id_usuario,
                    'id_clinica'      => $id_clinica,
                    'id_cat_servicio' => $id_cat_servicio_rel,
                    'id_tratamiento'  => $id_tratamiento_rel,
                    'fecha'           => $fecha,
                    'hora'            => $hora,
                    'motivo_consulta' => $validated['motivo_consulta'],
                    'duracion'        => $validated['duracion'],
                    'estatus_cita'    => 'programada',
                    'created_at'      => now(),
                ]);
            });

            return redirect()->back()->with('success', 'Cita para paciente existente programada correctamente.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Error al programar cita: ' . $e->getMessage());
        }
    }

    public function tratamientosActivos($id)
    {
        $tratamientos = Tratamiento::with('catalogoTratamiento')
            ->where('id_paciente', $id)
            ->where('estatus', 'curso')
            ->get();

        $data = $tratamientos->map(function($t) {
            return [
                'id_tratamiento' => $t->id_tratamiento,
                'nombre' => $t->catalogoTratamiento->nombre ?? 'Tratamiento sin nombre'
            ];
        });

        return response()->json($data);
    }

    public function getCitasOcupadas()
{
    $id_clinica = Auth::user()->id_clinica;

    $citas = DB::table('citas')
        ->where('id_clinica', $id_clinica)
        ->whereIn('estatus_cita', ['programada', 'confirmada'])
        ->get();

    $eventos = [];

    foreach ($citas as $cita) {
    $inicio = \Carbon\Carbon::parse($cita->fecha . ' ' . $cita->hora);
    $fin = (clone $inicio)->addMinutes($cita->duracion);

    $eventos[] = [
        'id'    => $cita->id_cita,
        'title' => 'OCUPADO', 
        // Cambiamos toIso8601String() por format('Y-m-d\TH:i:s')
        'start' => $inicio->format('Y-m-d\TH:i:s'), 
        'end'   => $fin->format('Y-m-d\TH:i:s'),   
        'backgroundColor' => '#f87171', 
        'borderColor' => '#ef4444',
        'textColor' => '#ffffff',
        'display' => 'block'
    ];
}

    return response()->json($eventos);
}
}