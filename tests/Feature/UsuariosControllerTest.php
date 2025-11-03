<?php

use App\Modules\Usuarios\Services\UsuarioService;

beforeEach(function () {
    $this->withoutMiddleware();
    $this->mockService = Mockery::mock(UsuarioService::class);
    $this->app->instance(UsuarioService::class, $this->mockService);
});

afterEach(function () {
    Mockery::close();
});

test('deve listar todos os usuários', function () {
    $this->mockService
        ->shouldReceive('listarTodos')
        ->once()
        ->andReturn([
            'clientes' => [
                ['id_cliente' => 1, 'nome' => 'João Silva', 'email' => 'joao@teste.com'],
            ],
            'gestores' => [
                ['id_gestor' => 2, 'nome' => 'Maria Santos', 'email' => 'maria@empresa.com'],
            ],
        ]);

    $response = $this->getJson('/api/usuarios');

    $response->assertOk()
        ->assertJsonFragment(['nome' => 'João Silva'])
        ->assertJsonFragment(['nome' => 'Maria Santos']);
});

test('deve criar usuário com sucesso', function () {
    $dados = [
        'perfil' => 'C',
        'nome' => 'Carlos Souza',
        'email' => 'carlos@teste.com',
        'senha' => 'senha123',
    ];

    $resultado = [
        'message' => 'Usuário criado com sucesso.',
        'id_usuario' => 10,
    ];

    $this->mockService
        ->shouldReceive('criarUsuario')
        ->once()
        ->with($dados)
        ->andReturn($resultado);

    $response = $this->postJson('/api/usuarios', $dados);

    $response->assertCreated()
        ->assertJsonFragment(['message' => 'Usuário criado com sucesso.']);
});

test('deve rejeitar dados inválidos ao criar usuário', function () {
    $dados = [
        'perfil' => '',
        'nome' => '',
        'email' => '',
        'senha' => '',
    ];

    $response = $this->postJson('/api/usuarios', $dados);

    $response->assertStatus(422)
        ->assertJsonStructure(['message', 'errors']);
});

test('deve buscar usuário por id', function () {
    $usuario = [
        'id_usuario' => 5,
        'perfil' => 'G',
        'dados' => [
            'nome' => 'Mariana Oliveira',
            'email' => 'mariana@corp.com',
            'cnpj' => '12345678000190',
        ],
    ];

    $this->mockService
        ->shouldReceive('buscarPorId')
        ->once()
        ->with(5)
        ->andReturn($usuario);

    $response = $this->getJson('/api/usuarios/5');

    $response->assertOk()
        ->assertJsonFragment(['perfil' => 'G'])
        ->assertJsonFragment(['nome' => 'Mariana Oliveira']);
});

test('deve retornar erro se usuário não existir', function () {
    $this->mockService
        ->shouldReceive('buscarPorId')
        ->once()
        ->with(999)
        ->andReturn(null);

    $response = $this->getJson('/api/usuarios/999');

    $response->assertNotFound()
        ->assertJsonFragment(['error' => 'Usuário não encontrado.']);
});

test('deve atualizar usuário com sucesso', function () {
    $dados = [
        'nome' => 'João Atualizado',
        'email' => 'joao@novo.com',
        'senha' => 'novaSenha',
    ];

    $this->mockService
        ->shouldReceive('atualizarUsuario')
        ->once()
        ->with(10, $dados)
        ->andReturnTrue();

    $response = $this->putJson('/api/usuarios/10', $dados);

    $response->assertOk()
        ->assertJsonFragment(['message' => 'Usuário atualizado com sucesso.']);
});

test('deve retornar erro ao atualizar usuário inexistente', function () {
    $dados = [
        'nome' => 'Fulano',
        'email' => 'fulano@teste.com',
        'senha' => 'senha123',
    ];

    $this->mockService
        ->shouldReceive('atualizarUsuario')
        ->once()
        ->with(999, $dados)
        ->andThrow(new Exception('Usuário não encontrado.'));

    $response = $this->putJson('/api/usuarios/999', $dados);

    $response->assertNotFound()
        ->assertJsonFragment(['error' => 'Usuário não encontrado.']);
});

test('deve deletar usuário com sucesso', function () {
    $this->mockService
        ->shouldReceive('remover')
        ->once()
        ->with(5)
        ->andReturnTrue();

    $response = $this->deleteJson('/api/usuarios/5');

    $response->assertOk()
        ->assertJsonFragment(['message' => 'Usuário removido com sucesso.']);
});

test('deve retornar erro se usuário não existir ao deletar', function () {
    $this->mockService
        ->shouldReceive('remover')
        ->once()
        ->with(999)
        ->andThrow(new Exception('Usuário não encontrado.'));

    $response = $this->deleteJson('/api/usuarios/999');

    $response->assertNotFound()
        ->assertJsonFragment(['error' => 'Usuário não encontrado.']);
});
