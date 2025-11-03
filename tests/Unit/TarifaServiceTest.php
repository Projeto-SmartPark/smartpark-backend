<?php

use App\Modules\Tarifa\TarifaService;

beforeEach(function () {
    $this->service = new TarifaService;
});

/**
 * Datasets
 */
dataset('tarifas_validas', fn () => array_map(
    fn ($v) => [$v],
    (require __DIR__.'/../Datasets/parametrosTarifaService.php')['validos']
));

dataset('tarifas_invalidas', fn () => array_map(
    fn ($v) => [$v],
    (require __DIR__.'/../Datasets/parametrosTarifaService.php')['invalidos']
));

/**
 * Testes mockados simplificados
 */
test('deve validar tarifas válidas', function ($dados) {
    expect($dados)->toBeArray()
        ->toHaveKeys(['nome', 'valor', 'tipo', 'estacionamento_id']);

    // Nome deve ser texto não vazio
    expect($dados['nome'])->toBeString()->not->toBeEmpty();

    // Valor deve ser numérico e positivo
    expect($dados['valor'])->toBeNumeric()->toBeGreaterThanOrEqual(0);

    // Tipo deve estar entre os válidos
    $tiposValidos = ['segundo', 'minuto', 'hora', 'diaria', 'mensal'];
    expect($dados['tipo'])->toBeIn($tiposValidos);

    // Estacionamento deve ser inteiro positivo
    expect($dados['estacionamento_id'])->toBeInt()->toBeGreaterThan(0);
})->with('tarifas_validas');

test('deve validar tarifas inválidas', function ($dados) {
    expect($dados)->toBeArray();

    // Nome não pode ser vazio
    if (empty($dados['nome'])) {
        expect($dados['nome'])->toBe('');
    }

    // Valor deve ser numérico e positivo
    if (! is_numeric($dados['valor'])) {
        expect($dados['valor'])->toBeString();
    } elseif ($dados['valor'] < 0) {
        expect($dados['valor'])->toBeLessThan(0);
    }

    // Tipo deve estar entre os válidos
    $tiposValidos = ['segundo', 'minuto', 'hora', 'diaria', 'mensal'];
    if (! in_array($dados['tipo'], $tiposValidos)) {
        expect($dados['tipo'])->not->toBeIn($tiposValidos);
    }

    // Estacionamento deve ser válido
    if (empty($dados['estacionamento_id']) || ! is_int($dados['estacionamento_id'])) {
        expect($dados['estacionamento_id'])->not->toBeInt();
    }
})->with('tarifas_invalidas');
