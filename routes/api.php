<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthApiController;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;

Route::prefix('v1')->group(function () {

    Route::post('/login',[AuthApiController::class,'login']);

    Route::post('/login-paciente',[AuthApiController::class,'loginPaciente']);

});

Route::get('/cp/{codigo}', function ($codigo) {
    $resultados = DB::table('codigos_postales')
        ->where('codigo_postal', $codigo)
        ->get();

    if ($resultados->isEmpty()) {
        return response()->json([
            'success' => false,
            'message' => 'Código postal no encontrado'
        ], 404);
    }

    // Como un CP tiene varias colonias pero el mismo estado y municipio, formateamos la respuesta:
    return response()->json([
        'success' => true,
        'estado' => $resultados[0]->estado,
        'municipio' => $resultados[0]->municipio,
        'ciudad' => $resultados[0]->ciudad,
        'colonias' => $resultados->map(function ($item) {
            return $item->asentamiento;
        })
    ]);
});