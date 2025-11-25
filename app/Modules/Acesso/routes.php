<?php

use App\Modules\Acesso\AcessoController;
use Illuminate\Support\Facades\Route;

Route::prefix('acessos')
    ->middleware('auth.microservico')
    ->group(function () {
        Route::get('/', [AcessoController::class, 'index']);
        Route::get('/cliente', [AcessoController::class, 'acessosCliente']);
        Route::post('/', [AcessoController::class, 'store']);
        Route::get('/{id}', [AcessoController::class, 'show']);
        Route::put('/{id}', [AcessoController::class, 'update']);
        Route::delete('/{id}', [AcessoController::class, 'destroy']);
    });
