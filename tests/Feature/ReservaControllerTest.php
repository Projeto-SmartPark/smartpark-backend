<?php

use App\Modules\Endereco\Endereco;
use App\Modules\Estacionamento\Estacionamento;
use App\Modules\Reserva\Reserva;
use App\Modules\Reserva\ReservaService;
use App\Modules\Usuarios\Models\Cliente;
use App\Modules\Usuarios\Models\Gestor;
use App\Modules\Vaga\Vaga;
use App\Modules\Veiculo\Veiculo;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->withoutMiddleware(\App\Http\Middleware\AuthMicroservico::class);

    // Criar usuarios
    DB::table('usuarios')->insert([
        ['id_usuario' => 1, 'perfil' => 'G'],
        ['id_usuario' => 2, 'perfil' => 'C'],
    ]);

    Gestor::create([
        'id_gestor' => 1,
        'nome' => 'Gestor Teste',
        'email' => 'gestor@test.com',
        'senha' => bcrypt('senha123'),
        'cnpj' => '12345678000199',
        'usuario_id' => 1,
    ]);

    Cliente::create([
        'id_cliente' => 1,
        'nome' => 'Cliente Teste',
        'email' => 'cliente@test.com',
        'senha' => bcrypt('senha123'),
        'usuario_id' => 2,
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

    Vaga::create([
        'id_vaga' => 1,
        'identificacao' => 'A-101',
        'tipo' => 'carro',
        'disponivel' => 'S',
        'estacionamento_id' => 1,
    ]);

    Veiculo::create([
        'id_veiculo' => 1,
        'placa' => 'ABC1234',
        'cliente_id' => 1,
    ]);

    $this->mockService = Mockery::mock(ReservaService::class);
    $this->app->instance(ReservaService::class, $this->mockService);
});

afterEach(function () {
    Mockery::close();
});

test('deve listar todas as reservas', function () {
    $this->mockService
        ->shouldReceive('listarReservas')
        ->once()
        ->andReturn([
            ['id_reserva' => 1, 'data' => '2025-10-26', 'status' => 'ativa', 'cliente_id' => 1],
            ['id_reserva' => 2, 'data' => '2025-10-27', 'status' => 'concluida', 'cliente_id' => 1],
        ]);

    $response = $this->getJson('/api/reservas');

    $response->assertOk()
        ->assertJsonFragment(['status' => 'ativa'])
        ->assertJsonFragment(['status' => 'concluida']);
});

test('deve criar reserva com sucesso', function () {
    $dados = [
        'data' => '2025-10-26',
        'hora_inicio' => '14:00:00',
        'hora_fim' => '16:00:00',
        'status' => 'ativa',
        'cliente_id' => 1,
        'veiculo_id' => 1,
        'vaga_id' => 1,
    ];

    $mockReserva = Mockery::mock(Reserva::class)->makePartial();
    $mockReserva->id_reserva = 1;
    $mockReserva->data = '2025-10-26';
    $mockReserva->status = 'ativa';

    $this->mockService
        ->shouldReceive('criarReserva')
        ->once()
        ->with($dados)
        ->andReturn($mockReserva);

    $response = $this->postJson('/api/reservas', $dados);

    $response->assertCreated()
        ->assertJsonFragment(['message' => 'Reserva criada com sucesso.']);
});

test('deve rejeitar reserva com dados inválidos', function () {
    $dados = ['data' => '', 'hora_inicio' => '', 'cliente_id' => null];

    $response = $this->postJson('/api/reservas', $dados);

    $response->assertStatus(422)
        ->assertJsonStructure(['message', 'errors']);
});

test('deve retornar reserva por id', function () {
    $mockReserva = Mockery::mock(Reserva::class)->makePartial();
    $mockReserva->id_reserva = 1;
    $mockReserva->data = '2025-10-26';
    $mockReserva->status = 'ativa';

    $this->mockService
        ->shouldReceive('buscarReservaPorId')
        ->once()
        ->with(1)
        ->andReturn($mockReserva);

    $response = $this->getJson('/api/reservas/1');

    $response->assertOk()
        ->assertJsonFragment(['data' => '2025-10-26']);
});

test('deve retornar erro se reserva não existir', function () {
    $this->mockService
        ->shouldReceive('buscarReservaPorId')
        ->once()
        ->with(999)
        ->andThrow(new ModelNotFoundException);

    $response = $this->getJson('/api/reservas/999');

    $response->assertNotFound()
        ->assertJsonFragment(['error' => 'Reserva não encontrada.']);
});

test('deve atualizar reserva com sucesso', function () {
    $dados = [
        'data' => '2025-10-27',
        'hora_inicio' => '15:00:00',
        'hora_fim' => '17:00:00',
        'status' => 'cancelada',
        'cliente_id' => 1,
        'veiculo_id' => 1,
        'vaga_id' => 1,
    ];

    $mockReserva = Mockery::mock(Reserva::class)->makePartial();
    $mockReserva->id_reserva = 1;
    $mockReserva->status = 'cancelada';

    $this->mockService
        ->shouldReceive('atualizarReserva')
        ->once()
        ->with(1, $dados)
        ->andReturn($mockReserva);

    $response = $this->putJson('/api/reservas/1', $dados);

    $response->assertOk()
        ->assertJsonFragment(['message' => 'Reserva atualizada com sucesso.']);
});

test('deve retornar erro se reserva não existir ao atualizar', function () {
    $dados = [
        'data' => '2025-10-27',
        'hora_inicio' => '15:00:00',
        'hora_fim' => '17:00:00',
        'status' => 'ativa',
        'cliente_id' => 1,
        'veiculo_id' => 1,
        'vaga_id' => 1,
    ];

    $this->mockService
        ->shouldReceive('atualizarReserva')
        ->once()
        ->with(999, $dados)
        ->andThrow(new ModelNotFoundException);

    $response = $this->putJson('/api/reservas/999', $dados);

    $response->assertNotFound()
        ->assertJsonFragment(['error' => 'Reserva não encontrada.']);
});

test('deve deletar reserva com sucesso', function () {
    $this->mockService
        ->shouldReceive('deletarReserva')
        ->once()
        ->with(1)
        ->andReturnTrue();

    $response = $this->deleteJson('/api/reservas/1');

    $response->assertOk()
        ->assertJsonFragment(['message' => 'Reserva removida com sucesso.']);
});

test('deve retornar erro se reserva não existir ao deletar', function () {
    $this->mockService
        ->shouldReceive('deletarReserva')
        ->once()
        ->with(999)
        ->andThrow(new ModelNotFoundException);

    $response = $this->deleteJson('/api/reservas/999');

    $response->assertNotFound()
        ->assertJsonFragment(['error' => 'Reserva não encontrada.']);
});
