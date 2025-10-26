<?php

use App\Modules\Endereco\Endereco;

$parametros = require dirname(__DIR__, 2).DIRECTORY_SEPARATOR.'Datasets'.DIRECTORY_SEPARATOR.'parametrosEnderecoService.php';

// ========== TESTES DE LISTAGEM (GET /enderecos) ==========

test('GET /api/enderecos lista todos os endereços', function () use ($parametros) {
    Endereco::create($parametros['validos'][0]);
    Endereco::create($parametros['validos'][1]);

    $resposta = $this->getJson('/api/enderecos');

    $resposta->assertStatus(200)
        ->assertJsonCount(2);
});

test('GET /api/enderecos retorna lista vazia', function () {
    $resposta = $this->getJson('/api/enderecos');

    $resposta->assertStatus(200)
        ->assertJsonCount(0);
});

// ========== TESTES DE CRIAÇÃO (POST /enderecos) ==========

test('POST /api/enderecos cria endereço com sucesso', function () use ($parametros) {
    $dadosEndereco = $parametros['validos'][0];

    $resposta = $this->postJson('/api/enderecos', $dadosEndereco);

    $resposta->assertStatus(201)
        ->assertJsonPath('message', 'Endereço criado com sucesso.');
    $this->assertDatabaseHas('enderecos', ['cep' => $dadosEndereco['cep']]);
});

test('POST /api/enderecos rejeita dados inválidos', function () use ($parametros) {
    // Teste com CEP inválido
    $dadosInvalidos = array_merge($parametros['validos'][0], ['cep' => '123']);
    $resposta = $this->postJson('/api/enderecos', $dadosInvalidos);
    $resposta->assertStatus(422)->assertJsonValidationErrors('cep');

    // Teste com Estado inválido
    $dadosInvalidos = array_merge($parametros['validos'][0], ['estado' => 'SPP']);
    $resposta = $this->postJson('/api/enderecos', $dadosInvalidos);
    $resposta->assertStatus(422)->assertJsonValidationErrors('estado');

    // Teste com Cidade longa
    $dadosInvalidos = array_merge($parametros['validos'][0], ['cidade' => str_repeat('A', 81)]);
    $resposta = $this->postJson('/api/enderecos', $dadosInvalidos);
    $resposta->assertStatus(422)->assertJsonValidationErrors('cidade');
});

// ========== TESTES DE BUSCA (GET /enderecos/{id}) ==========

test('GET /api/enderecos/{id} busca endereço com sucesso', function () use ($parametros) {
    $endereco = Endereco::create($parametros['validos'][0]);

    $resposta = $this->getJson('/api/enderecos/'.$endereco->id_endereco);

    $resposta->assertStatus(200)
        ->assertJsonPath('cep', $endereco->cep);
});

test('GET /api/enderecos/{id} retorna 404 para ID inexistente', function () use ($parametros) {
    $resposta = $this->getJson('/api/enderecos/'.$parametros['ids']['inexistente']);

    $resposta->assertStatus(404);
});

// ========== TESTES DE ATUALIZAÇÃO (PUT /enderecos/{id}) ==========

test('PUT /api/enderecos/{id} atualiza endereço com sucesso', function () use ($parametros) {
    $endereco = Endereco::create($parametros['validos'][0]);
    $dadosParaAtualizar = $parametros['validos'][1];

    $resposta = $this->putJson('/api/enderecos/'.$endereco->id_endereco, $dadosParaAtualizar);

    $resposta->assertStatus(200)
        ->assertJsonPath('message', 'Endereço atualizado com sucesso.');
    $this->assertDatabaseHas('enderecos', ['id_endereco' => $endereco->id_endereco, 'cep' => $dadosParaAtualizar['cep']]);
});

test('PUT /api/enderecos/{id} retorna 404 para ID inexistente', function () use ($parametros) {
    $resposta = $this->putJson('/api/enderecos/'.$parametros['ids']['inexistente'], $parametros['validos'][0]);

    $resposta->assertStatus(404);
});

test('PUT /api/enderecos/{id} rejeita dados inválidos', function () use ($parametros) {
    $endereco = Endereco::create($parametros['validos'][0]);
    $dadosInvalidos = array_merge($parametros['validos'][0], ['cep' => '123']);

    $resposta = $this->putJson('/api/enderecos/'.$endereco->id_endereco, $dadosInvalidos);

    $resposta->assertStatus(422)->assertJsonValidationErrors('cep');
});

// ========== TESTES DE REMOÇÃO (DELETE /enderecos/{id}) ==========

test('DELETE /api/enderecos/{id} remove endereço com sucesso', function () use ($parametros) {
    $endereco = Endereco::create($parametros['validos'][0]);

    $resposta = $this->deleteJson('/api/enderecos/'.$endereco->id_endereco);

    $resposta->assertStatus(200)
        ->assertJsonPath('message', 'Endereço deletado com sucesso.');
    $this->assertDatabaseMissing('enderecos', ['id_endereco' => $endereco->id_endereco]);
});

test('DELETE /api/enderecos/{id} retorna 404 para ID inexistente', function () use ($parametros) {
    $resposta = $this->deleteJson('/api/enderecos/'.$parametros['ids']['inexistente']);

    $resposta->assertStatus(404);
});
