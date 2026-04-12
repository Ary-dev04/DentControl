<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Cita;
use App\Models\Tratamiento;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class MobilePacienteController extends Controller
{
    public function tratamientos(Request $request): JsonResponse
    {
        try {
            $access     = $request->attributes->get('api_actor');
            $idPaciente = $access->paciente->id_paciente;

            $todos = Tratamiento::with(['catalogoTratamiento', 'notasEvolucion'])
                ->where('id_paciente', $idPaciente)
                ->orderBy('fecha_inicio', 'desc')
                ->get();

            $actual     = $todos->firstWhere('estatus', 'curso');
            $anteriores = $todos->filter(fn($t) => $t->estatus !== 'curso')->values();

            $serialize = function (Tratamiento $t): array {
                $pagado   = Cita::where('id_tratamiento', $t->id_tratamiento)
                                ->whereNotNull('monto_cobrado')
                                ->sum('monto_cobrado');
                $costo    = (float)($t->precio_final ?? $t->precio_estimado ?? 0);
                $restante = max(0, $costo - (float)$pagado);

                // Notas ordenadas de más reciente a más antigua
                $notas = $t->notasEvolucion
                    ->sortByDesc(fn($n) => $n->fecha . ' ' . ($n->hora ?? ''))
                    ->map(fn($n) => [
                        'fecha'        => $n->fecha,
                        'hora'         => $n->hora ?? '',
                        'nota_texto'   => $n->nota_texto ?? '',
                        'indicaciones' => $n->indicaciones ?? null,
                    ])->values();

                return [
                    'id_tratamiento'      => $t->id_tratamiento,
                    'nombre'              => $t->catalogoTratamiento?->nombre ?? 'Sin nombre',
                    'diagnostico_inicial' => $t->diagnostico_inicial ?? '',
                    'precio_estimado'     => (float)($t->precio_estimado ?? 0),
                    'precio_final'        => (float)($t->precio_final ?? 0),
                    'monto_pagado'        => (float)$pagado,
                    'restante'            => $restante,
                    'fecha_inicio'        => $t->fecha_inicio,
                    'fecha_fin'           => $t->fecha_fin,
                    'estatus'             => $t->estatus,
                    'notas'               => $notas,
                ];
            };

            return response()->json([
                'data' => [
                    'actual'     => $actual ? $serialize($actual) : null,
                    'anteriores' => $anteriores->map($serialize)->values(),
                ],
            ]);

        } catch (\Throwable $e) {
            Log::error('tratamientos: ' . $e->getMessage());
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }

    public function citas(Request $request): JsonResponse
    {
        try {
            $access     = $request->attributes->get('api_actor');
            $idPaciente = $access->paciente->id_paciente;

            $citas = Cita::with(['servicio', 'tratamiento.catalogoTratamiento'])
                ->where('id_paciente', $idPaciente)
                ->orderBy('fecha', 'desc')
                ->orderBy('hora',  'desc')
                ->get();

            $hoy = Carbon::today();

            $proxima = $citas
                ->where('estatus_cita', 'programada')
                ->filter(fn($c) => Carbon::parse($c->fecha)->gte($hoy))
                ->sortBy('fecha')
                ->first();

            $historial = $citas->filter(fn($c) =>
                $c->estatus_cita !== 'programada' ||
                Carbon::parse($c->fecha)->lt($hoy)
            )->values();

            $serialize = function (Cita $c): array {
                return [
                    'id_cita'         => $c->id_cita,
                    'fecha'           => $c->fecha,
                    'hora'            => $c->hora,
                    'servicio'        => $c->servicio?->nombre ?? null,
                    'tratamiento'     => $c->tratamiento?->catalogoTratamiento?->nombre ?? null,
                    'motivo_consulta' => $c->motivo_consulta ?? null,
                    'estatus_cita'    => $c->estatus_cita,
                ];
            };

            return response()->json([
                'data' => [
                    'proxima'   => $proxima ? $serialize($proxima) : null,
                    'historial' => $historial->map($serialize)->values(),
                ],
            ]);

        } catch (\Throwable $e) {
            Log::error('citas: ' . $e->getMessage());
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }

    // NUEVA FUNCIÓN PARA GUARDAR EL TOKEN DE FIREBASE
    public function guardarFcmToken(Request $request): JsonResponse
    {
        try {
            // 1. Obtenemos al usuario que hizo la petición
            $access = $request->attributes->get('api_actor');

            // 2. Validamos que Android sí nos haya mandado el dato
            $request->validate([
                'fcm_token' => 'required|string'
            ]);

            // 3. Guardamos el token en la base de datos
            $access->fcm_token = $request->fcm_token;
            $access->save();

            // 4. Le avisamos a Android que todo salió bien
            return response()->json([
                'status' => true,
                'message' => 'FCM Token guardado correctamente'
            ]);

        } catch (\Throwable $e) {
            Log::error('fcm-token: ' . $e->getMessage());
            return response()->json(['message' => 'Error al guardar token'], 500);
        }
    }
}