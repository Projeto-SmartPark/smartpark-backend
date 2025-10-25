<?php

use Illuminate\Support\Facades\Route;
use App\Modules\Endereco\EnderecoController;

Route::prefix('enderecos')->group(function () {
    Route::get('/', [EnderecoController::class, 'index']);
    Route::post('/', [EnderecoController::class, 'store']);
    Route::get('/{id}', [EnderecoController::class, 'show']);
    Route::put('/{id}', [EnderecoController::class, 'update']);
    Route::delete('/{id}', [EnderecoController::class, 'destroy']);
});
