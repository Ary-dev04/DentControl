<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Clinica;
use App\Models\Usuario;
use App\Models\Tratamiento;
use Illuminate\Support\Facades\DB;

class ReporteSaaSController extends Controller
{
    public function index()
    {
        // 1. KPIs Globales
        // Contamos clínicas activas
        $totalClinicas = Clinica::where('estatus', 'activo')->count();
        
        // Contamos todos los usuarios (excepto quizás los superadmin si quieres ser estricto)
        //$totalUsuarios = DB::table('usuario')->where('estatus', 'activo')->count();
        // Contamos usuarios activos que NO sean superadmin
        $totalUsuarios = DB::table('usuario')
            ->where('estatus', 'activo')
            ->where('rol', '!=', 'superadmin') // Excluimos al administrador global
            ->count();
        
        // Contamos tratamientos totales en el sistema
        $totalTratamientos = Tratamiento::count();
        
        // Cálculo del promedio
        $promedioTratamientos = $totalClinicas > 0 
            ? round($totalTratamientos / $totalClinicas, 1) 
            : 0;

        // 2. Análisis por Clínica (Tabla dinámica)
        // Usamos Query Builder para ser precisos con los nombres de tus tablas
        $clinicasDetalle = Clinica::select('clinica.id_clinica', 'clinica.nombre')
            ->where('clinica.estatus', 'activo')
            ->get()
            ->map(function ($clinica) {
                // Contar usuarios de esta clínica
                $clinica->usuarios_count = DB::table('usuario')
                    ->where('id_clinica', $clinica->id_clinica)
                    ->where('estatus', 'activo')
                    ->count();

                // Contar tratamientos de esta clínica
                $clinica->tratamientos_count = Tratamiento::where('id_clinica', $clinica->id_clinica)->count();

                return $clinica;
            })->sortByDesc('tratamientos_count');

        return view('admin.reporte-saas.index', compact(
            'totalClinicas',
            'totalUsuarios',
            'totalTratamientos',
            'promedioTratamientos',
            'clinicasDetalle'
        ));
    }
}