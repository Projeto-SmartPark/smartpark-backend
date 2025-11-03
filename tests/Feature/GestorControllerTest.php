<?php

use App\Modules\Usuarios\Models\Gestor;
use App\Modules\Usuarios\Services\GestorService;

beforeEach(function () {
    $this->withoutMiddleware();
    $this->mockService = Mockery::mock(GestorService::class);
    $this->app->instance(GestorService::class, $this->mockService);
});

afterEach(function () {
    Mockery::close();
});

test('deve listar todos os gestores', function () {
    $this->mockService
        ->shouldReceive('listarTodos')
        ->once()
        ->andReturn([
            ['id_gestor' => 1, 'nome' => 'Gestor Alfa'],
            ['id_gestor' => 2, 'nome' => 'Gestor Beta'],
        ]);

    $response = $this->getJson('/api/gestores');

    $response->assertOk()
        ->assertJson([
            ['id_gestor' => 1, 'nome' => 'Gestor Alfa'],
            ['id_gestor' => 2, 'nome' => 'Gestor Beta'],
        ]);
});

test('deve criar gestor com sucesso', function () {
    $dados = [
        'nome' => 'Gestor Novo',
        'email' => 'novo@empresa.com',
        'senha' => 'senha123',
        'cnpj' => '12345678000199',
    ];

    $mockGestor = Mockery::mock(Gestor::class)->makePartial();
    $mockGestor->id_gestor = 99;
    $mockGestor->nome = 'Gestor Novo';
    $mockGestor->email = 'novo@empresa.com';
    $mockGestor->cnpj = '12345678000199';

    $this->mockService
        ->shouldReceive('criarGestor')
        ->once()
        ->with($dados)
        ->andReturn($mockGestor);

    $response = $this->postJson('/api/gestores', $dados);

    $response->assertCreated()
        ->assertJsonFragment(['message' => 'Gestor criado com sucesso.']);
});

test('deve rejeitar gestor com dados inválidos', function () {
    $dados = [
        'nome' => '',
        'email' => 'email-invalido',
        'senha' => '123',
        'cnpj' => '',
    ];

    $response = $this->postJson('/api/gestores', $dados);

    $response->assertStatus(422)
        ->assertJsonStructure(['message', 'errors']);
});

test('deve retornar gestor por id', function () {
    $mockGestor = Mockery::mock(Gestor::class)->makePartial();
    $mockGestor->id_gestor = 5;
    $mockGestor->nome = 'Gestor Detalhado';
    $mockGestor->email = 'detalhado@empresa.com';
    $mockGestor->cnpj = '12345678000199';

    $this->mockService
        ->shouldReceive('buscarPorId')
        ->once()
        ->with(5)
        ->andReturn($mockGestor);

    $response = $this->getJson('/api/gestores/5');

    $response->assertOk()
        ->assertJson([
            'id_gestor' => 5,
            'nome' => 'Gestor Detalhado',
        ]);
});

test('deve retornar erro se gestor não existir', function () {
    $this->mockService
        ->shouldReceive('buscarPorId')
        ->once()
        ->with(999)
        ->andThrow(new Illuminate\Database\Eloquent\ModelNotFoundException);

    $response = $this->getJson('/api/gestores/999');

    $response->assertNotFound()
        ->assertJsonFragment(['error' => 'Gestor não encontrado.']);
});

test('deve atualizar gestor com sucesso', function () {
    $dados = [
        'nome' => 'Gestor Atualizado',
        'email' => 'gestor@novo.com',
        'senha' => 'novaSenha',
        'cnpj' => '98765432000188',
    ];

    $mockGestor = Mockery::mock(Gestor::class)->makePartial();
    $mockGestor->id_gestor = 10;
    $mockGestor->nome = 'Gestor Atualizado';
    $mockGestor->email = 'gestor@novo.com';
    $mockGestor->cnpj = '98765432000188';

    $this->mockService
        ->shouldReceive('atualizar')
        ->once()
        ->with(10, $dados)
        ->andReturn($mockGestor);

    $response = $this->putJson('/api/gestores/10', $dados);

    $response->assertOk()
        ->assertJsonFragment(['message' => 'Gestor atualizado com sucesso.']);
});

test('deve retornar erro se gestor não existir ao atualizar', function () {
    $dados = [
        'nome' => 'Fulano',
        'email' => 'fulano@empresa.com',
        'senha' => 'senha123',
        'cnpj' => '12345678000199',
    ];

    $this->mockService
        ->shouldReceive('atualizar')
        ->once()
        ->with(999, $dados)
        ->andThrow(new Illuminate\Database\Eloquent\ModelNotFoundException);

    $response = $this->putJson('/api/gestores/999', $dados);

    $response->assertNotFound()
        ->assertJsonFragment(['error' => 'Gestor não encontrado.']);
});

test('deve deletar gestor com sucesso', function () {
    $mockGestor = Mockery::mock(Gestor::class)->makePartial();
    $mockGestor->id_gestor = 5;
    $mockGestor->nome = 'Apagar';

    $this->mockService
        ->shouldReceive('buscarPorId')
        ->once()
        ->with(5)
        ->andReturn($mockGestor);

    $this->mockService
        ->shouldReceive('remover')
        ->once()
        ->with(5)
        ->andReturnTrue();

    $response = $this->deleteJson('/api/gestores/5');

    $response->assertOk()
        ->assertJsonFragment(['message' => 'Gestor removido com sucesso.']);
});

test('deve retornar erro se gestor não existir ao deletar', function () {
    $this->mockService
        ->shouldReceive('buscarPorId')
        ->once()
        ->with(999)
        ->andThrow(new Illuminate\Database\Eloquent\ModelNotFoundException);

    $response = $this->deleteJson('/api/gestores/999');

    $response->assertNotFound()
        ->assertJsonFragment(['error' => 'Gestor não encontrado.']);
});
