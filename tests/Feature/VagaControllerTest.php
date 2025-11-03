<?php

use App\Modules\Endereco\Endereco;
use App\Modules\Estacionamento\Estacionamento;
use App\Modules\Usuarios\Models\Gestor;
use App\Modules\Vaga\Vaga;
use App\Modules\Vaga\VagaService;
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

    $this->mockService = Mockery::mock(VagaService::class);
    $this->app->instance(VagaService::class, $this->mockService);
});

afterEach(function () {
    Mockery::close();
});

test('deve listar todas as vagas', function () {
    $this->mockService
        ->shouldReceive('listarVagas')
        ->once()
        ->andReturn([
            ['id_vaga' => 1, 'identificacao' => 'A-101', 'tipo' => 'carro', 'disponivel' => 'S', 'estacionamento_id' => 1],
            ['id_vaga' => 2, 'identificacao' => 'B-202', 'tipo' => 'moto', 'disponivel' => 'N', 'estacionamento_id' => 1],
        ]);

    $response = $this->getJson('/api/vagas');

    $response->assertOk()
        ->assertJsonFragment(['identificacao' => 'A-101'])
        ->assertJsonFragment(['identificacao' => 'B-202']);
});

test('deve criar vaga com sucesso', function () {
    $dados = [
        'identificacao' => 'A-101',
        'tipo' => 'carro',
        'disponivel' => 'S',
        'estacionamento_id' => 1,
    ];

    $mockVaga = Mockery::mock(Vaga::class)->makePartial();
    $mockVaga->id_vaga = 1;
    $mockVaga->identificacao = 'A-101';
    $mockVaga->tipo = 'carro';
    $mockVaga->disponivel = 'S';
    $mockVaga->estacionamento_id = 1;

    $this->mockService
        ->shouldReceive('criarVaga')
        ->once()
        ->with($dados)
        ->andReturn($mockVaga);

    $response = $this->postJson('/api/vagas', $dados);

    $response->assertCreated()
        ->assertJsonFragment(['message' => 'Vaga criada com sucesso.'])
        ->assertJsonFragment(['identificacao' => 'A-101']);
});

test('deve rejeitar vaga com dados inválidos', function () {
    $dados = ['identificacao' => '', 'tipo' => 'carro', 'estacionamento_id' => null];

    $response = $this->postJson('/api/vagas', $dados);

    $response->assertStatus(422)
        ->assertJsonStructure(['message', 'errors']);
});

test('deve retornar vaga por id', function () {
    $mockVaga = Mockery::mock(Vaga::class)->makePartial();
    $mockVaga->id_vaga = 1;
    $mockVaga->identificacao = 'A-101';
    $mockVaga->tipo = 'carro';
    $mockVaga->disponivel = 'S';
    $mockVaga->estacionamento_id = 1;

    $this->mockService
        ->shouldReceive('buscarVagaPorId')
        ->once()
        ->with(1)
        ->andReturn($mockVaga);

    $response = $this->getJson('/api/vagas/1');

    $response->assertOk()
        ->assertJsonFragment(['identificacao' => 'A-101'])
        ->assertJsonFragment(['tipo' => 'carro']);
});

test('deve retornar erro se vaga não existir', function () {
    $this->mockService
        ->shouldReceive('buscarVagaPorId')
        ->once()
        ->with(999)
        ->andThrow(new ModelNotFoundException);

    $response = $this->getJson('/api/vagas/999');

    $response->assertNotFound()
        ->assertJsonFragment(['error' => 'Vaga não encontrada.']);
});

test('deve atualizar vaga com sucesso', function () {
    $dados = [
        'identificacao' => 'A-102',
        'tipo' => 'moto',
        'disponivel' => 'N',
        'estacionamento_id' => 1,
    ];

    $mockVaga = Mockery::mock(Vaga::class)->makePartial();
    $mockVaga->id_vaga = 1;
    $mockVaga->identificacao = 'A-102';
    $mockVaga->tipo = 'moto';
    $mockVaga->disponivel = 'N';
    $mockVaga->estacionamento_id = 1;

    $this->mockService
        ->shouldReceive('atualizarVaga')
        ->once()
        ->with(1, $dados)
        ->andReturn($mockVaga);

    $response = $this->putJson('/api/vagas/1', $dados);

    $response->assertOk()
        ->assertJsonFragment(['message' => 'Vaga atualizada com sucesso.'])
        ->assertJsonFragment(['identificacao' => 'A-102']);
});

test('deve retornar erro se vaga não existir ao atualizar', function () {
    $dados = [
        'identificacao' => 'X-999',
        'tipo' => 'carro',
        'disponivel' => 'S',
        'estacionamento_id' => 1,
    ];

    $this->mockService
        ->shouldReceive('atualizarVaga')
        ->once()
        ->with(999, $dados)
        ->andThrow(new ModelNotFoundException);

    $response = $this->putJson('/api/vagas/999', $dados);

    $response->assertNotFound()
        ->assertJsonFragment(['error' => 'Vaga não encontrada.']);
});

test('deve deletar vaga com sucesso', function () {
    $this->mockService
        ->shouldReceive('deletarVaga')
        ->once()
        ->with(1)
        ->andReturnTrue();

    $response = $this->deleteJson('/api/vagas/1');

    $response->assertOk()
        ->assertJsonFragment(['message' => 'Vaga removida com sucesso.']);
});

test('deve retornar erro se vaga não existir ao deletar', function () {
    $this->mockService
        ->shouldReceive('deletarVaga')
        ->once()
        ->with(999)
        ->andThrow(new ModelNotFoundException);

    $response = $this->deleteJson('/api/vagas/999');

    $response->assertNotFound()
        ->assertJsonFragment(['error' => 'Vaga não encontrada.']);
});
