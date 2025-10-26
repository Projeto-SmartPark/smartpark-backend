<?php

test('GET /api/gestores lista todos os gestores', function () {
    $resposta = $this->getJson('/api/gestores');
    $resposta->assertStatus(200);
});

test('POST /api/usuarios cria gestor com sucesso', function () {
    $dadosGestor = [
        'perfil' => 'G',
        'nome' => 'Maria',
        'email' => 'maria@empresa.com',
        'senha' => '123456',
        'cnpj' => '12345678000190',
    ];

    $resposta = $this->postJson('/api/usuarios', $dadosGestor);
    $resposta->assertStatus(201)
        ->assertJsonPath('message', 'Usuário criado com sucesso.');
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

test('PUT /api/gestores/{id} retorna 404 se gestor não existe', function () {
    $resposta = $this->putJson('/api/gestores/999', [
        'nome' => 'Teste',
        'email' => 'teste@empresa.com',
        'senha' => '123456',
        'cnpj' => '12345678000190',
    ]);
    $resposta->assertStatus(404);
});

test('DELETE /api/gestores/{id} retorna 404 se gestor não existe', function () {
    $resposta = $this->deleteJson('/api/gestores/999');
    $resposta->assertStatus(404);
});
