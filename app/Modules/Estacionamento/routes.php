<?php

use App\Modules\Estacionamento\EstacionamentoController;
use Illuminate\Support\Facades\Route;

Route::prefix('estacionamentos')
    ->middleware('auth.microservico')
    ->group(function () {
        Route::get('/', [EstacionamentoController::class, 'index']);
        Route::post('/', [EstacionamentoController::class, 'store']);
        Route::get('/{id}', [EstacionamentoController::class, 'show']);
        Route::put('/{id}', [EstacionamentoController::class, 'update']);
        Route::delete('/{id}', [EstacionamentoController::class, 'destroy']);
    });
