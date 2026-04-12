<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Usuario;
use App\Services\ApiTokenService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AuthApiController extends Controller
{
    public function __construct(private readonly ApiTokenService $apiTokenService)
    {
    }

    public function login(Request $request): JsonResponse
    {
        $credentials = $request->validate([
            'nom_usuario' => 'required|string',
            'password' => 'required|string',
            'device_name' => 'nullable|string|max:100',
        ]);

        $user = Usuario::with('clinica')
            ->where('nom_usuario', $credentials['nom_usuario'])
            ->first();

        if (! $user || ! Hash::check($credentials['password'], $user->password)) {
            return response()->json([
                'message' => 'Las credenciales no son correctas.',
            ], 422);
        }

        if ($user->estatus !== 'activo') {
            return response()->json([
                'message' => 'La cuenta del usuario esta inactiva.',
            ], 403);
        }

        if ($user->clinica && $user->clinica->estatus === 'baja') {
            return response()->json([
                'message' => 'La clinica asociada al usuario esta inactiva.',
            ], 403);
        }

        $tokenResult = $this->apiTokenService->createToken(
            $user,
            $credentials['device_name'] ?? 'saas-client',
            now()->addDays((int) config('api_tokens.user_ttl_days', 30)),
        );

        return response()->json([
            'message' => 'Inicio de sesion correcto.',
            'data' => [
                'token' => $tokenResult['plain_text_token'],
                'token_type' => 'Bearer',
                'expires_at' => $tokenResult['token']->expires_at?->toIso8601String(),
                'user' => $this->serializeUser($user),
            ],
        ]);
    }

    public function me(Request $request): JsonResponse
    {
        /** @var \App\Models\Usuario $user */
        $user = $request->attributes->get('api_actor');
        $user->loadMissing('clinica');

        return response()->json([
            'data' => $this->serializeUser($user),
        ]);
    }

    public function logout(Request $request): JsonResponse
    {
        $token = $request->attributes->get('api_token');

        if ($token) {
            $token->delete();
        }

        return response()->json([
            'message' => 'Sesion cerrada correctamente.',
        ]);
    }

    protected function serializeUser(Usuario $user): array
    {
        return [
            'id_usuario' => $user->id_usuario,
            'nombre' => trim($user->nombre.' '.$user->apellido_paterno.' '.$user->apellido_materno),
            'nom_usuario' => $user->nom_usuario,
            'rol' => $user->rol,
            'email' => $user->email,
            'telefono' => $user->telefono,
            'estatus' => $user->estatus,
            'clinica' => $user->clinica ? [
                'id_clinica' => $user->clinica->id_clinica,
                'nombre' => $user->clinica->nombre,
                'estatus' => $user->clinica->estatus,
                'codigo_postal' => $user->clinica->codigo_postal,
            ] : null,
        ];
    }
}
