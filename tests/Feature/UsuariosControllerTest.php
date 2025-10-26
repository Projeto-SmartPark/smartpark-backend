<?php

test('GET /api/usuarios retorna lista de clientes e gestores', function () {
    $resposta = $this->getJson('/api/usuarios');
    $resposta->assertStatus(200)
        ->assertJsonStructure(['clientes', 'gestores']);
});

test('POST /api/usuarios cria cliente com sucesso', function () {
    $dadosCliente = [
        'perfil' => 'C',
        'nome' => 'Novo Cliente',
        'email' => 'cliente@teste.com',
        'senha' => '123456',
    ];

    $resposta = $this->postJson('/api/usuarios', $dadosCliente);
    $resposta->assertStatus(201)
        ->assertJsonPath('message', 'Usuário criado com sucesso.');
});

test('POST /api/usuarios cria gestor com sucesso', function () {
    $dadosGestor = [
        'perfil' => 'G',
        'nome' => 'Novo Gestor',
        'email' => 'gestor@empresa.com',
        'senha' => '123456',
        'cnpj' => '12345678000190',
    ];

    $resposta = $this->postJson('/api/usuarios', $dadosGestor);
    $resposta->assertStatus(201)
        ->assertJsonPath('message', 'Usuário criado com sucesso.');
});

test('POST /api/usuarios rejeita cliente com nome curto', function () {
    $dadosInvalidos = [
        'perfil' => 'C',
        'nome' => 'AB',
        'email' => 'cliente@teste.com',
        'senha' => '123456',
    ];

    $resposta = $this->postJson('/api/usuarios', $dadosInvalidos);
    $resposta->assertStatus(422)
        ->assertJsonValidationErrors(['nome']);
});

test('POST /api/usuarios rejeita cliente com senha curta', function () {
    $dadosInvalidos = [
        'perfil' => 'C',
        'nome' => 'Cliente',
        'email' => 'cliente@teste.com',
        'senha' => '12345',
    ];

    $resposta = $this->postJson('/api/usuarios', $dadosInvalidos);
    $resposta->assertStatus(422)
        ->assertJsonValidationErrors(['senha']);
});

test('POST /api/usuarios rejeita cliente com email inválido', function () {
    $dadosInvalidos = [
        'perfil' => 'C',
        'nome' => 'Cliente',
        'email' => 'email_invalido',
        'senha' => '123456',
    ];

    $resposta = $this->postJson('/api/usuarios', $dadosInvalidos);
    $resposta->assertStatus(422)
        ->assertJsonValidationErrors(['email']);
});

test('POST /api/usuarios rejeita gestor com nome curto', function () {
    $dadosInvalidos = [
        'perfil' => 'G',
        'nome' => 'AB',
        'email' => 'gestor@empresa.com',
        'senha' => '123456',
        'cnpj' => '12345678000190',
    ];

    $resposta = $this->postJson('/api/usuarios', $dadosInvalidos);
    $resposta->assertStatus(422)
        ->assertJsonValidationErrors(['nome']);
});

test('POST /api/usuarios rejeita gestor com senha curta', function () {
    $dadosInvalidos = [
        'perfil' => 'G',
        'nome' => 'Gestor',
        'email' => 'gestor@empresa.com',
        'senha' => '12345',
        'cnpj' => '12345678000190',
    ];

    $resposta = $this->postJson('/api/usuarios', $dadosInvalidos);
    $resposta->assertStatus(422)
        ->assertJsonValidationErrors(['senha']);
});

test('POST /api/usuarios rejeita gestor com CNPJ curto', function () {
    $dadosInvalidos = [
        'perfil' => 'G',
        'nome' => 'Gestor',
        'email' => 'gestor@empresa.com',
        'senha' => '123456',
        'cnpj' => '123',
    ];

    $resposta = $this->postJson('/api/usuarios', $dadosInvalidos);
    $resposta->assertStatus(422)
        ->assertJsonValidationErrors(['cnpj']);
});

test('POST /api/usuarios retorna 422 para dados inválidos', function () {
    $resposta = $this->postJson('/api/usuarios', []);
    $resposta->assertStatus(422);
});

test('GET /api/usuarios/{id} retorna 404 se não encontrado', function () {
    $resposta = $this->getJson('/api/usuarios/999');
    $resposta->assertStatus(404);
});

test('PUT /api/usuarios/{id} retorna 404 ao atualizar cliente inexistente', function () {
    $resposta = $this->putJson('/api/usuarios/999', [
        'nome' => 'Cliente Inexistente',
        'email' => 'cliente@teste.com',
        'senha' => '123456',
    ]);
    $resposta->assertStatus(404);
});

test('PUT /api/usuarios/{id} retorna 404 ao atualizar gestor inexistente', function () {
    $resposta = $this->putJson('/api/usuarios/999', [
        'nome' => 'Gestor Inexistente',
        'email' => 'gestor@empresa.com',
        'senha' => '123456',
        'cnpj' => '12345678000190',
    ]);
    $resposta->assertStatus(404);
});

test('DELETE /api/usuarios/{id} remove usuário inexistente retorna 404', function () {
    $resposta = $this->deleteJson('/api/usuarios/999');
    $resposta->assertStatus(404);
});
