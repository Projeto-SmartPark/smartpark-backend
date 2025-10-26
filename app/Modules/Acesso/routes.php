<?php

use Illuminate\Support\Facades\Route;
use App\Modules\Acesso\AcessoController;

Route::prefix('acessos')->group(function () {
    Route::get('/', [AcessoController::class, 'index']);
    Route::post('/', [AcessoController::class, 'store']);
    Route::get('/{id}', [AcessoController::class, 'show']);
    Route::put('/{id}', [AcessoController::class, 'update']);
    Route::delete('/{id}', [AcessoController::class, 'destroy']);
});
