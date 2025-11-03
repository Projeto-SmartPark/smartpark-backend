<?php

use App\Modules\Telefone\Telefone;
use App\Modules\Telefone\TelefoneService;
use Illuminate\Database\Eloquent\ModelNotFoundException;

beforeEach(function () {
    $this->withoutMiddleware();
    $this->mockService = Mockery::mock(TelefoneService::class);
    $this->app->instance(TelefoneService::class, $this->mockService);
});

afterEach(function () {
    Mockery::close();
});

test('deve listar todos os telefones', function () {
    $this->mockService
        ->shouldReceive('listarTelefones')
        ->once()
        ->andReturn([
            ['id_telefone' => 1, 'ddd' => '11', 'numero' => '987654321'],
            ['id_telefone' => 2, 'ddd' => '21', 'numero' => '999999999'],
        ]);

    $response = $this->getJson('/api/telefones');

    $response->assertOk()
        ->assertJsonFragment(['ddd' => '11', 'numero' => '987654321'])
        ->assertJsonFragment(['ddd' => '21', 'numero' => '999999999']);
});

test('deve criar telefone com sucesso', function () {
    $dados = [
        'ddd' => '11',
        'numero' => '999999999',
    ];

    $mockTelefone = Mockery::mock(Telefone::class)->makePartial();
    $mockTelefone->id_telefone = 1;
    $mockTelefone->ddd = '11';
    $mockTelefone->numero = '999999999';

    $this->mockService
        ->shouldReceive('criarTelefone')
        ->once()
        ->with($dados)
        ->andReturn($mockTelefone);

    $response = $this->postJson('/api/telefones', $dados);

    $response->assertCreated()
        ->assertJsonFragment(['message' => 'Telefone criado com sucesso.'])
        ->assertJsonFragment(['ddd' => '11', 'numero' => '999999999']);
});

test('deve rejeitar telefone com dados inválidos', function () {
    $dados = ['ddd' => '', 'numero' => ''];

    $response = $this->postJson('/api/telefones', $dados);

    $response->assertStatus(422)
        ->assertJsonStructure(['message', 'errors']);
});

test('deve retornar telefone por id', function () {
    $mockTelefone = Mockery::mock(Telefone::class)->makePartial();
    $mockTelefone->id_telefone = 1;
    $mockTelefone->ddd = '11';
    $mockTelefone->numero = '987654321';

    $this->mockService
        ->shouldReceive('buscarTelefonePorId')
        ->once()
        ->with(1)
        ->andReturn($mockTelefone);

    $response = $this->getJson('/api/telefones/1');

    $response->assertOk()
        ->assertJsonFragment(['ddd' => '11', 'numero' => '987654321']);
});

test('deve retornar erro se telefone não existir', function () {
    $this->mockService
        ->shouldReceive('buscarTelefonePorId')
        ->once()
        ->with(999)
        ->andThrow(new ModelNotFoundException);

    $response = $this->getJson('/api/telefones/999');

    $response->assertNotFound()
        ->assertJsonFragment(['error' => 'Telefone não encontrado.']);
});

test('deve atualizar telefone com sucesso', function () {
    $dados = [
        'ddd' => '19',
        'numero' => '777777777',
    ];

    $mockTelefone = Mockery::mock(Telefone::class)->makePartial();
    $mockTelefone->id_telefone = 1;
    $mockTelefone->ddd = '19';
    $mockTelefone->numero = '777777777';

    $this->mockService
        ->shouldReceive('atualizarTelefone')
        ->once()
        ->with(1, $dados)
        ->andReturn($mockTelefone);

    $response = $this->putJson('/api/telefones/1', $dados);

    $response->assertOk()
        ->assertJsonFragment(['message' => 'Telefone atualizado com sucesso.'])
        ->assertJsonFragment(['ddd' => '19', 'numero' => '777777777']);
});

test('deve retornar erro se telefone não existir ao atualizar', function () {
    $dados = [
        'ddd' => '11',
        'numero' => '888888888',
    ];

    $this->mockService
        ->shouldReceive('atualizarTelefone')
        ->once()
        ->with(999, $dados)
        ->andThrow(new ModelNotFoundException);

    $response = $this->putJson('/api/telefones/999', $dados);

    $response->assertNotFound()
        ->assertJsonFragment(['error' => 'Telefone não encontrado.']);
});

test('deve deletar telefone com sucesso', function () {
    $this->mockService
        ->shouldReceive('deletarTelefone')
        ->once()
        ->with(1)
        ->andReturnTrue();

    $response = $this->deleteJson('/api/telefones/1');

    $response->assertOk()
        ->assertJsonFragment(['message' => 'Telefone deletado com sucesso.']);
});

test('deve retornar erro se telefone não existir ao deletar', function () {
    $this->mockService
        ->shouldReceive('deletarTelefone')
        ->once()
        ->with(999)
        ->andThrow(new ModelNotFoundException);

    $response = $this->deleteJson('/api/telefones/999');

    $response->assertNotFound()
        ->assertJsonFragment(['error' => 'Telefone não encontrado.']);
});
