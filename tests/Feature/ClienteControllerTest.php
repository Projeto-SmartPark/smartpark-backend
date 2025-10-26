<?php

test('GET /api/clientes lista todos os clientes', function () {
    $resposta = $this->getJson('/api/clientes');
    $resposta->assertStatus(200);
});

test('POST /api/usuarios cria cliente corretamente', function () {
    $dadosCliente = ['perfil' => 'C', 'nome' => 'Cliente', 'email' => 'cliente@teste.com', 'senha' => '123456'];
    $resposta = $this->postJson('/api/usuarios', $dadosCliente);
    $resposta->assertStatus(201);
});

test('POST /api/usuarios rejeita cliente com nome curto', function () {
    $dadosInvalidos = ['perfil' => 'C', 'nome' => 'AB', 'email' => 'cliente@teste.com', 'senha' => '123456'];
    $resposta = $this->postJson('/api/usuarios', $dadosInvalidos);
    $resposta->assertStatus(422)
        ->assertJsonValidationErrors(['nome']);
});

test('POST /api/usuarios rejeita cliente com senha curta', function () {
    $dadosInvalidos = ['perfil' => 'C', 'nome' => 'Cliente', 'email' => 'cliente@teste.com', 'senha' => '12345'];
    $resposta = $this->postJson('/api/usuarios', $dadosInvalidos);
    $resposta->assertStatus(422)
        ->assertJsonValidationErrors(['senha']);
});

test('POST /api/usuarios rejeita cliente com email inválido', function () {
    $dadosInvalidos = ['perfil' => 'C', 'nome' => 'Cliente', 'email' => 'email_invalido', 'senha' => '123456'];
    $resposta = $this->postJson('/api/usuarios', $dadosInvalidos);
    $resposta->assertStatus(422)
        ->assertJsonValidationErrors(['email']);
});

test('PUT /api/clientes/{id} retorna 404 se cliente não existe', function () {
    $resposta = $this->putJson('/api/clientes/999', ['nome' => 'Inexistente', 'email' => 'x@x.com', 'senha' => '123456']);
    $resposta->assertStatus(404);
});

test('DELETE /api/clientes/{id} retorna 404 se cliente não existe', function () {
    $resposta = $this->deleteJson('/api/clientes/999');
    $resposta->assertStatus(404);
});
