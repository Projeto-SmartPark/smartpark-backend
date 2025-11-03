<?php

use App\Modules\Endereco\Endereco;
use App\Modules\Estacionamento\Estacionamento;
use App\Modules\Tarifa\Tarifa;
use App\Modules\Tarifa\TarifaService;
use App\Modules\Usuarios\Models\Gestor;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->withoutMiddleware(\App\Http\Middleware\AuthMicroservico::class);

    // Criar usuario, gestor e estacionamento para validação exists
    DB::table('usuarios')->insert([
        'id_usuario' => 1,
        'perfil' => 'G',
    ]);

    Gestor::create([
        'id_gestor' => 1,
        'nome' => 'Gestor Teste',
        'email' => 'gestor@test.com',
        'senha' => bcrypt('senha123'),
        'cnpj' => '12345678000199',
        'usuario_id' => 1,
    ]);

    Endereco::create([
        'id_endereco' => 1,
        'cep' => '01310100',
        'logradouro' => 'Avenida Teste',
        'numero' => '1000',
        'bairro' => 'Centro',
        'cidade' => 'São Paulo',
        'estado' => 'SP',
    ]);

    Estacionamento::create([
        'id_estacionamento' => 1,
        'nome' => 'Estacionamento Teste',
        'capacidade' => 100,
        'hora_abertura' => '08:00:00',
        'hora_fechamento' => '22:00:00',
        'lotado' => 'N',
        'gestor_id' => 1,
        'endereco_id' => 1,
    ]);

    $this->mockService = Mockery::mock(TarifaService::class);
    $this->app->instance(TarifaService::class, $this->mockService);
});

afterEach(function () {
    Mockery::close();
});

test('deve listar todas as tarifas', function () {
    $this->mockService
        ->shouldReceive('listarTarifas')
        ->once()
        ->andReturn([
            ['id_tarifa' => 1, 'nome' => 'Tarifa Horária', 'valor' => 5.50, 'tipo' => 'hora'],
            ['id_tarifa' => 2, 'nome' => 'Tarifa Diária', 'valor' => 45.00, 'tipo' => 'diaria'],
        ]);

    $response = $this->getJson('/api/tarifas');

    $response->assertOk()
        ->assertJsonFragment(['nome' => 'Tarifa Horária'])
        ->assertJsonFragment(['nome' => 'Tarifa Diária']);
});

test('deve criar tarifa com sucesso', function () {
    $dados = [
        'nome' => 'Tarifa Horária',
        'valor' => 5.50,
        'tipo' => 'hora',
        'estacionamento_id' => 1,
    ];

    $mockTarifa = Mockery::mock(Tarifa::class)->makePartial();
    $mockTarifa->id_tarifa = 1;
    $mockTarifa->nome = 'Tarifa Horária';
    $mockTarifa->valor = 5.50;
    $mockTarifa->tipo = 'hora';

    $this->mockService
        ->shouldReceive('criarTarifa')
        ->once()
        ->with($dados)
        ->andReturn($mockTarifa);

    $response = $this->postJson('/api/tarifas', $dados);

    $response->assertCreated()
        ->assertJsonFragment(['message' => 'Tarifa criada com sucesso.'])
        ->assertJsonFragment(['nome' => 'Tarifa Horária']);
});

test('deve rejeitar tarifa com dados inválidos', function () {
    $dados = ['nome' => '', 'valor' => -10, 'tipo' => 'invalido'];

    $response = $this->postJson('/api/tarifas', $dados);

    $response->assertStatus(422)
        ->assertJsonStructure(['message', 'errors']);
});

test('deve retornar tarifa por id', function () {
    $mockTarifa = Mockery::mock(Tarifa::class)->makePartial();
    $mockTarifa->id_tarifa = 1;
    $mockTarifa->nome = 'Tarifa Horária';
    $mockTarifa->valor = 5.50;
    $mockTarifa->tipo = 'hora';

    $this->mockService
        ->shouldReceive('buscarTarifaPorId')
        ->once()
        ->with(1)
        ->andReturn($mockTarifa);

    $response = $this->getJson('/api/tarifas/1');

    $response->assertOk()
        ->assertJsonFragment(['nome' => 'Tarifa Horária'])
        ->assertJsonFragment(['valor' => '5.50']);
});

test('deve retornar erro se tarifa não existir', function () {
    $this->mockService
        ->shouldReceive('buscarTarifaPorId')
        ->once()
        ->with(999)
        ->andThrow(new ModelNotFoundException);

    $response = $this->getJson('/api/tarifas/999');

    $response->assertNotFound()
        ->assertJsonFragment(['error' => 'Tarifa não encontrada']);
});

test('deve atualizar tarifa com sucesso', function () {
    $dados = [
        'nome' => 'Tarifa Mensal',
        'valor' => 350.00,
        'tipo' => 'mensal',
        'estacionamento_id' => 1,
    ];

    $mockTarifa = Mockery::mock(Tarifa::class)->makePartial();
    $mockTarifa->id_tarifa = 1;
    $mockTarifa->nome = 'Tarifa Mensal';
    $mockTarifa->valor = 350.00;
    $mockTarifa->tipo = 'mensal';

    $this->mockService
        ->shouldReceive('atualizarTarifa')
        ->once()
        ->with(1, $dados)
        ->andReturn($mockTarifa);

    $response = $this->putJson('/api/tarifas/1', $dados);

    $response->assertOk()
        ->assertJsonFragment(['message' => 'Tarifa atualizada com sucesso.'])
        ->assertJsonFragment(['nome' => 'Tarifa Mensal']);
});

test('deve retornar erro se tarifa não existir ao atualizar', function () {
    $dados = [
        'nome' => 'Tarifa Teste',
        'valor' => 10.00,
        'tipo' => 'hora',
        'estacionamento_id' => 1,
    ];

    $this->mockService
        ->shouldReceive('atualizarTarifa')
        ->once()
        ->with(999, $dados)
        ->andThrow(new ModelNotFoundException);

    $response = $this->putJson('/api/tarifas/999', $dados);

    $response->assertNotFound()
        ->assertJsonFragment(['error' => 'Tarifa não encontrada.']);
});

test('deve deletar tarifa com sucesso', function () {
    $this->mockService
        ->shouldReceive('deletarTarifa')
        ->once()
        ->with(1)
        ->andReturnTrue();

    $response = $this->deleteJson('/api/tarifas/1');

    $response->assertOk()
        ->assertJsonFragment(['message' => 'Tarifa deletada com sucesso']);
});

test('deve retornar erro se tarifa não existir ao deletar', function () {
    $this->mockService
        ->shouldReceive('deletarTarifa')
        ->once()
        ->with(999)
        ->andThrow(new ModelNotFoundException);

    $response = $this->deleteJson('/api/tarifas/999');

    $response->assertNotFound()
        ->assertJsonFragment(['error' => 'Tarifa não encontrada']);
});
