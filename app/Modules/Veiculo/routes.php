<?php

use App\Modules\Veiculo\VeiculoController;
use Illuminate\Support\Facades\Route;

Route::prefix('veiculos')
    ->middleware('auth.microservico')
    ->group(function () {
        Route::get('/', [VeiculoController::class, 'index']);
        Route::post('/', [VeiculoController::class, 'store']);
        Route::get('/{id}', [VeiculoController::class, 'show']);
        Route::put('/{id}', [VeiculoController::class, 'update']);
        Route::delete('/{id}', [VeiculoController::class, 'destroy']);
    });
