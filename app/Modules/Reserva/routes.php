<?php

use App\Modules\Reserva\ReservaController;
use Illuminate\Support\Facades\Route;

Route::prefix('reservas')
    ->middleware('auth.microservico')
    ->group(function () {
        Route::get('/', [ReservaController::class, 'index']);
        Route::get('/cliente', [ReservaController::class, 'listarPorCliente']);
        Route::post('/verificar-disponibilidade', [ReservaController::class, 'verificarDisponibilidade']);
        Route::post('/', [ReservaController::class, 'store']);
        Route::get('/{id}', [ReservaController::class, 'show']);
        Route::put('/{id}', [ReservaController::class, 'update']);
        Route::put('/{id}/cancelar', [ReservaController::class, 'cancelar']);
        Route::delete('/{id}', [ReservaController::class, 'destroy']);
    });
