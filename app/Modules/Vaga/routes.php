<?php

use Illuminate\Support\Facades\Route;
use App\Modules\Vaga\VagaController;

Route::prefix('vagas')->group(function () {
    Route::get('/', [VagaController::class, 'index']);
    Route::post('/', [VagaController::class, 'store']);
    Route::get('/{id}', [VagaController::class, 'show']);
    Route::put('/{id}', [VagaController::class, 'update']);
    Route::delete('/{id}', [VagaController::class, 'destroy']);
});
