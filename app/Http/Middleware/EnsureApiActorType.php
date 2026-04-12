<?php

namespace App\Http\Middleware;

use App\Models\AccesoMovil;
use App\Models\Usuario;
use Closure;
use Illuminate\Http\Request;

class EnsureApiActorType
{
    public function handle(Request $request, Closure $next, string $type): mixed
    {
        $actor = $request->attributes->get('api_actor');

        $isValid = match ($type) {
            'usuario' => $actor instanceof Usuario,
            'acceso_movil' => $actor instanceof AccesoMovil,
            default => false,
        };

        if (! $isValid) {
            return response()->json([
                'message' => 'El token no tiene permisos para acceder a este recurso.',
            ], 403);
        }

        return $next($request);
    }
}
