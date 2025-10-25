<?php

use Illuminate\Support\Facades\Route;
use App\Modules\Estacionamento\EstacionamentoController;

Route::prefix('estacionamentos')->group(function () {
    Route::get('/', [EstacionamentoController::class, 'index']);
    Route::post('/', [EstacionamentoController::class, 'store']);
    Route::get('/{id}', [EstacionamentoController::class, 'show']);
    Route::put('/{id}', [EstacionamentoController::class, 'update']);
    Route::delete('/{id}', [EstacionamentoController::class, 'destroy']);
});
