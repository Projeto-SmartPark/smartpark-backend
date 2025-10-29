<?php

use App\Modules\Endereco\EnderecoController;
use Illuminate\Support\Facades\Route;

Route::prefix('enderecos')
    ->middleware('auth.microservico')
    ->group(function () {
        Route::get('/', [EnderecoController::class, 'index']);
        Route::post('/', [EnderecoController::class, 'store']);
        Route::get('/{id}', [EnderecoController::class, 'show']);
        Route::put('/{id}', [EnderecoController::class, 'update']);
        Route::delete('/{id}', [EnderecoController::class, 'destroy']);
    });
