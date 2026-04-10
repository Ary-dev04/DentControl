<?php

namespace App\Http\Controllers\Clinica;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Paciente;
use App\Models\Tratamiento;
use App\Models\Cita;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        $id_clinica = $user->id_clinica;
        $hoy = Carbon::today();

        // 1. Métricas principales
        $citasHoyCount = Cita::where('id_clinica', $id_clinica)
            ->whereDate('fecha', $hoy)
            ->count();

        $totalPacientes = Paciente::where('id_clinica', $id_clinica)->count();

        // Contamos tratamientos con estatus 'en proceso' o similar si lo manejas, 
        // de lo contrario, el conteo general está bien.
        $tratamientosActivos = Tratamiento::where('id_clinica', $id_clinica)->count();

        // 2. Lógica de Alertas Dinámicas
        $alertas = collect();

        // Alerta A: Recordatorio de agenda
        if ($citasHoyCount > 0) {
            $alertas->push((object)[
                'mensaje' => "Hoy tienes $citasHoyCount cita(s) programada(s) en tu agenda.",
                'icono' => 'fa-calendar-check'
            ]);
        }

        // Alerta B: Expedientes incompletos (Pacientes sin teléfono o RFC)
        // Alerta B: Expedientes incompletos (Lógica Inteligente para Adultos y Menores)
$pacientesIncompletos = Paciente::where('id_clinica', $id_clinica)
    ->where(function($q) {
        // CASO 1: Es adulto (no tiene tutor) pero faltan sus datos
        $q->where(function($query) {
            $query->whereNull('nombre_tutor')
                  ->where(function($sub) {
                      $sub->whereNull('telefono')->orWhere('telefono', '');
                  });
        })
        // CASO 2: Es menor (tiene tutor) pero falta el teléfono del tutor
        ->orWhere(function($query) {
            $query->whereNotNull('nombre_tutor')
                  ->where(function($sub) {
                      $sub->whereNull('telefono_tutor')->orWhere('telefono_tutor', '');
                  });
        });
    })
    ->where('estatus', 'activo') // Solo pacientes que siguen viniendo
    ->count();

if ($pacientesIncompletos > 0) {
    $alertas->push((object)[
        'mensaje' => "Hay $pacientesIncompletos paciente(s) sin un teléfono de contacto (propio o de tutor).",
        'icono' => 'fa-user-pen'
    ]);
}

        // Alerta C: Cumpleaños del día (Fidelización)
        $cumpleaneros = Paciente::where('id_clinica', $id_clinica)
            ->whereMonth('fecha_nacimiento', $hoy->month)
            ->whereDay('fecha_nacimiento', $hoy->day)
            ->count();

        if ($cumpleaneros > 0) {
            $alertas->push((object)[
                'mensaje' => "¡Hoy es el cumpleaños de $cumpleaneros paciente(s)! No olvides felicitarlos.",
                'icono' => 'fa-cake-candles'
            ]);
        }

        // 3. Selección de vista según Rol
        // Esto permite que el Asistente y el Dentista compartan controlador pero vean diseños distintos
        $vista = ($user->rol === 'dentista') ? 'dentista.dashboard' : 'asistente.dashboard';

        return view($vista, compact(
            'totalPacientes', 
            'tratamientosActivos', 
            'citasHoyCount', 
            'alertas'
        ));
    }
}