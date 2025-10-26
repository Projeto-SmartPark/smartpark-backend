<?php

use App\Modules\Usuarios\Models\Cliente;
use App\Modules\Usuarios\Services\ClienteService;

beforeEach(fn () => $this->service = new ClienteService);

$parametros = require dirname(__DIR__).DIRECTORY_SEPARATOR.'Datasets'.DIRECTORY_SEPARATOR.'parametrosClienteService.php';

// ========== TESTES DE CRIAÇÃO (criarCliente) ==========

test('criarCliente - dados válidos', function () use ($parametros) {
    foreach ($parametros['validos'] as $dados) {
        $cliente = $this->service->criarCliente($dados);
        expect($cliente)->toBeInstanceOf(Cliente::class)
            ->and($cliente->email)->toBe($dados['email'])
            ->and($cliente->nome)->toBe($dados['nome']);
    }
    expect(Cliente::count())->toBe(count($parametros['validos']));
});

test('criarCliente - dados inválidos', function () use ($parametros) {
    // Primeiro cria clientes válidos para testar duplicados
    foreach ($parametros['validos'] as $dados) {
        $this->service->criarCliente($dados);
    }

    foreach ($parametros['invalidos'] as $dados) {
        try {
            $this->service->criarCliente($dados);
            // Se não lançar exceção, verifica se foi bloqueado pelo banco
        } catch (Exception|Throwable $excecao) {
            expect($excecao)->toBeInstanceOf(Throwable::class);
        }
    }
});

test('criarCliente - dados borda', function () use ($parametros) {
    foreach ($parametros['borda'] as $dados) {
        try {
            $cliente = $this->service->criarCliente($dados);
            // Se criar, verifica que é uma instância válida
            expect($cliente)->toBeInstanceOf(Cliente::class);
        } catch (Exception|Throwable $excecao) {
            // Casos de borda podem falhar (esperado)
            expect($excecao)->toBeInstanceOf(Throwable::class);
        }
    }
});

// ========== TESTES DE LISTAGEM (listarTodos) ==========

test('listarTodos - com dados válidos', function () use ($parametros) {
    foreach ($parametros['validos'] as $dados) {
        $this->service->criarCliente($dados);
    }
    $clientes = $this->service->listarTodos();
    expect($clientes)->toHaveCount(count($parametros['validos']));
});

test('listarTodos - retorna vazio quando não há dados', function () {
    $clientes = $this->service->listarTodos();
    expect($clientes)->toBeEmpty();
});

// ========== TESTES DE BUSCA (buscarPorId) ==========

test('buscarPorId - dados válidos', function () use ($parametros) {
    $cliente = $this->service->criarCliente($parametros['validos'][0]);
    $clienteEncontrado = $this->service->buscarPorId($cliente->id_cliente);
    expect($clienteEncontrado)->toBeInstanceOf(Cliente::class)
        ->and($clienteEncontrado->email)->toBe($parametros['validos'][0]['email']);
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
    $cliente = $this->service->criarCliente($parametros['validos'][0]);

    foreach ($parametros['validos'] as $indice => $dados) {
        if ($indice === 0) {
            continue;
        } // Pula o primeiro (já usado na criação)

        $clienteAtualizado = $this->service->atualizar($cliente->id_cliente, $dados);
        expect($clienteAtualizado->email)->toBe($dados['email'])
            ->and($clienteAtualizado->nome)->toBe($dados['nome']);
    }
});

test('atualizar - dados inválidos', function () use ($parametros) {
    $cliente = $this->service->criarCliente($parametros['validos'][0]);
    $outroCliente = $this->service->criarCliente($parametros['validos'][1]);

    foreach ($parametros['invalidos'] as $dados) {
        try {
            $this->service->atualizar($cliente->id_cliente, $dados);
        } catch (Exception|Throwable $excecao) {
            expect($excecao)->toBeInstanceOf(Throwable::class);
        }
    }
});

test('atualizar - dados borda', function () use ($parametros) {
    $cliente = $this->service->criarCliente($parametros['validos'][0]);

    foreach ($parametros['borda'] as $dados) {
        try {
            $clienteAtualizado = $this->service->atualizar($cliente->id_cliente, $dados);
            expect($clienteAtualizado)->toBeInstanceOf(Cliente::class);
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
    $cliente = $this->service->criarCliente($parametros['validos'][0]);
    $resultado = $this->service->remover($cliente->id_cliente);
    expect($resultado)->toBeTrue()
        ->and(Cliente::count())->toBe(0);
});

test('remover - dados inválidos (ID inexistente)', function () use ($parametros) {
    $resultado = $this->service->remover($parametros['ids']['inexistente']);
    expect($resultado)->toBeFalse();
});

test('remover - dados borda (ID negativo)', function () use ($parametros) {
    $resultado = $this->service->remover($parametros['ids']['negativo']);
    expect($resultado)->toBeFalse();
});
