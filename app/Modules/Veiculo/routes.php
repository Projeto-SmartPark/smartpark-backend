<?php

use Illuminate\Support\Facades\Route;
use App\Modules\Veiculo\VeiculoController;

Route::prefix('veiculos')->group(function () {
    Route::get('/', [VeiculoController::class, 'index']);
    Route::post('/', [VeiculoController::class, 'store']);
    Route::get('/{id}', [VeiculoController::class, 'show']);
    Route::put('/{id}', [VeiculoController::class, 'update']);
    Route::delete('/{id}', [VeiculoController::class, 'destroy']);
});
