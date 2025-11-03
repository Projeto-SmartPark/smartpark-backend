<?php

use App\Modules\Estacionamento\Estacionamento;
use App\Modules\Estacionamento\EstacionamentoService;
use App\Modules\Usuarios\Models\Gestor;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->withoutMiddleware(\App\Http\Middleware\AuthMicroservico::class);

    // Criar usuario e gestor para validação exists
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

    $this->mockService = Mockery::mock(EstacionamentoService::class);
    $this->app->instance(EstacionamentoService::class, $this->mockService);
});

afterEach(function () {
    Mockery::close();
});

test('deve listar todos os estacionamentos', function () {
    $this->mockService
        ->shouldReceive('listarEstacionamentos')
        ->once()
        ->andReturn([
            ['id_estacionamento' => 1, 'nome' => 'Estacionamento Central', 'capacidade' => 100],
            ['id_estacionamento' => 2, 'nome' => 'Park Sul', 'capacidade' => 50],
        ]);

    $response = $this->getJson('/api/estacionamentos');

    $response->assertOk()
        ->assertJsonFragment(['nome' => 'Estacionamento Central'])
        ->assertJsonFragment(['nome' => 'Park Sul']);
});

test('deve criar estacionamento com sucesso', function () {
    $dados = [
        'nome' => 'Estacionamento Central',
        'capacidade' => 120,
        'hora_abertura' => '08:00:00',
        'hora_fechamento' => '22:00:00',
        'lotado' => 'N',
        'gestor_id' => 1,
        'endereco' => [
            'cep' => '01310100',
            'logradouro' => 'Avenida Paulista',
            'numero' => '1000',
            'complemento' => 'Sala 101',
            'bairro' => 'Bela Vista',
            'cidade' => 'São Paulo',
            'estado' => 'SP',
        ],
        'telefones' => [
            ['ddd' => '11', 'numero' => '987654321'],
        ],
    ];

    $mockEstacionamento = Mockery::mock(Estacionamento::class)->makePartial();
    $mockEstacionamento->id_estacionamento = 1;
    $mockEstacionamento->nome = 'Estacionamento Central';

    $this->mockService
        ->shouldReceive('criarEstacionamento')
        ->once()
        ->with(
            Mockery::on(fn ($val) => isset($val['nome'])), // dados principais
            Mockery::on(fn ($val) => isset($val['cep'])), // endereço
            Mockery::on(fn ($val) => is_array($val)) // telefones
        )
        ->andReturn($mockEstacionamento);

    $response = $this->postJson('/api/estacionamentos', $dados);

    $response->assertCreated()
        ->assertJsonFragment(['message' => 'Estacionamento criado com sucesso.'])
        ->assertJsonFragment(['nome' => 'Estacionamento Central']);
});

test('deve rejeitar dados inválidos ao criar estacionamento', function () {
    $dados = [
        'nome' => '',
        'capacidade' => '',
        'hora_abertura' => '',
        'hora_fechamento' => '',
        'gestor_id' => '',
        'endereco' => [],
        'telefones' => [],
    ];

    $response = $this->postJson('/api/estacionamentos', $dados);

    $response->assertStatus(422)
        ->assertJsonStructure(['message', 'errors']);
});

test('deve buscar estacionamento por id', function () {
    $mockEstacionamento = Mockery::mock(Estacionamento::class)->makePartial();
    $mockEstacionamento->id_estacionamento = 5;
    $mockEstacionamento->nome = 'Park Norte';
    $mockEstacionamento->capacidade = 80;

    $this->mockService
        ->shouldReceive('buscarEstacionamentoPorId')
        ->once()
        ->with(5)
        ->andReturn($mockEstacionamento);

    $response = $this->getJson('/api/estacionamentos/5');

    $response->assertOk()
        ->assertJsonFragment(['nome' => 'Park Norte'])
        ->assertJsonFragment(['capacidade' => 80]);
});

test('deve retornar erro se estacionamento não existir', function () {
    $this->mockService
        ->shouldReceive('buscarEstacionamentoPorId')
        ->once()
        ->with(999)
        ->andThrow(new ModelNotFoundException);

    $response = $this->getJson('/api/estacionamentos/999');

    $response->assertNotFound()
        ->assertJsonFragment(['error' => 'Estacionamento não encontrado.']);
});

test('deve atualizar estacionamento com sucesso', function () {
    $dados = [
        'nome' => 'Estacionamento Atualizado',
        'capacidade' => 150,
        'hora_abertura' => '07:00:00',
        'hora_fechamento' => '23:00:00',
        'lotado' => 'N',
        'gestor_id' => 1,
        'endereco' => [
            'cep' => '01310100',
            'logradouro' => 'Rua Nova',
            'numero' => '200',
            'bairro' => 'Centro',
            'cidade' => 'São Paulo',
            'estado' => 'SP',
        ],
        'telefones' => [
            ['ddd' => '11', 'numero' => '999999999'],
        ],
    ];

    $mockEstacionamento = Mockery::mock(Estacionamento::class)->makePartial();
    $mockEstacionamento->id_estacionamento = 10;
    $mockEstacionamento->nome = 'Estacionamento Atualizado';

    $this->mockService
        ->shouldReceive('atualizarEstacionamento')
        ->once()
        ->with(
            10,
            Mockery::on(fn ($val) => isset($val['nome'])),
            Mockery::on(fn ($val) => isset($val['cep'])),
            Mockery::on(fn ($val) => is_array($val))
        )
        ->andReturn($mockEstacionamento);

    $response = $this->putJson('/api/estacionamentos/10', $dados);

    $response->assertOk()
        ->assertJsonFragment(['message' => 'Estacionamento atualizado com sucesso.'])
        ->assertJsonFragment(['nome' => 'Estacionamento Atualizado']);
});

test('deve retornar erro se estacionamento não existir ao atualizar', function () {
    $dados = [
        'nome' => 'Inexistente',
        'capacidade' => 50,
        'hora_abertura' => '08:00:00',
        'hora_fechamento' => '20:00:00',
        'gestor_id' => 1,
        'endereco' => [
            'cep' => '01310100',
            'logradouro' => 'Rua Teste',
            'numero' => '1',
            'bairro' => 'Centro',
            'cidade' => 'SP',
            'estado' => 'SP',
        ],
        'telefones' => [
            ['ddd' => '11', 'numero' => '888888888'],
        ],
    ];

    $this->mockService
        ->shouldReceive('atualizarEstacionamento')
        ->once()
        ->with(999, Mockery::any(), Mockery::any(), Mockery::any())
        ->andThrow(new ModelNotFoundException);

    $response = $this->putJson('/api/estacionamentos/999', $dados);

    $response->assertNotFound()
        ->assertJsonFragment(['error' => 'Estacionamento não encontrado.']);
});

test('deve deletar estacionamento com sucesso', function () {
    $this->mockService
        ->shouldReceive('deletarEstacionamento')
        ->once()
        ->with(5)
        ->andReturnTrue();

    $response = $this->deleteJson('/api/estacionamentos/5');

    $response->assertOk()
        ->assertJsonFragment(['message' => 'Estacionamento removido com sucesso.']);
});

test('deve retornar erro se estacionamento não existir ao deletar', function () {
    $this->mockService
        ->shouldReceive('deletarEstacionamento')
        ->once()
        ->with(999)
        ->andThrow(new ModelNotFoundException);

    $response = $this->deleteJson('/api/estacionamentos/999');

    $response->assertNotFound()
        ->assertJsonFragment(['error' => 'Estacionamento não encontrado.']);
});
