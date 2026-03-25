<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
// 1. Importa tu controlador
use App\Http\Controllers\Api\AuthController;

// 2. Ruta pública para loguearse
Route::post('/login-movil', [AuthController::class, 'login']);

// 3. Rutas protegidas (Solo accesibles con el Token de Postman)
Route::middleware('auth:sanctum')->group(function () {
    
    // Ruta para obtener los datos del paciente logueado
    Route::get('/perfil', function (Request $request) {
        return response()->json([
            'res' => true,
            'datos' => $request->user()->load('paciente') 
        ]);
    });

    // Ruta para cerrar sesión
    Route::post('/logout-movil', [AuthController::class, 'logout']);
    
});