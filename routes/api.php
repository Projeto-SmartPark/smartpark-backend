<?php

use Illuminate\Support\Facades\Route;
use App\Modules\Usuarios\UsuariosController;
use App\Modules\Usuarios\ClienteController;
use App\Modules\Usuarios\GestorController;

Route::prefix('usuarios')->group(function () {
    // CRUD genérico de usuários
    Route::get('/', [UsuariosController::class, 'index']);      // Listar todos
    Route::post('/', [UsuariosController::class, 'store']);     // Criar usuário
    Route::get('/{id}', [UsuariosController::class, 'show']);   // Mostrar usuário
    Route::delete('/{id}', [UsuariosController::class, 'destroy']); // Remover usuário
});

// CRUD específico de clientes
Route::prefix('clientes')->group(function () {
    Route::get('/', [ClienteController::class, 'index']);      // Listar clientes
    Route::get('/{id}', [ClienteController::class, 'show']);   // Mostrar cliente
    Route::put('/{id}', [ClienteController::class, 'update']); // Atualizar cliente
    Route::delete('/{id}', [ClienteController::class, 'destroy']); // Remover cliente
});

// CRUD específico de gestores
Route::prefix('gestores')->group(function () {
    Route::get('/', [GestorController::class, 'index']);       // Listar gestores
    Route::get('/{id}', [GestorController::class, 'show']);    // Mostrar gestor
    Route::put('/{id}', [GestorController::class, 'update']);  // Atualizar gestor
    Route::delete('/{id}', [GestorController::class, 'destroy']); // Remover gestor
});
