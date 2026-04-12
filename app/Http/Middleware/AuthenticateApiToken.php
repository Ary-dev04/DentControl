<?php

namespace App\Http\Middleware;

use App\Models\AccesoMovil;
use App\Models\ApiToken;
use App\Models\Usuario;
use Closure;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthenticateApiToken
{
    public function handle(Request $request, Closure $next): mixed
    {
        $plainTextToken = $request->bearerToken();

        if (! $plainTextToken) {
            return $this->unauthorized('Debes enviar un token Bearer valido.');
        }

        $apiToken = ApiToken::with('tokenable')
            ->where('token_hash', hash('sha256', $plainTextToken))
            ->first();

        if (! $apiToken || ! $apiToken->tokenable) {
            return $this->unauthorized('El token proporcionado no es valido.');
        }

        if ($apiToken->expires_at && $apiToken->expires_at->isPast()) {
            $apiToken->delete();

            return $this->unauthorized('El token ha expirado. Inicia sesion nuevamente.');
        }

        $actor = $apiToken->tokenable;

        if ($actor instanceof Usuario) {
            if ($actor->estatus !== 'activo') {
                $apiToken->delete();

                return $this->forbidden('La cuenta del usuario ya no esta activa.');
            }

            if ($actor->clinica && $actor->clinica->estatus === 'baja') {
                $apiToken->delete();

                return $this->forbidden('La clinica asociada al usuario esta inactiva.');
            }
        }

        if ($actor instanceof AccesoMovil) {
            if ($actor->fecha_expiracion && $actor->fecha_expiracion->isPast()) {
                $actor->update(['estatus' => 'expirado']);
                $apiToken->delete();

                return $this->forbidden('El acceso movil expiro. Solicita nuevas credenciales.');
            }

            if ($actor->estatus === 'expirado') {
                $apiToken->delete();

                return $this->forbidden('El acceso movil ya no esta disponible.');
            }

            if (! $actor->paciente || $actor->paciente->estatus !== 'activo') {
                $apiToken->delete();

                return $this->forbidden('El paciente asociado ya no esta activo.');
            }

            if ($actor->paciente->clinica && $actor->paciente->clinica->estatus === 'baja') {
                $apiToken->delete();

                return $this->forbidden('La clinica asociada al acceso movil esta inactiva.');
            }
        }

        $apiToken->forceFill(['last_used_at' => now()])->save();

        $request->attributes->set('api_token', $apiToken);
        $request->attributes->set('api_actor', $actor);

        if ($actor instanceof AuthenticatableContract) {
            Auth::setUser($actor);
            $request->setUserResolver(fn () => $actor);
        }

        return $next($request);
    }

    protected function unauthorized(string $message): JsonResponse
    {
        return response()->json([
            'message' => $message,
        ], 401);
    }

    protected function forbidden(string $message): JsonResponse
    {
        return response()->json([
            'message' => $message,
        ], 403);
    }
}
