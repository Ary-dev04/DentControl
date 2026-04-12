<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\V1\AuthApiController;
use App\Http\Controllers\Api\V1\CodigoPostalController;
use App\Http\Controllers\Api\V1\MobileAuthController;
use App\Http\Controllers\Api\V1\MobilePacienteController;

Route::post('/login-movil', [AuthController::class, 'login']);

Route::middleware('auth:sanctum')->group(function () {
    Route::get('/perfil', function (Request $request) {
        return response()->json([
            'res' => true,
            'datos' => $request->user()->load('paciente')
        ]);
    });

    Route::post('/logout-movil', [AuthController::class, 'logout']);
});

Route::prefix('v1')->group(function () {
    
    // Grupo Auth
    Route::prefix('auth')->group(function () {
        Route::post('/login', [AuthApiController::class, 'login']);

        Route::middleware(['api.token', 'api.actor:usuario'])->group(function () {
            Route::get('/me', [AuthApiController::class, 'me']);
            Route::post('/logout', [AuthApiController::class, 'logout']);
        });
    });

    // Grupo Mobile
    Route::prefix('mobile')->group(function () {
        Route::post('/auth/login', [MobileAuthController::class, 'login']);

        Route::middleware(['api.token', 'api.actor:acceso_movil'])->group(function () {
            Route::get('/auth/me',                [MobileAuthController::class, 'me']);
            Route::post('/auth/logout',           [MobileAuthController::class, 'logout']);
            Route::post('/auth/cambiar-password', [MobileAuthController::class, 'cambiarPassword']);
            
            // Ruta para guardar el token de Firebase
            Route::post('/auth/fcm-token',        [MobilePacienteController::class, 'guardarFcmToken']);
            
            Route::get('/paciente/tratamientos',  [MobilePacienteController::class, 'tratamientos']);
            Route::get('/paciente/citas',         [MobilePacienteController::class, 'citas']);
        });
    });

    // Grupo Catalogos
    Route::prefix('catalogos')->group(function () {
        Route::get('/codigos-postales', [CodigoPostalController::class, 'index']);
        Route::get('/codigos-postales/{codigoPostal}', [CodigoPostalController::class, 'show']);
    });

});