<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\AccesoMovil;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        // 1. Validar entrada
        $request->validate([
            'usuario_movil' => 'required|string',
            'password'      => 'required|string',
            'device_name'   => 'required|string', 
        ]);

        // 2. Buscar usuario con su paciente vinculado
        $acceso = AccesoMovil::with('paciente')
            ->where('usuario_movil', $request->usuario_movil)
            ->first();

        // 3. Validar credenciales
        if (!$acceso || !Hash::check($request->password, $acceso->password)) {
            return response()->json([
                'res' => false,
                'msg' => 'Usuario o contraseña incorrectos.'
            ], 401);
        }

        // 4. Validar si la cuenta no ha expirado
        if ($acceso->estatus === 'expirado') {
            return response()->json([
                'res' => false,
                'msg' => 'Tu acceso ha expirado. Por favor, contacta a la clínica.'
            ], 403);
        }

        // 5. Generar el Token (Sanctum lo guarda en la tabla personal_access_tokens)
        $token = $acceso->createToken($request->device_name)->plainTextToken;

        // 6. Si era 'temporal', lo pasamos a 'activo' al primer login exitoso
        if ($acceso->estatus === 'temporal') {
            $acceso->update(['estatus' => 'activo']);
        }

        // 7. Respuesta para la App
        return response()->json([
            'res'   => true,
            'token' => $token,
            'paciente' => [
                'id'       => $acceso->id_paciente,
                'nombre'   => $acceso->paciente->nombre . ' ' . $acceso->paciente->apellido_paterno,
                'estatus'  => $acceso->estatus
            ]
        ], 200);
    }

    public function logout(Request $request)
    {
        // Revoca el token que está usando el dispositivo actualmente
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'res' => true,
            'msg' => 'Sesión cerrada'
        ], 200);
    }
}