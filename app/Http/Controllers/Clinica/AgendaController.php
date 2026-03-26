<?php

namespace App\Http\Controllers\Clinica;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class AgendaController extends Controller
{
    public function index()
    {
        $citas = [
            (object)[
                'hora' => '09:00',
                'tipo' => 'servicio',
                'nombre_servicio' => 'Limpieza dental',
                'motivo' => 'Dolor leve',
                'paciente' => (object)[
                    'nombre' => 'Juan',
                    'apellido_paterno' => 'Pérez'
                ]
            ],

            (object)[
                'hora' => '11:00',
                'tipo' => 'tratamiento',
                'nombre_servicio' => 'Ortodoncia',
                'motivo' => 'Ajuste mensual',
                'paciente' => (object)[
                    'nombre' => 'María',
                    'apellido_paterno' => 'Gómez'
                ]
            ]
        ];

        return view('asistente.agenda.agenda', compact('citas'));
    }
}