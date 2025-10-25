<?php

use Illuminate\Support\Facades\Route;
use App\Modules\Telefone\TelefoneController;

Route::get('/telefones', [TelefoneController::class, 'index']);
Route::get('/telefones/{id}', [TelefoneController::class, 'show']);
Route::put('/telefones/{id}', [TelefoneController::class, 'update']);
Route::delete('/telefones/{id}', [TelefoneController::class, 'destroy']);
