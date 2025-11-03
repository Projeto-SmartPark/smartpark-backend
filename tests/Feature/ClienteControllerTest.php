<?php

use App\Modules\Usuarios\Models\Cliente;
use App\Modules\Usuarios\Services\ClienteService;
use Illuminate\Database\Eloquent\ModelNotFoundException;

beforeEach(function () {
    $this->withoutMiddleware();
    $this->mockService = Mockery::mock(ClienteService::class);
    $this->app->instance(ClienteService::class, $this->mockService);
});

afterEach(function () {
    Mockery::close();
});

test('deve listar todos os clientes', function () {
    $this->mockService
        ->shouldReceive('listarTodos')
        ->once()
        ->andReturn([
            ['id_cliente' => 1, 'nome' => 'João Silva', 'email' => 'joao@teste.com'],
            ['id_cliente' => 2, 'nome' => 'Maria Oliveira', 'email' => 'maria@teste.com'],
        ]);

    $response = $this->getJson('/api/clientes');

    $response->assertOk()
        ->assertJsonFragment(['nome' => 'João Silva'])
        ->assertJsonFragment(['nome' => 'Maria Oliveira']);
});

test('deve criar cliente com sucesso', function () {
    $dados = [
        'nome' => 'Carlos Souza',
        'email' => 'carlos@teste.com',
        'senha' => 'senha123',
    ];

    $mockCliente = Mockery::mock(Cliente::class)->makePartial();
    $mockCliente->id_cliente = 10;
    $mockCliente->nome = 'Carlos Souza';
    $mockCliente->email = 'carlos@teste.com';

    $this->mockService
        ->shouldReceive('criarCliente')
        ->once()
        ->with($dados)
        ->andReturn($mockCliente);

    $response = $this->postJson('/api/clientes', $dados);

    $response->assertCreated()
        ->assertJsonFragment(['message' => 'Cliente criado com sucesso.'])
        ->assertJsonFragment(['nome' => 'Carlos Souza']);
});

test('deve rejeitar dados inválidos ao criar cliente', function () {
    $dados = ['nome' => '', 'email' => '', 'senha' => ''];

    $response = $this->postJson('/api/clientes', $dados);

    $response->assertStatus(422)
        ->assertJsonStructure(['message', 'errors']);
});

test('deve buscar cliente por id', function () {
    $mockCliente = Mockery::mock(Cliente::class)->makePartial();
    $mockCliente->id_cliente = 5;
    $mockCliente->nome = 'Mariana Oliveira';
    $mockCliente->email = 'mariana@corp.com';
    $mockCliente->usuario_id = 3;

    $this->mockService
        ->shouldReceive('buscarPorId')
        ->once()
        ->with(5)
        ->andReturn($mockCliente);

    $response = $this->getJson('/api/clientes/5');

    $response->assertOk()
        ->assertJsonFragment(['nome' => 'Mariana Oliveira'])
        ->assertJsonFragment(['email' => 'mariana@corp.com']);
});

test('deve retornar erro se cliente não existir', function () {
    $this->mockService
        ->shouldReceive('buscarPorId')
        ->once()
        ->with(999)
        ->andThrow(new ModelNotFoundException);

    $response = $this->getJson('/api/clientes/999');

    $response->assertNotFound()
        ->assertJsonFragment(['error' => 'Cliente não encontrado.']);
});

test('deve atualizar cliente com sucesso', function () {
    $dados = [
        'nome' => 'João Atualizado',
        'email' => 'joao@novo.com',
        'senha' => 'novaSenha',
    ];

    $mockCliente = Mockery::mock(Cliente::class)->makePartial();
    $mockCliente->id_cliente = 10;
    $mockCliente->nome = 'João Atualizado';
    $mockCliente->email = 'joao@novo.com';

    $this->mockService
        ->shouldReceive('atualizar')
        ->once()
        ->with(10, $dados)
        ->andReturn($mockCliente);

    $response = $this->putJson('/api/clientes/10', $dados);

    $response->assertOk()
        ->assertJsonFragment(['message' => 'Cliente atualizado com sucesso.']);
});

test('deve retornar erro se cliente não existir ao atualizar', function () {
    $dados = [
        'nome' => 'Fulano',
        'email' => 'fulano@teste.com',
        'senha' => 'senha123',
    ];

    $this->mockService
        ->shouldReceive('atualizar')
        ->once()
        ->with(999, $dados)
        ->andThrow(new ModelNotFoundException);

    $response = $this->putJson('/api/clientes/999', $dados);

    $response->assertNotFound()
        ->assertJsonFragment(['error' => 'Cliente não encontrado.']);
});

test('deve deletar cliente com sucesso', function () {
    $mockCliente = Mockery::mock(Cliente::class)->makePartial();
    $mockCliente->id_cliente = 5;
    $mockCliente->nome = 'Apagar';

    $this->mockService
        ->shouldReceive('buscarPorId')
        ->once()
        ->with(5)
        ->andReturn($mockCliente);

    $this->mockService
        ->shouldReceive('remover')
        ->once()
        ->with(5)
        ->andReturnTrue();

    $response = $this->deleteJson('/api/clientes/5');

    $response->assertOk()
        ->assertJsonFragment(['message' => 'Cliente removido com sucesso.']);
});

test('deve retornar erro se cliente não existir ao deletar', function () {
    $this->mockService
        ->shouldReceive('buscarPorId')
        ->once()
        ->with(999)
        ->andThrow(new ModelNotFoundException);

    $response = $this->deleteJson('/api/clientes/999');

    $response->assertNotFound()
        ->assertJsonFragment(['error' => 'Cliente não encontrado.']);
});
