<?php

use App\Modules\Tarifa\TarifaController;
use Illuminate\Support\Facades\Route;

Route::prefix('tarifas')
    ->middleware('auth.microservico')
    ->group(function () {
        Route::get('/', [TarifaController::class, 'index']);
        Route::post('/', [TarifaController::class, 'store']);
        Route::get('/{id}', [TarifaController::class, 'show']);
        Route::put('/{id}', [TarifaController::class, 'update']);
        Route::delete('/{id}', [TarifaController::class, 'destroy']);
    });
