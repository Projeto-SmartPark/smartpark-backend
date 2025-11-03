<?php

use App\Modules\Usuarios\Models\Cliente;
use App\Modules\Veiculo\Veiculo;
use App\Modules\Veiculo\VeiculoService;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->withoutMiddleware(\App\Http\Middleware\AuthMicroservico::class);

    // Criar usuario e cliente para validação exists
    DB::table('usuarios')->insert([
        'id_usuario' => 1,
        'perfil' => 'C',
    ]);

    Cliente::create([
        'id_cliente' => 1,
        'nome' => 'Cliente Teste',
        'email' => 'cliente@test.com',
        'senha' => bcrypt('senha123'),
        'usuario_id' => 1,
    ]);

    $this->mockService = Mockery::mock(VeiculoService::class);
    $this->app->instance(VeiculoService::class, $this->mockService);
});

afterEach(function () {
    Mockery::close();
});

test('deve listar todos os veículos', function () {
    $this->mockService
        ->shouldReceive('listarVeiculos')
        ->once()
        ->andReturn([
            ['id_veiculo' => 1, 'placa' => 'ABC1234', 'cliente_id' => 1],
            ['id_veiculo' => 2, 'placa' => 'XYZ9876', 'cliente_id' => 1],
        ]);

    $response = $this->getJson('/api/veiculos');

    $response->assertOk()
        ->assertJsonFragment(['placa' => 'ABC1234'])
        ->assertJsonFragment(['placa' => 'XYZ9876']);
});

test('deve criar veículo com sucesso', function () {
    $dados = [
        'placa' => 'ABC1234',
        'cliente_id' => 1,
    ];

    $mockVeiculo = Mockery::mock(Veiculo::class)->makePartial();
    $mockVeiculo->id_veiculo = 1;
    $mockVeiculo->placa = 'ABC1234';
    $mockVeiculo->cliente_id = 1;

    $this->mockService
        ->shouldReceive('criarVeiculo')
        ->once()
        ->with($dados)
        ->andReturn($mockVeiculo);

    $response = $this->postJson('/api/veiculos', $dados);

    $response->assertCreated()
        ->assertJsonFragment(['message' => 'Veículo criado com sucesso.'])
        ->assertJsonFragment(['placa' => 'ABC1234']);
});

test('deve rejeitar veículo com dados inválidos', function () {
    $dados = ['placa' => '', 'cliente_id' => null];

    $response = $this->postJson('/api/veiculos', $dados);

    $response->assertStatus(422)
        ->assertJsonStructure(['message', 'errors']);
});

test('deve retornar veículo por id', function () {
    $mockVeiculo = Mockery::mock(Veiculo::class)->makePartial();
    $mockVeiculo->id_veiculo = 1;
    $mockVeiculo->placa = 'ABC1234';
    $mockVeiculo->cliente_id = 1;

    $this->mockService
        ->shouldReceive('buscarVeiculoPorId')
        ->once()
        ->with(1)
        ->andReturn($mockVeiculo);

    $response = $this->getJson('/api/veiculos/1');

    $response->assertOk()
        ->assertJsonFragment(['placa' => 'ABC1234']);
});

test('deve retornar erro se veículo não existir', function () {
    $this->mockService
        ->shouldReceive('buscarVeiculoPorId')
        ->once()
        ->with(999)
        ->andThrow(new ModelNotFoundException);

    $response = $this->getJson('/api/veiculos/999');

    $response->assertNotFound()
        ->assertJsonFragment(['error' => 'Veículo não encontrado.']);
});

test('deve atualizar veículo com sucesso', function () {
    $dados = [
        'placa' => 'DEF5678',
        'cliente_id' => 1,
    ];

    $mockVeiculo = Mockery::mock(Veiculo::class)->makePartial();
    $mockVeiculo->id_veiculo = 1;
    $mockVeiculo->placa = 'DEF5678';
    $mockVeiculo->cliente_id = 1;

    $this->mockService
        ->shouldReceive('atualizarVeiculo')
        ->once()
        ->with(1, $dados)
        ->andReturn($mockVeiculo);

    $response = $this->putJson('/api/veiculos/1', $dados);

    $response->assertOk()
        ->assertJsonFragment(['message' => 'Veículo atualizado com sucesso.'])
        ->assertJsonFragment(['placa' => 'DEF5678']);
});

test('deve retornar erro se veículo não existir ao atualizar', function () {
    $dados = [
        'placa' => 'XXX9999',
        'cliente_id' => 1,
    ];

    $this->mockService
        ->shouldReceive('atualizarVeiculo')
        ->once()
        ->with(999, $dados)
        ->andThrow(new ModelNotFoundException);

    $response = $this->putJson('/api/veiculos/999', $dados);

    $response->assertNotFound()
        ->assertJsonFragment(['error' => 'Veículo não encontrado.']);
});

test('deve deletar veículo com sucesso', function () {
    $this->mockService
        ->shouldReceive('deletarVeiculo')
        ->once()
        ->with(1)
        ->andReturnTrue();

    $response = $this->deleteJson('/api/veiculos/1');

    $response->assertOk()
        ->assertJsonFragment(['message' => 'Veículo removido com sucesso.']);
});

test('deve retornar erro se veículo não existir ao deletar', function () {
    $this->mockService
        ->shouldReceive('deletarVeiculo')
        ->once()
        ->with(999)
        ->andThrow(new ModelNotFoundException);

    $response = $this->deleteJson('/api/veiculos/999');

    $response->assertNotFound()
        ->assertJsonFragment(['error' => 'Veículo não encontrado.']);
});
