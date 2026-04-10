<?php

namespace App\Http\Controllers\Dentista;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Paciente;
use App\Models\Tratamiento;
use App\Models\Cita; // Asegúrate de tener este modelo
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class ReporteController extends Controller
{
    public function index()
{
    $id_clinica = Auth::user()->id_clinica;

    // KPI's Básicos
    $totalPacientes = Paciente::where('id_clinica', $id_clinica)->where('estatus', 'activo')->count();
    $citasHoy = Cita::where('id_clinica', $id_clinica)->whereDate('fecha', Carbon::today())->count();
    $citasFinalizadas = Cita::where('id_clinica', $id_clinica)->where('estatus_cita', 'finalizada')->count();
    $citasProgramadas = Cita::where('id_clinica', $id_clinica)->where('estatus_cita', 'programada')->count();
    
    $ingresosMes = Cita::where('id_clinica', $id_clinica)
        ->where('estatus_cita', 'finalizada')
        ->whereMonth('fecha', Carbon::now()->month)
        ->sum('monto_cobrado');

    $accesosMoviles = DB::table('paciente')
        ->join('acceso_movil', 'paciente.id_paciente', '=', 'acceso_movil.id_paciente')
        ->where('paciente.id_clinica', $id_clinica)
        ->count();

    // INGRESOS POR SERVICIO / TRATAMIENTO (UNIFICADO)
    $serviciosQuery = DB::table('citas')
        ->join('catalogo_servicios', 'citas.id_cat_servicio', '=', 'catalogo_servicios.id_cat_servicio')
        ->select('catalogo_servicios.nombre', DB::raw('count(citas.id_cita) as cantidad'), DB::raw('sum(citas.monto_cobrado) as ingresos'))
        ->where('citas.id_clinica', $id_clinica)->where('citas.estatus_cita', 'finalizada')
        ->groupBy('catalogo_servicios.nombre');

    $ingresosPorServicio = DB::table('citas')
        ->join('tratamiento', 'citas.id_tratamiento', '=', 'tratamiento.id_tratamiento')
        ->join('catalogo_tratamientos', 'tratamiento.id_cat_tratamientos', '=', 'catalogo_tratamientos.id_cat_tratamientos')
        ->select('catalogo_tratamientos.nombre', DB::raw('count(citas.id_cita) as cantidad'), DB::raw('sum(citas.monto_cobrado) as ingresos'))
        ->where('citas.id_clinica', $id_clinica)->where('citas.estatus_cita', 'finalizada')
        ->groupBy('catalogo_tratamientos.nombre')
        ->union($serviciosQuery)
        ->orderBy('ingresos', 'desc')
        ->get();

    // INGRESOS POR MES (Año actual completo)
    $ingresosPorMes = [];
    for ($i = 1; $i <= Carbon::now()->month; $i++) {
        $fecha = Carbon::create(null, $i, 1);
        $total = Cita::where('id_clinica', $id_clinica)
            ->where('estatus_cita', 'finalizada')
            ->whereMonth('fecha', $i)
            ->whereYear('fecha', Carbon::now()->year)
            ->sum('monto_cobrado');

        $ingresosPorMes[] = ['nombre' => ucfirst($fecha->translatedFormat('F')), 'total'  => $total];
    }

    return view('dentista.reportes.index', compact(
        'totalPacientes', 'citasHoy', 'citasFinalizadas', 'citasProgramadas', 
        'ingresosMes', 'accesosMoviles', 'ingresosPorServicio', 'ingresosPorMes'
    ));
}
}