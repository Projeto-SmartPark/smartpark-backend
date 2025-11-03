<?php

use App\Modules\Acesso\Acesso;
use App\Modules\Acesso\AcessoService;
use App\Modules\Endereco\Endereco;
use App\Modules\Estacionamento\Estacionamento;
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

    $this->mockService = Mockery::mock(AcessoService::class);
    $this->app->instance(AcessoService::class, $this->mockService);
});

afterEach(function () {
    Mockery::close();
});

test('deve listar todos os acessos', function () {
    $this->mockService
        ->shouldReceive('listarAcessos')
        ->once()
        ->andReturn([
            ['id_acesso' => 1, 'data' => '2025-01-15', 'hora_inicio' => '08:30:00', 'cliente_id' => 1],
            ['id_acesso' => 2, 'data' => '2025-01-16', 'hora_inicio' => '09:00:00', 'cliente_id' => 1],
        ]);

    $response = $this->getJson('/api/acessos');

    $response->assertOk()
        ->assertJsonFragment(['data' => '2025-01-15'])
        ->assertJsonFragment(['data' => '2025-01-16']);
});

test('deve criar acesso com sucesso', function () {
    $dados = [
        'data' => '2025-01-15',
        'hora_inicio' => '08:30:00',
        'hora_fim' => '12:45:00',
        'valor_total' => 22.50,
        'veiculo_id' => 1,
        'vaga_id' => 1,
        'cliente_id' => 1,
    ];

    $mockAcesso = Mockery::mock(Acesso::class)->makePartial();
    $mockAcesso->id_acesso = 1;
    $mockAcesso->data = '2025-01-15';
    $mockAcesso->hora_inicio = '08:30:00';

    $this->mockService
        ->shouldReceive('criarAcesso')
        ->once()
        ->with($dados)
        ->andReturn($mockAcesso);

    $response = $this->postJson('/api/acessos', $dados);

    $response->assertCreated()
        ->assertJsonFragment(['message' => 'Acesso criado com sucesso.']);
});

test('deve rejeitar acesso com dados inválidos', function () {
    $dados = ['data' => '', 'hora_inicio' => '', 'veiculo_id' => null];

    $response = $this->postJson('/api/acessos', $dados);

    $response->assertStatus(422)
        ->assertJsonStructure(['message', 'errors']);
});

test('deve retornar acesso por id', function () {
    $mockAcesso = Mockery::mock(Acesso::class)->makePartial();
    $mockAcesso->id_acesso = 1;
    $mockAcesso->data = '2025-01-15';
    $mockAcesso->hora_inicio = '08:30:00';

    $this->mockService
        ->shouldReceive('buscarAcessoPorId')
        ->once()
        ->with(1)
        ->andReturn($mockAcesso);

    $response = $this->getJson('/api/acessos/1');

    $response->assertOk()
        ->assertJsonFragment(['data' => '2025-01-15']);
});

test('deve retornar erro se acesso não existir', function () {
    $this->mockService
        ->shouldReceive('buscarAcessoPorId')
        ->once()
        ->with(999)
        ->andThrow(new ModelNotFoundException);

    $response = $this->getJson('/api/acessos/999');

    $response->assertNotFound()
        ->assertJsonFragment(['error' => 'Acesso não encontrado']);
});

test('deve atualizar acesso com sucesso', function () {
    $dados = [
        'data' => '2025-01-16',
        'hora_inicio' => '09:00:00',
        'hora_fim' => '13:00:00',
        'valor_total' => 25.00,
        'veiculo_id' => 1,
        'vaga_id' => 1,
        'cliente_id' => 1,
    ];

    $mockAcesso = Mockery::mock(Acesso::class)->makePartial();
    $mockAcesso->id_acesso = 1;
    $mockAcesso->data = '2025-01-16';

    $this->mockService
        ->shouldReceive('atualizarAcesso')
        ->once()
        ->with(1, $dados)
        ->andReturn($mockAcesso);

    $response = $this->putJson('/api/acessos/1', $dados);

    $response->assertOk()
        ->assertJsonFragment(['message' => 'Acesso atualizado com sucesso.']);
});

test('deve retornar erro se acesso não existir ao atualizar', function () {
    $dados = [
        'data' => '2025-01-16',
        'hora_inicio' => '09:00:00',
        'veiculo_id' => 1,
        'vaga_id' => 1,
        'cliente_id' => 1,
    ];

    $this->mockService
        ->shouldReceive('atualizarAcesso')
        ->once()
        ->with(999, $dados)
        ->andThrow(new ModelNotFoundException);

    $response = $this->putJson('/api/acessos/999', $dados);

    $response->assertNotFound()
        ->assertJsonFragment(['error' => 'Acesso não encontrado.']);
});

test('deve deletar acesso com sucesso', function () {
    $this->mockService
        ->shouldReceive('deletarAcesso')
        ->once()
        ->with(1)
        ->andReturnTrue();

    $response = $this->deleteJson('/api/acessos/1');

    $response->assertOk()
        ->assertJsonFragment(['message' => 'Acesso deletado com sucesso']);
});

test('deve retornar erro se acesso não existir ao deletar', function () {
    $this->mockService
        ->shouldReceive('deletarAcesso')
        ->once()
        ->with(999)
        ->andThrow(new ModelNotFoundException);

    $response = $this->deleteJson('/api/acessos/999');

    $response->assertNotFound()
        ->assertJsonFragment(['error' => 'Acesso não encontrado']);
});
