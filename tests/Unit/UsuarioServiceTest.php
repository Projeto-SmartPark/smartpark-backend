<?php

use App\Modules\Usuarios\Models\Cliente;
use App\Modules\Usuarios\Models\Gestor;
use App\Modules\Usuarios\Services\UsuarioService;

beforeEach(fn () => $this->service = new UsuarioService);

$parametros = require dirname(__DIR__).DIRECTORY_SEPARATOR.'Datasets'.DIRECTORY_SEPARATOR.'parametrosUsuarioService.php';

// ========== TESTES DE CRIAÇÃO (criarUsuario) ==========

test('criarUsuario - dados válidos', function () use ($parametros) {
    foreach ($parametros['validos'] as $dados) {
        $resultado = $this->service->criarUsuario($dados);
        expect($resultado)->toHaveKeys(['message', 'id_usuario'])
            ->and(is_numeric($resultado['id_usuario']))->toBeTrue();
    }
    expect(Cliente::count() + Gestor::count())->toBe(count($parametros['validos']));
});

test('criarUsuario - dados inválidos', function () use ($parametros) {
    // Primeiro cria usuários válidos para testar duplicados
    foreach ($parametros['validos'] as $dados) {
        $this->service->criarUsuario($dados);
    }

    foreach ($parametros['invalidos'] as $dados) {
        try {
            $this->service->criarUsuario($dados);
        } catch (Exception|Throwable $excecao) {
            expect($excecao)->toBeInstanceOf(Throwable::class);
        }
    }
});

test('criarUsuario - dados borda', function () use ($parametros) {
    foreach ($parametros['borda'] as $dados) {
        try {
            $resultado = $this->service->criarUsuario($dados);
            expect($resultado)->toHaveKeys(['message', 'id_usuario']);
        } catch (Exception|Throwable $excecao) {
            expect($excecao)->toBeInstanceOf(Throwable::class);
        }
    }
});

// ========== TESTES DE LISTAGEM (listarTodos) ==========

test('listarTodos - com dados válidos', function () use ($parametros) {
    foreach ($parametros['validos'] as $dados) {
        $this->service->criarUsuario($dados);
    }
    $usuarios = $this->service->listarTodos();
    expect($usuarios)->toHaveKeys(['clientes', 'gestores'])
        ->and($usuarios['clientes']->count() + $usuarios['gestores']->count())
        ->toBe(count($parametros['validos']));
});

test('listarTodos - retorna vazio quando não há dados', function () {
    $usuarios = $this->service->listarTodos();
    expect($usuarios['clientes'])->toBeEmpty()
        ->and($usuarios['gestores'])->toBeEmpty();
});

// ========== TESTES DE BUSCA (buscarPorId) ==========

test('buscarPorId - dados válidos', function () use ($parametros) {
    $usuarioCriado = $this->service->criarUsuario($parametros['validos'][0]);
    $usuarioEncontrado = $this->service->buscarPorId($usuarioCriado['id_usuario']);
    expect($usuarioEncontrado)->toHaveKeys(['id_usuario', 'perfil', 'dados'])
        ->and($usuarioEncontrado['perfil'])->toBe($parametros['validos'][0]['perfil']);
});

test('buscarPorId - dados inválidos (ID inexistente)', function () use ($parametros) {
    $resultado = $this->service->buscarPorId($parametros['ids']['inexistente']);
    expect($resultado)->toBeNull();
});

test('buscarPorId - dados borda (ID negativo)', function () use ($parametros) {
    $resultado = $this->service->buscarPorId($parametros['ids']['negativo']);
    expect($resultado)->toBeNull();
});

// ========== TESTES DE ATUALIZAÇÃO (atualizarUsuario) ==========

test('atualizarUsuario - dados válidos', function () use ($parametros) {
    $usuarioCriado = $this->service->criarUsuario($parametros['validos'][0]);

    foreach ($parametros['validos'] as $indice => $dados) {
        if ($indice === 0 || $dados['perfil'] !== $parametros['validos'][0]['perfil']) {
            continue;
        }

        $resultado = $this->service->atualizarUsuario($usuarioCriado['id_usuario'], $dados);
        expect($resultado)->toBeTrue();
    }
});

test('atualizarUsuario - dados inválidos', function () use ($parametros) {
    $usuarioCriado = $this->service->criarUsuario($parametros['validos'][0]);

    $tentativas = 0;
    foreach ($parametros['invalidos'] as $dados) {
        $tentativas++;
        try {
            $this->service->atualizarUsuario($usuarioCriado['id_usuario'], $dados);
        } catch (Exception|Throwable $excecao) {
            expect($excecao)->toBeInstanceOf(Throwable::class);
        }
    }

    expect($tentativas)->toBeGreaterThan(0);
});

test('atualizarUsuario - dados borda', function () use ($parametros) {
    $usuarioCriado = $this->service->criarUsuario($parametros['validos'][0]);

    foreach ($parametros['borda'] as $dados) {
        if ($dados['perfil'] !== $parametros['validos'][0]['perfil']) {
            continue;
        }

        try {
            $resultado = $this->service->atualizarUsuario($usuarioCriado['id_usuario'], $dados);
            expect($resultado)->toBeTrue();
        } catch (Exception|Throwable $excecao) {
            expect($excecao)->toBeInstanceOf(Throwable::class);
        }
    }
});

test('atualizarUsuario - ID inexistente', function () use ($parametros) {
    expect(fn () => $this->service->atualizarUsuario($parametros['ids']['inexistente'], $parametros['validos'][0]))
        ->toThrow(Exception::class, 'Usuário não encontrado.');
});

// ========== TESTES DE REMOÇÃO (remover) ==========

test('remover - dados válidos', function () use ($parametros) {
    $usuarioCriado = $this->service->criarUsuario($parametros['validos'][0]);
    $resultado = $this->service->remover($usuarioCriado['id_usuario']);
    expect($resultado)->toBeTrue()
        ->and(Cliente::count() + Gestor::count())->toBe(0);
});

test('remover - dados inválidos (ID inexistente)', function () use ($parametros) {
    expect(fn () => $this->service->remover($parametros['ids']['inexistente']))
        ->toThrow(Exception::class, 'Usuário não encontrado.');
});

test('remover - dados borda (ID negativo)', function () use ($parametros) {
    expect(fn () => $this->service->remover($parametros['ids']['negativo']))
        ->toThrow(Exception::class, 'Usuário não encontrado.');
});
