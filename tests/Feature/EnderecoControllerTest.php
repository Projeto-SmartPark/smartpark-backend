<?php

use App\Modules\Endereco\Endereco;
use App\Modules\Endereco\EnderecoService;
use Illuminate\Database\Eloquent\ModelNotFoundException;

beforeEach(function () {
    $this->withoutMiddleware();
    $this->mockService = Mockery::mock(EnderecoService::class);
    $this->app->instance(EnderecoService::class, $this->mockService);
});

afterEach(function () {
    Mockery::close();
});

test('deve listar todos os endereços', function () {
    $this->mockService
        ->shouldReceive('listarEnderecos')
        ->once()
        ->andReturn([
            ['id' => 1, 'cep' => '12345678', 'cidade' => 'São Paulo'],
            ['id' => 2, 'cep' => '87654321', 'cidade' => 'Rio de Janeiro'],
        ]);

    $response = $this->getJson('/api/enderecos');

    $response->assertOk()
        ->assertJsonFragment(['cidade' => 'São Paulo']);
});

test('deve criar endereço com sucesso', function () {
    $dados = [
        'cep' => '12345678',
        'estado' => 'SP',
        'cidade' => 'São Paulo',
        'bairro' => 'Centro',
        'numero' => '100',
        'logradouro' => 'Rua das Flores',
    ];

    $mockEndereco = Mockery::mock(Endereco::class)->makePartial();
    $mockEndereco->id = 1;
    $mockEndereco->cep = '12345678';

    $this->mockService
        ->shouldReceive('criarEndereco')
        ->once()
        ->with($dados)
        ->andReturn($mockEndereco);

    $response = $this->postJson('/api/enderecos', $dados);

    $response->assertCreated()
        ->assertJsonFragment(['message' => 'Endereço criado com sucesso.']);
});

test('deve rejeitar dados inválidos ao criar endereço', function () {
    $dados = ['cep' => '', 'estado' => '', 'cidade' => '', 'bairro' => '', 'numero' => '', 'logradouro' => ''];

    $response = $this->postJson('/api/enderecos', $dados);

    $response->assertStatus(422)
        ->assertJsonStructure(['message', 'errors']);
});

test('deve buscar endereço por id', function () {
    $mockEndereco = Mockery::mock(Endereco::class)->makePartial();
    $mockEndereco->id = 5;
    $mockEndereco->cep = '12345678';

    $this->mockService
        ->shouldReceive('buscarEnderecoPorId')
        ->once()
        ->with(5)
        ->andReturn($mockEndereco);

    $response = $this->getJson('/api/enderecos/5');

    $response->assertOk()
        ->assertJsonFragment(['cep' => '12345678']);
});

test('deve retornar erro se endereço não existir', function () {
    $this->mockService
        ->shouldReceive('buscarEnderecoPorId')
        ->once()
        ->with(999)
        ->andThrow(new ModelNotFoundException);

    $response = $this->getJson('/api/enderecos/999');

    $response->assertNotFound()
        ->assertJsonFragment(['error' => 'Endereço não encontrado']);
});

test('deve atualizar endereço com sucesso', function () {
    $dados = ['cidade' => 'Campinas'];

    $mockEndereco = Mockery::mock(Endereco::class)->makePartial();
    $mockEndereco->id = 10;
    $mockEndereco->cidade = 'Campinas';

    $this->mockService
        ->shouldReceive('atualizarEndereco')
        ->once()
        ->with(10, $dados)
        ->andReturn($mockEndereco);

    $response = $this->putJson('/api/enderecos/10', $dados);

    $response->assertOk()
        ->assertJsonFragment(['message' => 'Endereço atualizado com sucesso.']);
});

test('deve deletar endereço com sucesso', function () {
    $this->mockService
        ->shouldReceive('deletarEndereco')
        ->once()
        ->with(5)
        ->andReturnTrue();

    $response = $this->deleteJson('/api/enderecos/5');

    $response->assertOk()
        ->assertJsonFragment(['message' => 'Endereço deletado com sucesso.']);
});

test('deve retornar erro se endereço não existir ao deletar', function () {
    $this->mockService
        ->shouldReceive('deletarEndereco')
        ->once()
        ->with(999)
        ->andThrow(new ModelNotFoundException);

    $response = $this->deleteJson('/api/enderecos/999');

    $response->assertNotFound()
        ->assertJsonFragment(['error' => 'Endereço não encontrado.']);
});
