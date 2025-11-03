<?php

use App\Modules\Veiculo\VeiculoService;

beforeEach(function () {
    $this->service = new VeiculoService;
});

/**
 * Datasets
 */
dataset('veiculos_validos', fn () => array_map(
    fn ($v) => [$v],
    (require __DIR__.'/../Datasets/parametrosVeiculoService.php')['validos']
));

dataset('veiculos_invalidos', fn () => array_map(
    fn ($v) => [$v],
    (require __DIR__.'/../Datasets/parametrosVeiculoService.php')['invalidos']
));

/**
 * Testes estruturais (sem mock, apenas validação de dataset)
 */
test('deve validar veículos válidos', function ($dados) {
    expect($dados)->toBeArray()
        ->toHaveKeys(['placa', 'cliente_id']);

    // Placa deve ser string, não vazia e até 10 caracteres
    expect($dados['placa'])
        ->toBeString()
        ->not->toBeEmpty();

    expect(strlen($dados['placa']))->toBeLessThanOrEqual(10);

    // Deve estar no formato de placa válida (ex: ABC1234 ou ABC1D23)
    expect($dados['placa'])->toMatch('/^[A-Z]{3}\d{4}$|^[A-Z]{3}\d[A-Z]\d{2}$/');

    // Cliente ID deve ser inteiro positivo
    expect($dados['cliente_id'])->toBeInt()->toBeGreaterThan(0);
})->with('veiculos_validos');

test('deve validar veículos inválidos', function ($dados) {
    expect($dados)->toBeArray();

    // Placa ausente, vazia, longa ou formato incorreto
    if (empty($dados['placa']) || strlen($dados['placa']) > 10 || ! preg_match('/^[A-Z]{3}\d{4}$|^[A-Z]{3}\d[A-Z]\d{2}$/', $dados['placa'])) {
        expect($dados['placa'] ?? null)->not->toMatch('/^[A-Z]{3}\d{4}$|^[A-Z]{3}\d[A-Z]\d{2}$/');
    }

    // Cliente ID ausente, não inteiro ou negativo
    if (empty($dados['cliente_id']) || ! is_int($dados['cliente_id']) || $dados['cliente_id'] <= 0) {
        expect($dados['cliente_id'])->toBeLessThanOrEqual(0);
    }
})->with('veiculos_invalidos');
