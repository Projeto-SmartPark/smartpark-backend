<?php

use App\Modules\Acesso\AcessoController;
use Illuminate\Support\Facades\Route;

Route::prefix('acessos')->group(function () {
    Route::get('/', [AcessoController::class, 'index']);
    Route::post('/', [AcessoController::class, 'store']);
    Route::get('/{id}', [AcessoController::class, 'show']);
    Route::put('/{id}', [AcessoController::class, 'update']);
    Route::delete('/{id}', [AcessoController::class, 'destroy']);
});
