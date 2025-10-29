<?php

use App\Modules\Usuarios\Controllers\AuthController;
use App\Modules\Usuarios\Controllers\ClienteController;
use App\Modules\Usuarios\Controllers\GestorController;
use App\Modules\Usuarios\Controllers\UsuariosController;
use Illuminate\Support\Facades\Route;

/**
 * Rotas do Módulo de Usuários
 *
 * Todas as rotas deste arquivo já estão prefixadas com /api
 * devido à configuração do RouteServiceProvider
 */

// ============================
// Rotas protegidas (com token)
// ============================

// Usuários
Route::prefix('usuarios')->group(function () {
    Route::get('/', [UsuariosController::class, 'index']);
    Route::post('/', [UsuariosController::class, 'store']);
    Route::get('/{id}', [UsuariosController::class, 'show']);
    Route::put('/{id}', [UsuariosController::class, 'update']);
    Route::delete('/{id}', [UsuariosController::class, 'destroy']);
});

// Clientes
Route::prefix('clientes')->group(function () {
    Route::get('/', [ClienteController::class, 'index']);
    Route::post('/', [ClienteController::class, 'store']);
    Route::get('/{id}', [ClienteController::class, 'show']);
    Route::put('/{id}', [ClienteController::class, 'update']);
    Route::delete('/{id}', [ClienteController::class, 'destroy']);
});

// Gestores
Route::prefix('gestores')->group(function () {
    Route::get('/', [GestorController::class, 'index']);
    Route::post('/', [GestorController::class, 'store']);
    Route::get('/{id}', [GestorController::class, 'show']);
    Route::put('/{id}', [GestorController::class, 'update']);
    Route::delete('/{id}', [GestorController::class, 'destroy']);
});
