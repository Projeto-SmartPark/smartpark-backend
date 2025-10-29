<?php

use App\Modules\Telefone\TelefoneController;
use Illuminate\Support\Facades\Route;

Route::prefix('telefones')
    ->middleware('auth.microservico')
    ->group(function () {
        Route::get('/', [TelefoneController::class, 'index']);
        Route::post('/', [TelefoneController::class, 'store']);
        Route::get('/{id}', [TelefoneController::class, 'show']);
        Route::put('/{id}', [TelefoneController::class, 'update']);
        Route::delete('/{id}', [TelefoneController::class, 'destroy']);
    });
