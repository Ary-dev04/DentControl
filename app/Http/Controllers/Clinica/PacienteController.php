<?php

namespace App\Http\Controllers\Clinica;

use App\Http\Controllers\Controller;
use App\Models\Paciente;
use App\Models\CatalogoServicio;
use App\Models\CatalogoTratamiento;
use App\Models\Tratamiento;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Cita;
use Illuminate\Support\Facades\DB;

class PacienteController extends Controller
{
    public function index()
    {
        $id_clinica = Auth::user()->id_clinica;

        $pacientes = Paciente::where('id_clinica', $id_clinica)
            ->where('estatus', 'activo')
            ->get();

        // Cargamos catálogos para llenar los selectores de los modales
        $catServicios = CatalogoServicio::where('id_clinica', $id_clinica)->where('estatus', 'activo')->get();
        $catTratamientos = CatalogoTratamiento::where('id_clinica', $id_clinica)->where('estatus', 'activo')->get();

        return view('asistente.pacientes.index', compact('pacientes', 'catServicios', 'catTratamientos'));
    }

    public function obtenerDuracion(Request $request)
{
    $tipo = $request->query('tipo');
    $id = $request->query('id');

    if ($tipo === 'tratamiento') {
        $item = DB::table('catalogo_tratamientos')
                    ->where('id_cat_tratamientos', $id)
                    ->first();
        // Usamos el nombre exacto de tu tabla
        $duracion = $item ? $item->duracion_sugerido_sesion : 0;
    } else {
        $item = DB::table('catalogo_servicios')
                    ->where('id_cat_servicio', $id)
                    ->first();
        // Usamos el nombre exacto de tu tabla
        $duracion = $item ? $item->duracion : 0;
    }

    return response()->json(['duracion' => $duracion]);
}

   public function store(Request $request)
    {
        // 1. Validación exhaustiva basada en la migración de la tabla 'paciente'
        $validated = $request->validate([
            // Datos personales
            'nombre'           => 'required|string|max:50|regex:/^[a-zA-ZáéíóúÁÉÍÓÚñÑ ]+$/',
            'apellido_paterno' => 'required|string|max:50|regex:/^[a-zA-ZáéíóúÁÉÍÓÚñÑ ]+$/',
            'apellido_materno' => 'required|string|max:50|regex:/^[a-zA-ZáéíóúÁÉÍÓÚñÑ ]+$/',
            'fecha_nacimiento' => 'required|date|before:today',
            'sexo'             => 'required|in:hombre,mujer',
            'email'            => 'required|email|unique:paciente,email',
            'telefono'         => 'required|digits:10',
            'curp'             => 'required|string|max:18|unique:paciente,curp',
            'ocupacion'        => 'nullable|string|max:255',
            'peso'             => 'nullable|numeric|between:0,999.99',
            
            // Dirección
            'calle'            => 'required|string|max:255',
            'num_ext'          => 'required|alpha_num|max:10',
            'num_int'          => 'required|alpha_num|max:10',
            'colonia'          => 'required|string|max:100',
            'ciudad'           => 'required|string|max:100',
            'estado'           => 'required|string|max:100',
            'codigo_postal'    => 'required|digits:5',

            // Datos de la Cita y Atención
            'fecha_cita'       => 'required|date|after_or_equal:today',
            'duracion'         => 'required|integer|min:1|max:480', // Duración real enviada desde el form
            'motivo_consulta'  => 'nullable|string|max:255',
            'tipo_atencion'    => 'required|in:tratamiento,servicio',
        ]);

        $id_clinica = Auth::user()->id_clinica;
        $id_usuario = Auth::id();

        try {
            return DB::transaction(function () use ($request, $validated, $id_clinica, $id_usuario) {
                
                // 2. Crear el registro del Paciente
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

                // 3. Procesar Fecha y Hora (separar el string de FullCalendar)
                $dt = new \DateTime($validated['fecha_cita']);
                $fecha = $dt->format('Y-m-d');
                $hora  = $dt->format('H:i:s');

                $id_tratamiento_rel = null;
                $id_cat_servicio_rel = null;

                // 4. Lógica de Atención (Tratamiento o Servicio)
                if ($validated['tipo_atencion'] === 'tratamiento') {
                    // Si es tratamiento, creamos la entrada en la tabla 'tratamiento'
                    $nuevoTratamiento = Tratamiento::create([
                        'id_paciente'         => $paciente->id_paciente,
                        'id_usuario'          => $id_usuario,
                        'id_clinica'          => $id_clinica,
                        'id_cat_tratamientos' => $request->id_cat_tratamiento,
                        'fecha_inicio'        => $fecha,
                        'estatus'             => 'curso',
                    ]);
                    $id_tratamiento_rel = $nuevoTratamiento->id_tratamiento;
                } else {
                    // Si es servicio rápido, guardamos la referencia directa
                    $id_cat_servicio_rel = $request->id_cat_servicio;
                }

                // 5. Crear la Cita final
                DB::table('citas')->insert([
                    'id_paciente'     => $paciente->id_paciente,
                    'id_usuario'      => $id_usuario,
                    'id_clinica'      => $id_clinica,
                    'id_cat_servicio' => $id_cat_servicio_rel,
                    'id_tratamiento'  => $id_tratamiento_rel,
                    'fecha'           => $fecha,
                    'hora'            => $hora,
                    'motivo_consulta' => $validated['motivo_consulta'],
                    'duracion'        => $validated['duracion'], // Duración real de la cita
                    'estatus_cita'    => 'programada',
                    'created_at'      => now(),
                    'updated_at'      => now(),
                ]);

                return redirect()->back()->with('success', 'Paciente y cita registrados correctamente.');
            });
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Ocurrió un error al guardar: ' . $e->getMessage());
        }
    }

    public function storeCitaExistente(Request $request)
{
    $validated = $request->validate([
        'id_paciente'     => 'required|exists:paciente,id_paciente',
        'fecha_cita'      => 'required|date',
        'duracion'        => 'required|integer|min:1',
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
                // En paciente existente, el ID viene del select de tratamientos activos
                $id_tratamiento_rel = $request->id_tratamiento;
            } else {
                $id_cat_servicio_rel = $request->id_cat_servicio;
            }

            // Crear la Cita
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
                'updated_at'      => now(),
            ]);
        });

        return redirect()->back()->with('success', 'Cita programada exitosamente.');
    } catch (\Exception $e) {
        return redirect()->back()->with('error', 'Error: ' . $e->getMessage());
    }
}
}