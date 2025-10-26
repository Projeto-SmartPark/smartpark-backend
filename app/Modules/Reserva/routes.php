<?php

use Illuminate\Support\Facades\Route;
use App\Modules\Reserva\ReservaController;

Route::prefix('reservas')->group(function () {
    Route::get('/', [ReservaController::class, 'index']);
    Route::post('/', [ReservaController::class, 'store']);
    Route::get('/{id}', [ReservaController::class, 'show']);
    Route::put('/{id}', [ReservaController::class, 'update']);
    Route::delete('/{id}', [ReservaController::class, 'destroy']);
});
