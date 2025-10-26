<?php

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

// CRUD genérico de usuários
Route::prefix('usuarios')->group(function () {
    Route::get('/', [UsuariosController::class, 'index']);          // Listar todos
    Route::post('/', [UsuariosController::class, 'store']);         // Criar usuário
    Route::get('/{id}', [UsuariosController::class, 'show']);       // Mostrar usuário
    Route::put('/{id}', [UsuariosController::class, 'update']);     // Atualizar usuário
    Route::delete('/{id}', [UsuariosController::class, 'destroy']); // Remover usuário
});

// CRUD específico de clientes
Route::prefix('clientes')->group(function () {
    Route::get('/', [ClienteController::class, 'index']);           // Listar clientes
    Route::get('/{id}', [ClienteController::class, 'show']);        // Mostrar cliente
    Route::put('/{id}', [ClienteController::class, 'update']);      // Atualizar cliente
    Route::delete('/{id}', [ClienteController::class, 'destroy']);  // Remover cliente
});

// CRUD específico de gestores
Route::prefix('gestores')->group(function () {
    Route::get('/', [GestorController::class, 'index']);            // Listar gestores
    Route::get('/{id}', [GestorController::class, 'show']);         // Mostrar gestor
    Route::put('/{id}', [GestorController::class, 'update']);       // Atualizar gestor
    Route::delete('/{id}', [GestorController::class, 'destroy']);   // Remover gestor
});
