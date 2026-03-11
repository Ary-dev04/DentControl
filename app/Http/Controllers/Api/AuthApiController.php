<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\AccesoMovil;
use Illuminate\Support\Facades\Hash;

class AuthApiController extends Controller
{

    // Login del sistema
    public function login(Request $request)
    {

        $credentials = $request->only('email','password');

        if(!Auth::attempt($credentials)){
            return response()->json([
                'status'=>false,
                'message'=>'Credenciales incorrectas'
            ],401);
        }

        $user = Auth::user();

        return response()->json([
            'status'=>true,
            'user'=>$user
        ]);
    }


    // Login de paciente móvil
    public function loginPaciente(Request $request)
    {

        $acceso = AccesoMovil::where('usuario_movil',$request->usuario)->first();

        if(!$acceso){
            return response()->json([
                'status'=>false,
                'message'=>'Usuario no encontrado'
            ]);
        }

        if(!Hash::check($request->password,$acceso->password)){
            return response()->json([
                'status'=>false,
                'message'=>'Contraseña incorrecta'
            ]);
        }

        return response()->json([
            'status'=>true,
            'paciente'=>$acceso->paciente
        ]);
    }

}