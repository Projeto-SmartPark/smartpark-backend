<?php

use App\Modules\Endereco\Endereco;
use App\Modules\Endereco\EnderecoService;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Database\QueryException;

beforeEach(fn () => $this->service = new EnderecoService);

$parametros = require dirname(__DIR__, 2).DIRECTORY_SEPARATOR.'Datasets'.DIRECTORY_SEPARATOR.'parametrosEnderecoService.php';

// ========== TESTES DE CRIAÇÃO (criarEndereco) ==========

test('criarEndereco - dados válidos', function () use ($parametros) {
    foreach ($parametros['validos'] as $dados) {
        $enderecoCriado = $this->service->criarEndereco($dados);
        expect($enderecoCriado)->toBeInstanceOf(Endereco::class)
            ->and($enderecoCriado->cep)->toBe($dados['cep']);
        $this->assertDatabaseHas('enderecos', ['cep' => $dados['cep']]);
    }
    expect(Endereco::count())->toBe(count($parametros['validos']));
});

test('criarEndereco - dados inválidos', function () use ($parametros) {
    foreach ($parametros['invalidos'] as $dados) {
        try {
            $this->service->criarEndereco($dados);
        } catch (Exception|Throwable $excecao) {
            // Espera-se uma exceção de banco de dados devido a dados mal formatados (ex: string longa demais)
            expect($excecao)->toBeInstanceOf(QueryException::class);
        }
    }
    // Garante que nenhum endereço foi criado com dados inválidos
    expect(Endereco::count())->toBe(0);
});

test('criarEndereco - dados borda', function () use ($parametros) {
    foreach ($parametros['borda'] as $dados) {
        try {
            $enderecoCriado = $this->service->criarEndereco($dados);
            // Casos de borda que são válidos devem criar com sucesso
            expect($enderecoCriado)->toBeInstanceOf(Endereco::class);
        } catch (Exception|Throwable $excecao) {
            // Casos de borda que são inválidos (ex: campos obrigatórios vazios) devem lançar exceção
            expect($excecao)->toBeInstanceOf(QueryException::class);
        }
    }
});

// ========== TESTES DE LISTAGEM (listarEnderecos) ==========

test('listarEnderecos - com dados válidos', function () use ($parametros) {
    foreach ($parametros['validos'] as $dados) {
        $this->service->criarEndereco($dados);
    }
    $enderecos = $this->service->listarEnderecos();
    expect($enderecos)->toHaveCount(count($parametros['validos']));
});

test('listarEnderecos - retorna vazio quando não há dados', function () {
    $enderecos = $this->service->listarEnderecos();
    expect($enderecos)->toBeEmpty();
});

// ========== TESTES DE BUSCA (buscarEnderecoPorId) ==========

test('buscarEnderecoPorId - dado válido', function () use ($parametros) {
    $enderecoCriado = $this->service->criarEndereco($parametros['validos'][0]);
    $enderecoEncontrado = $this->service->buscarEnderecoPorId($enderecoCriado->id_endereco);
    expect($enderecoEncontrado)->toBeInstanceOf(Endereco::class)
        ->and($enderecoEncontrado->id_endereco)->toBe($enderecoCriado->id_endereco);
});

test('buscarEnderecoPorId - ID inexistente', function () use ($parametros) {
    expect(fn () => $this->service->buscarEnderecoPorId($parametros['ids']['inexistente']))
        ->toThrow(ModelNotFoundException::class);
});

test('buscarEnderecoPorId - IDs de borda', function () use ($parametros) {
    expect(fn () => $this->service->buscarEnderecoPorId($parametros['ids']['negativo']))
        ->toThrow(ModelNotFoundException::class);
    expect(fn () => $this->service->buscarEnderecoPorId($parametros['ids']['zero']))
        ->toThrow(ModelNotFoundException::class);
});

// ========== TESTES DE ATUALIZAÇÃO (atualizarEndereco) ==========

test('atualizarEndereco - dados válidos', function () use ($parametros) {
    $enderecoCriado = $this->service->criarEndereco($parametros['validos'][0]);
    $dadosParaAtualizar = $parametros['validos'][1];

    $enderecoAtualizado = $this->service->atualizarEndereco($enderecoCriado->id_endereco, $dadosParaAtualizar);

    expect($enderecoAtualizado->cep)->toBe($dadosParaAtualizar['cep']);
    $this->assertDatabaseHas('enderecos', ['id_endereco' => $enderecoCriado->id_endereco, 'cep' => $dadosParaAtualizar['cep']]);
});

test('atualizarEndereco - ID inexistente', function () use ($parametros) {
    $dadosParaAtualizar = $parametros['validos'][0];
    expect(fn () => $this->service->atualizarEndereco($parametros['ids']['inexistente'], $dadosParaAtualizar))
        ->toThrow(ModelNotFoundException::class);
});

test('atualizarEndereco - dados inválidos', function () use ($parametros) {
    $enderecoCriado = $this->service->criarEndereco($parametros['validos'][0]);

    foreach ($parametros['invalidos'] as $dados) {
        try {
            $this->service->atualizarEndereco($enderecoCriado->id_endereco, $dados);
        } catch (Exception|Throwable $excecao) {
            expect($excecao)->toBeInstanceOf(QueryException::class);
        }
    }
});

// ========== TESTES DE REMOÇÃO (deletarEndereco) ==========

test('deletarEndereco - dado válido', function () use ($parametros) {
    $enderecoCriado = $this->service->criarEndereco($parametros['validos'][0]);
    $resultado = $this->service->deletarEndereco($enderecoCriado->id_endereco);
    expect($resultado)->toBeTrue();
    $this->assertDatabaseMissing('enderecos', ['id_endereco' => $enderecoCriado->id_endereco]);
});

test('deletarEndereco - ID inexistente', function () use ($parametros) {
    expect(fn () => $this->service->deletarEndereco($parametros['ids']['inexistente']))
        ->toThrow(ModelNotFoundException::class);
});

test('deletarEndereco - IDs de borda', function () use ($parametros) {
    expect(fn () => $this->service->deletarEndereco($parametros['ids']['negativo']))
        ->toThrow(ModelNotFoundException::class);
    expect(fn () => $this->service->deletarEndereco($parametros['ids']['zero']))
        ->toThrow(ModelNotFoundException::class);
});
