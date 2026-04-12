<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\AccesoMovil;
use App\Services\ApiTokenService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class MobileAuthController extends Controller
{
    public function __construct(private readonly ApiTokenService $apiTokenService)
    {
    }

    public function login(Request $request): JsonResponse
    {
        $credentials = $request->validate([
            'usuario_movil' => 'required|string',
            'password' => 'required|string',
            'device_name' => 'nullable|string|max:100',
        ]);

        $access = AccesoMovil::with(['paciente.clinica'])
            ->where('usuario_movil', $credentials['usuario_movil'])
            ->first();

        if (! $access || ! Hash::check($credentials['password'], $access->password)) {
            return response()->json([
                'message' => 'Las credenciales moviles no son correctas.',
            ], 422);
        }

        if ($access->fecha_expiracion && $access->fecha_expiracion->isPast()) {
            $access->update(['estatus' => 'expirado']);

            return response()->json([
                'message' => 'El acceso movil expiro. Solicita nuevas credenciales.',
            ], 403);
        }

        if ($access->estatus === 'expirado') {
            return response()->json([
                'message' => 'El acceso movil esta expirado.',
            ], 403);
        }

        if (! $access->paciente || $access->paciente->estatus !== 'activo') {
            return response()->json([
                'message' => 'El paciente asociado no esta activo.',
            ], 403);
        }

        if ($access->paciente->clinica && $access->paciente->clinica->estatus === 'baja') {
            return response()->json([
                'message' => 'La clinica asociada al paciente esta inactiva.',
            ], 403);
        }

        $tokenResult = $this->apiTokenService->createToken(
            $access,
            $credentials['device_name'] ?? 'mobile-app',
            now()->addDays((int) config('api_tokens.mobile_ttl_days', 90)),
        );

        return response()->json([
            'message' => 'Inicio de sesion movil correcto.',
            'data' => [
                'token' => $tokenResult['plain_text_token'],
                'token_type' => 'Bearer',
                'expires_at' => $tokenResult['token']->expires_at?->toIso8601String(),
                'access' => $this->serializeAccess($access),
            ],
        ]);
    }

    public function me(Request $request): JsonResponse
    {
        /** @var \App\Models\AccesoMovil $access */
        $access = $request->attributes->get('api_actor');
        $access->loadMissing(['paciente.clinica']);

        return response()->json([
            'data' => $this->serializeAccess($access),
        ]);
    }

    public function logout(Request $request): JsonResponse
    {
        $token = $request->attributes->get('api_token');

        if ($token) {
            $token->delete();
        }

        return response()->json([
            'message' => 'Sesion movil cerrada correctamente.',
        ]);
    }

    public function cambiarPassword(Request $request): JsonResponse
    {
        $request->validate([
            'password'              => 'required|string|min:8|confirmed',
            'password_confirmation' => 'required|string',
        ]);

    /** @var \App\Models\AccesoMovil $access */
    $access = $request->attributes->get('api_actor');

    $access->update([
        'password' => \Illuminate\Support\Facades\Hash::make($request->password),
        'estatus'  => 'activo',
    ]);

    return response()->json([
        'message' => 'Contraseña actualizada correctamente.',
    ]);
}

    protected function serializeAccess(AccesoMovil $access): array
    {
        $paciente = $access->paciente;

        return [
            'id_acceso' => $access->id_acceso,
            'usuario_movil' => $access->usuario_movil,
            'estatus' => $access->estatus,
            'fecha_expiracion' => $access->fecha_expiracion?->toIso8601String(),
            'paciente' => $paciente ? [
                'id_paciente' => $paciente->id_paciente,
                'nombre' => trim($paciente->nombre.' '.$paciente->apellido_paterno.' '.$paciente->apellido_materno),
                'email' => $paciente->email,
                'telefono' => $paciente->telefono,
                'codigo_postal' => $paciente->codigo_postal,
                'colonia' => $paciente->colonia,
                'ciudad' => $paciente->ciudad,
                'estado' => $paciente->estado,
                'clinica' => $paciente->clinica ? [
                    'id_clinica' => $paciente->clinica->id_clinica,
                    'nombre' => $paciente->clinica->nombre,
                    'telefono' => $paciente->clinica->telefono,
                ] : null,
            ] : null,
        ];
    }
}