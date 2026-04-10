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

        //$pacientes = Paciente::where('id_clinica', $id_clinica)
          //  ->where('estatus', 'activo')
            //->get();

        // Traemos pacientes activos con su última cita (ordenadas por fecha y hora)
    $pacientes = Paciente::where('id_clinica', $id_clinica)
        ->where('estatus', 'activo')
        ->with(['citas' => function($query) {
            $query->orderBy('fecha', 'desc')->orderBy('hora', 'desc');
        }])
        ->get();

        $catServicios = CatalogoServicio::where('id_clinica', $id_clinica)->where('estatus', 'activo')->get();
        $catTratamientos = CatalogoTratamiento::where('id_clinica', $id_clinica)->where('estatus', 'activo')->get();

        return view('asistente.pacientes.index', compact('pacientes', 'catServicios', 'catTratamientos'));
    }

    public function obtenerDuracion(Request $request)
{
    $tipo = $request->query('tipo');
    $id = $request->query('id');
    $duracion = 0;

    if ($tipo === 'tratamiento') {
        // Nuevo tratamiento (Catálogo)
        $item = DB::table('catalogo_tratamientos')->where('id_cat_tratamientos', $id)->first(); // Verifica si es id_cat_tratamientos o id_cat_treatments
        $duracion = $item ? $item->duracion_sugerido_sesion : 0;
    } 
    elseif ($tipo === 'tratamiento_seguimiento') {
        // SEGUIMIENTO: Buscamos el tratamiento que ya tiene el paciente
        $tratamientoPaciente = DB::table('tratamiento')->where('id_tratamiento', $id)->first();
        if ($tratamientoPaciente) {
            $item = DB::table('catalogo_tratamientos')
                ->where('id_cat_tratamientos', $tratamientoPaciente->id_cat_tratamientos)
                ->first();
            $duracion = $item ? $item->duracion_sugerido_sesion : 0;
        }
    } 
    else {
        // Servicio Rápido
        $item = DB::table('catalogo_servicios')->where('id_cat_servicio', $id)->first();
        $duracion = $item ? $item->duracion : 0;
    }

    return response()->json(['duracion' => $duracion]);
}

    public function store(Request $request)
    {
        $esMenor = $request->filled('nombre_tutor');

        $validated = $request->validate([
            'nombre'           => 'required|string|max:50|regex:/^[a-zA-ZáéíóúÁÉÍÓÚñÑ ]+$/',
            'apellido_paterno' => 'required|string|max:50|regex:/^[a-zA-ZáéíóúÁÉÍÓÚñÑ ]+$/',
            'apellido_materno' => 'required|string|max:50|regex:/^[a-zA-ZáéíóúÁÉÍÓÚñÑ ]+$/',
            'fecha_nacimiento' => 'required|date|after:1900-01-01|before:today',
            'sexo'             => 'required|in:hombre,mujer',
            'email'            => $esMenor ? 'nullable|email|max:100' : 'required|email|unique:paciente,email|max:100',
            'telefono'         => $esMenor ? 'nullable|digits:10' : 'required|digits:10',
            'curp'             => ['required', 'string', 'size:18', 'unique:paciente,curp', 'regex:/^[A-Z]{4}[0-9]{6}[H,M][A-Z]{5}[0-9,A-Z][0-9]$/'],
            'ocupacion'        => $esMenor ? 'nullable|string|max:255' : 'required|string|max:255',
            'grado_estudio'    => $esMenor ? 'required|string|max:100' : 'nullable|string|max:100',
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
            'tiene_alergias' => 'required|in:si,no',
            'alergias' => 'required_if:tiene_alergias,si|max:255',
            'precio_estimado'  => 'nullable|numeric|min:0',

            'nombre_tutor'     => $esMenor ? 'required|string|max:100' : 'nullable',
            'parentesco_tutor' => $esMenor ? 'required|string|max:50' : 'nullable',
            'telefono_tutor'   => $esMenor ? 'required|digits:10' : 'nullable',
            'email_tutor'      => $esMenor ? 'required|email|max:100' : 'nullable',
        ]);

        $id_clinica = Auth::user()->id_clinica;
        $id_usuario = Auth::id();

        try {
         DB::transaction(function () use ($request, $validated, $id_clinica, $id_usuario, $esMenor) {
                
                // 1. Crear Paciente
                $paciente = Paciente::create([
                    'id_clinica'       => $id_clinica,
                    'nombre'           => $validated['nombre'],
                    'apellido_paterno' => $validated['apellido_paterno'],
                    'apellido_materno' => $validated['apellido_materno'],
                    'fecha_nacimiento' => $validated['fecha_nacimiento'],
                    'sexo'             => $validated['sexo'],
                    'email'            => $esMenor ? null : $validated['email'], // Guardamos NULL si es menor
                    'telefono'         => $esMenor ? null : $validated['telefono'], // Guardamos NULL si es menor
                    'curp'             => $validated['curp'],
                    'ocupacion'        => $esMenor ? null : $validated['ocupacion'],
                    'grado_estudio'    => $esMenor ? $validated['grado_estudio'] : null,
                    'peso'             => $validated['peso'],
                    'calle'            => $validated['calle'],
                    'num_ext'          => $validated['num_ext'],
                    'num_int'          => $validated['num_int'],
                    'colonia'          => $validated['colonia'],
                    'ciudad'           => $validated['ciudad'],
                    'estado'           => $validated['estado'],
                    'codigo_postal'    => $validated['codigo_postal'],
                    'estatus'          => 'activo',

                    'nombre_tutor'     => $validated['nombre_tutor'],
                    'parentesco_tutor' => $validated['parentesco_tutor'],
                    'telefono_tutor'   => $validated['telefono_tutor'],
                    'email_tutor'      => $validated['email_tutor'],
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
                        'diagnostico_inicial' => null,
                        'precio_estimado'     => $request->precio_estimado,
                        'fecha_inicio'        => $fecha,
                        'estatus'             => 'curso',
                    ]);
                    $id_tratamiento_rel = $nuevoTratamiento->id_tratamiento;
                    $id_cat_servicio_rel = null;

                    // Primera Nota de Evolución para el historial
                    DB::table('notas_evolucion')->insert([
                        'id_tratamiento' => $id_tratamiento_rel,
                        'id_usuario'     => $id_usuario,
                        'fecha'          => $fecha,
                        'hora'           => $hora,
                        'nota_texto'     => 'Plan de tratamiento abierto. Diagnóstico y presupuesto pendientes de valoración clínica.',
                        'created_at'     => now(),
                    ]);
                } else {
                    $id_cat_servicio_rel = $request->id_cat_servicio;
                    $id_tratamiento_rel = null;
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
            });

             //return redirect()->back()->with('success', 'Paciente registrado con expediente y cita programada.');
             return redirect()->route('pacientes.index')
                     ->with('success', 'Paciente registrado con expediente y cita programada.');
        } catch (\Exception $e) {
            //return redirect()->back()->with('error', 'Error al registrar: ' . $e->getMessage());
            return redirect()->route('pacientes.index')
                     ->with('error', 'Error al registrar: ' . $e->getMessage());
        }
    }

    public function storeCitaExistente(Request $request)
    {
        $validated = $request->validate([
            'id_paciente'     => 'required|exists:paciente,id_paciente',
            'fecha_cita'      => 'required|date|after:now',
            'duracion'        => 'required|integer|min:5|max:480',
            'motivo_consulta' => 'required|string|max:255',
            'tipo_atencion_ex' => 'required|in:seguimiento,nuevo_tratamiento,servicio',
            'precio_estimado' => 'nullable|numeric|min:0',
        ]);

        $id_clinica = Auth::user()->id_clinica;
        $id_usuario = Auth::id();

        // --- INICIO VALIDACIÓN DUPLICADOS ---
    $dt = new \DateTime($validated['fecha_cita']);
    $fecha = $dt->format('Y-m-d');
    $hora  = $dt->format('H:i:s');

    // Buscamos si el paciente ya tiene una cita ese mismo día en esta clínica
    $citaDuplicada = DB::table('citas')
        ->where('id_paciente', $validated['id_paciente'])
        ->where('fecha', $fecha)
        ->whereIn('estatus_cita', ['programada', 'confirmada'])
        ->exists();

    if ($citaDuplicada) {
        return redirect()->route('pacientes.index')//->back() Cambiado back() por route para evitar el JSON
            ->with('error', 'El paciente ya tiene una cita programada para el día seleccionado.')
            ->withInput();
    }
    // --- FIN VALIDACIÓN DUPLICADOS ---

        try {
            DB::transaction(function () use ($request, $validated, $id_clinica, $id_usuario) {
                
                $dt = new \DateTime($validated['fecha_cita']);
                $fecha = $dt->format('Y-m-d');
                $hora  = $dt->format('H:i:s');

                //$id_tratamiento_rel = null;
                //$id_cat_servicio_rel = null;
                $id_tratamiento_final = null;
                $id_servicio_final = null;

                // LÓGICA SEGÚN EL TIPO DE ATENCIÓN
            if ($validated['tipo_atencion_ex'] === 'nuevo_tratamiento') {
                // CREAR UN NUEVO REGISTRO DE TRATAMIENTO
                $nuevoTratamiento = Tratamiento::create([
                    'id_paciente'         => $validated['id_paciente'],
                    'id_usuario'          => $id_usuario,
                    'id_clinica'          => $id_clinica,
                    'id_cat_tratamientos' => $request->id_cat_tratamiento_nuevo,
                    'diagnostico_inicial' => $request->diagnostico_nuevo, // Opcional
                    'precio_estimado'     => $request->precio_estimado_nuevo, // Opcional
                    'fecha_inicio'        => $fecha,
                    'estatus'             => 'curso',
                ]);
                $id_tratamiento_final = $nuevoTratamiento->id_tratamiento;

            } elseif ($validated['tipo_atencion_ex'] === 'seguimiento') {
                // USAR EL QUE YA EXISTE
                $id_tratamiento_final = $request->id_tratamiento_existente;

            } elseif ($validated['tipo_atencion_ex'] === 'servicio') {
                $id_servicio_final = $request->id_cat_servicio;
            }

                DB::table('citas')->insert([
                    'id_paciente'     => $validated['id_paciente'],
                    'id_usuario'      => $id_usuario,
                    'id_clinica'      => $id_clinica,
                    'id_cat_servicio' => $id_servicio_final,
                    'id_tratamiento'  => $id_tratamiento_final,
                    'fecha'           => $fecha,
                    'hora'            => $hora,
                    'motivo_consulta' => $validated['motivo_consulta'],
                    'duracion'        => $validated['duracion'],
                    'estatus_cita'    => 'programada',
                    'created_at'      => now(),
                ]);
            });

            //return redirect()->back()->with('success', 'Cita para paciente existente programada correctamente.');
            return redirect()->route('pacientes.index')
            ->with('success', 'Cita para paciente existente programada correctamente.');
        } catch (\Exception $e) {
            //return redirect()->back()->with('error', 'Error al programar cita: ' . $e->getMessage());
            return redirect()->route('pacientes.index')
            ->with('error', 'Error al programar cita: ' . $e->getMessage());
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
            'id_tratamiento'      => $t->id_tratamiento,
            'id_cat_tratamiento'  => $t->id_cat_tratamientos, // 🔹 Agregamos esto
            'nombre'              => $t->catalogoTratamiento->nombre ?? 'Tratamiento sin nombre'
        ];
    });

    return response()->json($data);
}

    public function getCitasOcupadas()
{
    $id_clinica = Auth::user()->id_clinica;

    // Traemos las citas incluyendo el nombre del paciente para que no diga solo "OCUPADO"
    $citas = DB::table('citas')
        ->join('paciente', 'citas.id_paciente', '=', 'paciente.id_paciente')
        ->select('citas.*', 'paciente.nombre as nombre_paciente')
        ->where('citas.id_clinica', $id_clinica)
        ->whereIn('citas.estatus_cita', ['programada', 'confirmada'])
        ->get();

    $eventos = [];

    // Paleta de colores profesionales (Azul, Verde, Amarillo, Morado, Naranja, Teal)
    $paleta = ['#2C7BE5', '#00A86B', '#F4B400', '#6F42C1', '#E5533D', '#17A2B8'];

    foreach ($citas as $cita) {
        $inicio = \Carbon\Carbon::parse($cita->fecha . ' ' . $cita->hora);
        $fin = (clone $inicio)->addMinutes($cita->duracion);

        // ASIGNACIÓN DE COLOR: 
        // Usamos el id_servicio para que todas las citas del mismo tipo de servicio tengan el mismo color
        // Si prefieres que cada cita tenga un color distinto, usa $cita->id_cita
        $colorIndex = $cita->id_cita % count($paleta);
        $colorElegido = $paleta[$colorIndex];

        $eventos[] = [
            'id'    => $cita->id_cita,
            'title' => $cita->nombre_paciente, // <--- CAMBIO: Ahora muestra el nombre del paciente
            'start' => $inicio->format('Y-m-d\TH:i:s'), 
            'end'   => $fin->format('Y-m-d\TH:i:s'),   
            'backgroundColor' => $colorElegido, 
            'borderColor' => $colorElegido,
            'textColor' => '#ffffff',
            'display' => 'block',
            // Pasamos datos extra por si quieres usarlos en JS
            'extendedProps' => [
                'duracion' => $cita->duracion
            ]
        ];
    }

    return response()->json($eventos);
}

public function validarCitaDuplicada(Request $request) {
// Si no llega el ID o la fecha, respondemos que no existe para no romper el JS
    if (!$request->id_paciente || !$request->fecha) {
        return response()->json(['existe' => false]);
    }

    $existe = DB::table('citas')
        ->where('id_paciente', $request->id_paciente)
        ->where('fecha', $request->fecha)
        ->whereIn('estatus_cita', ['programada', 'confirmada'])
        ->exists();

    return response()->json(['existe' => $existe]);
}
}