<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Clinica;
use App\Models\Usuario;
use App\Models\Paciente;
use Carbon\Carbon; // Importante para el manejo de fechas
use Illuminate\Support\Facades\DB;

class AdminController extends Controller
{
    public function index()
    {
        // 1. KPIs principales
        $totalClinicasActivas = Clinica::where('estatus', 'activo')->count();
        
        // Excluimos superadmin para ver usuarios reales del SaaS
        $totalUsuariosActivos = Usuario::where('estatus', 'activo')
            ->where('rol', '!=', 'superadmin')
            ->count();
            
        $totalPacientes = Paciente::count();

        // --- LÓGICA DE SUPER ALERTA (MEZCLA DE 3) ---
    
        // 1. Clínicas con estatus 'baja'
        $alertasBaja = Clinica::where('estatus', 'baja')->count();

        // 2. Clínicas activas sin RFC registrado (Perfil incompleto)
        $alertasRFC = Clinica::where('estatus', 'activo')
            ->where(function($q) {
                $q->whereNull('rfc')->orWhere('rfc', '');
            })->count();

        // 3. Clínicas activas que no han registrado citas en los últimos 7 días (Riesgo de abandono)
        $haceUnaSemana = Carbon::now()->subDays(7);
        $alertasInactividad = Clinica::where('estatus', 'activo')
            ->whereDoesntHave('citas', function($query) use ($haceUnaSemana) {
                $query->where('fecha', '>=', $haceUnaSemana); // Usamos 'fecha' que es tu columna en la tabla citas
            })->count();

        // Sumatoria total de alertas
        $totalAlertas = $alertasBaja + $alertasRFC + $alertasInactividad;
        
        // Últimas 5 clínicas para la tabla del dashboard
        $ultimasClinicas = Clinica::orderBy('created_at', 'desc')->take(5)->get();

        return view('admin.dashboard', compact(
            'totalClinicasActivas', 
            'totalUsuariosActivos', 
            'totalPacientes', 
            'totalAlertas',
            'ultimasClinicas'
        ));
    }
}