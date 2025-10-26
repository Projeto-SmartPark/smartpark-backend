<?php

use App\Modules\Usuarios\Models\Gestor;
use App\Modules\Usuarios\Services\GestorService;

beforeEach(fn () => $this->service = new GestorService);

$parametros = require dirname(__DIR__).DIRECTORY_SEPARATOR.'Datasets'.DIRECTORY_SEPARATOR.'parametrosGestorService.php';

// ========== TESTES DE CRIAÇÃO (criarGestor) ==========

test('criarGestor - dados válidos', function () use ($parametros) {
    foreach ($parametros['validos'] as $dados) {
        $gestor = $this->service->criarGestor($dados);
        expect($gestor)->toBeInstanceOf(Gestor::class)
            ->and($gestor->email)->toBe($dados['email'])
            ->and($gestor->nome)->toBe($dados['nome']);
    }
    expect(Gestor::count())->toBe(count($parametros['validos']));
});

test('criarGestor - dados inválidos', function () use ($parametros) {
    // Primeiro cria gestores válidos para testar duplicados
    foreach ($parametros['validos'] as $dados) {
        $this->service->criarGestor($dados);
    }

    foreach ($parametros['invalidos'] as $dados) {
        try {
            $this->service->criarGestor($dados);
        } catch (Exception|Throwable $excecao) {
            expect($excecao)->toBeInstanceOf(Throwable::class);
        }
    }
});

test('criarGestor - dados borda', function () use ($parametros) {
    foreach ($parametros['borda'] as $dados) {
        try {
            $gestor = $this->service->criarGestor($dados);
            expect($gestor)->toBeInstanceOf(Gestor::class);
        } catch (Exception|Throwable $excecao) {
            expect($excecao)->toBeInstanceOf(Throwable::class);
        }
    }
});

// ========== TESTES DE LISTAGEM (listarTodos) ==========

test('listarTodos - com dados válidos', function () use ($parametros) {
    foreach ($parametros['validos'] as $dados) {
        $this->service->criarGestor($dados);
    }
    $gestores = $this->service->listarTodos();
    expect($gestores)->toHaveCount(count($parametros['validos']));
});

test('listarTodos - retorna vazio quando não há dados', function () {
    $gestores = $this->service->listarTodos();
    expect($gestores)->toBeEmpty();
});

// ========== TESTES DE BUSCA (buscarPorId) ==========

test('buscarPorId - dados válidos', function () use ($parametros) {
    $gestor = $this->service->criarGestor($parametros['validos'][0]);
    $gestorEncontrado = $this->service->buscarPorId($gestor->id_gestor);
    expect($gestorEncontrado)->toBeInstanceOf(Gestor::class)
        ->and($gestorEncontrado->email)->toBe($parametros['validos'][0]['email']);
});

test('buscarPorId - dados inválidos (ID inexistente)', function () use ($parametros) {
    expect(fn () => $this->service->buscarPorId($parametros['ids']['inexistente']))
        ->toThrow(Exception::class);
});

test('buscarPorId - dados borda (ID negativo)', function () use ($parametros) {
    expect(fn () => $this->service->buscarPorId($parametros['ids']['negativo']))
        ->toThrow(Exception::class);
});

// ========== TESTES DE ATUALIZAÇÃO (atualizar) ==========

test('atualizar - dados válidos', function () use ($parametros) {
    $gestor = $this->service->criarGestor($parametros['validos'][0]);

    foreach ($parametros['validos'] as $indice => $dados) {
        if ($indice === 0) {
            continue;
        }

        $gestorAtualizado = $this->service->atualizar($gestor->id_gestor, $dados);
        expect($gestorAtualizado->email)->toBe($dados['email'])
            ->and($gestorAtualizado->nome)->toBe($dados['nome']);
    }
});

test('atualizar - dados inválidos', function () use ($parametros) {
    $gestor = $this->service->criarGestor($parametros['validos'][0]);
    $outroGestor = $this->service->criarGestor($parametros['validos'][1]);

    foreach ($parametros['invalidos'] as $dados) {
        try {
            $this->service->atualizar($gestor->id_gestor, $dados);
        } catch (Exception|Throwable $excecao) {
            expect($excecao)->toBeInstanceOf(Throwable::class);
        }
    }
});

test('atualizar - dados borda', function () use ($parametros) {
    $gestor = $this->service->criarGestor($parametros['validos'][0]);

    foreach ($parametros['borda'] as $dados) {
        try {
            $gestorAtualizado = $this->service->atualizar($gestor->id_gestor, $dados);
            expect($gestorAtualizado)->toBeInstanceOf(Gestor::class);
        } catch (Exception|Throwable $excecao) {
            expect($excecao)->toBeInstanceOf(Throwable::class);
        }
    }
});

test('atualizar - ID inexistente', function () use ($parametros) {
    expect(fn () => $this->service->atualizar($parametros['ids']['inexistente'], $parametros['validos'][0]))
        ->toThrow(Exception::class);
});

// ========== TESTES DE REMOÇÃO (remover) ==========

test('remover - dados válidos', function () use ($parametros) {
    $gestor = $this->service->criarGestor($parametros['validos'][0]);
    $resultado = $this->service->remover($gestor->id_gestor);
    expect($resultado)->toBeTrue()
        ->and(Gestor::count())->toBe(0);
});

test('remover - dados inválidos (ID inexistente)', function () use ($parametros) {
    $resultado = $this->service->remover($parametros['ids']['inexistente']);
    expect($resultado)->toBeFalse();
});

test('remover - dados borda (ID negativo)', function () use ($parametros) {
    $resultado = $this->service->remover($parametros['ids']['negativo']);
    expect($resultado)->toBeFalse();
});
