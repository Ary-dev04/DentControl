<?php

namespace App\Http\Controllers\Clinica;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class DentistaAgendaController extends Controller
{
    public function index()
    {

        // Datos de ejemplo (después vendrán de la BD)

        $citas = [

            [
                'hora' => '09:00',
                'paciente' => 'Juan Pérez López',
                'tipo' => 'servicio',
                'nombre' => 'Limpieza dental',
                'motivo' => 'Dolor leve en muela',
                'estado' => 'pendiente'
            ],

            [
                'hora' => '11:00',
                'paciente' => 'María Gómez Ruiz',
                'tipo' => 'tratamiento',
                'nombre' => 'Ortodoncia',
                'motivo' => 'Ajuste mensual',
                'estado' => 'enproceso'
            ],

            [
                'hora' => '13:00',
                'paciente' => 'Carlos Martínez',
                'tipo' => 'tratamiento',
                'nombre' => 'Extracción',
                'motivo' => 'Extracción molar',
                'estado' => 'finalizado'
            ]

        ];

        return view('dentista.agenda.index', compact('citas'));

    }
}