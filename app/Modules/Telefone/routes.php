<?php

use App\Modules\Telefone\TelefoneController;
use Illuminate\Support\Facades\Route;

Route::get('/telefones', [TelefoneController::class, 'index']);
Route::get('/telefones/{id}', [TelefoneController::class, 'show']);
Route::put('/telefones/{id}', [TelefoneController::class, 'update']);
Route::delete('/telefones/{id}', [TelefoneController::class, 'destroy']);
