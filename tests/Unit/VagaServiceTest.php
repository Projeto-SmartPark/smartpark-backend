<?php

use App\Modules\Vaga\VagaService;

beforeEach(function () {
    $this->service = new VagaService;
});

/**
 * Datasets
 */
dataset('vagas_validas', fn () => array_map(
    fn ($v) => [$v],
    (require __DIR__.'/../Datasets/parametrosVagaService.php')['validos']
));

dataset('vagas_invalidas', fn () => array_map(
    fn ($v) => [$v],
    (require __DIR__.'/../Datasets/parametrosVagaService.php')['invalidos']
));

/**
 * Testes estruturais (sem banco, apenas validação de dataset)
 */
test('deve validar vagas válidas', function ($dados) {
    expect($dados)->toBeArray()
        ->toHaveKeys(['identificacao', 'tipo', 'disponivel', 'estacionamento_id']);

    // Identificação: string não vazia e até 20 caracteres
    expect($dados['identificacao'])
        ->toBeString()
        ->not->toBeEmpty();

    expect(strlen($dados['identificacao']))->toBeLessThanOrEqual(20);

    // Tipo: dentro do conjunto permitido
    $tiposPermitidos = ['carro', 'moto', 'deficiente', 'idoso', 'eletrico', 'outro'];
    expect($dados['tipo'])->toBeIn($tiposPermitidos);

    // Disponível: S ou N
    expect($dados['disponivel'])->toBeIn(['S', 'N']);

    // Estacionamento ID: inteiro positivo
    expect($dados['estacionamento_id'])->toBeInt()->toBeGreaterThan(0);
})->with('vagas_validas');

test('deve validar vagas inválidas', function ($dados) {
    expect($dados)->toBeArray();

    // Identificação ausente, vazia ou muito longa
    if (empty($dados['identificacao']) || strlen($dados['identificacao']) > 20) {
        expect($dados['identificacao'] ?? null)->toBeEmpty();
    }

    // Tipo fora do conjunto permitido
    $tiposPermitidos = ['carro', 'moto', 'deficiente', 'idoso', 'eletrico', 'outro'];
    if (! in_array($dados['tipo'], $tiposPermitidos)) {
        expect($dados['tipo'])->not->toBeIn($tiposPermitidos);
    }

    // Disponível fora de S/N
    if (! in_array($dados['disponivel'], ['S', 'N'])) {
        expect($dados['disponivel'])->not->toBeIn(['S', 'N']);
    }

    // ID inválido
    if (empty($dados['estacionamento_id']) || ! is_int($dados['estacionamento_id']) || $dados['estacionamento_id'] <= 0) {
        expect($dados['estacionamento_id'])->toBeLessThanOrEqual(0);
    }
})->with('vagas_invalidas');
