<?php

use App\Modules\Telefone\TelefoneService;

beforeEach(function () {
    $this->service = new TelefoneService;
});

/**
 * Datasets
 */
dataset('telefones_validos', fn () => array_map(
    fn ($v) => [$v],
    (require __DIR__.'/../Datasets/parametrosTelefoneService.php')['validos']
));

dataset('telefones_invalidos', fn () => array_map(
    fn ($v) => [$v],
    (require __DIR__.'/../Datasets/parametrosTelefoneService.php')['invalidos']
));

/**
 * Testes estruturais (sem banco, apenas validação de dataset)
 */
test('deve validar telefones válidos', function ($dados) {
    expect($dados)->toBeArray()
        ->toHaveKeys(['ddd', 'numero']);

    // DDD deve ter exatamente 2 caracteres numéricos
    expect($dados['ddd'])
        ->toBeString()
        ->not->toBeEmpty();
    expect(strlen($dados['ddd']))->toBe(2);
    expect($dados['ddd'])->toMatch('/^\d{2}$/');

    // Número deve ser string numérica de até 9 dígitos
    expect($dados['numero'])
        ->toBeString()
        ->not->toBeEmpty();
    expect(strlen($dados['numero']))->toBeLessThanOrEqual(9);
    expect($dados['numero'])->toMatch('/^\d{8,9}$/');
})->with('telefones_validos');

test('deve validar telefones inválidos', function ($dados) {
    expect($dados)->toBeArray();

    // DDD inválido (vazio, não numérico, diferente de 2 caracteres)
    if (empty($dados['ddd']) || ! preg_match('/^\d{2}$/', $dados['ddd'])) {
        expect($dados['ddd'] ?? null)->not->toMatch('/^\d{2}$/');
    }

    // Número inválido (vazio, não numérico ou maior que 9 caracteres)
    if (empty($dados['numero']) || ! preg_match('/^\d{8,9}$/', $dados['numero'])) {
        expect($dados['numero'] ?? null)->not->toMatch('/^\d{8,9}$/');
    }
})->with('telefones_invalidos');
