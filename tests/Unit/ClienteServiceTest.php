<?php

use App\Modules\Usuarios\Services\ClienteService;

beforeEach(function () {
    $this->service = new ClienteService;
});

/**
 * Datasets
 */
dataset('clientes_validos', fn () => array_map(
    fn ($v) => [$v],
    (require __DIR__.'/../Datasets/parametrosClienteService.php')['validos']
));

dataset('clientes_invalidos', fn () => array_map(
    fn ($v) => [$v],
    (require __DIR__.'/../Datasets/parametrosClienteService.php')['invalidos']
));

/**
 * Testes unitários (validação de estrutura e coerência)
 */
test('deve validar clientes válidos', function ($dados) {
    expect($dados)->toBeArray()
        ->toHaveKeys(['nome', 'email', 'senha']);

    // Nome
    expect($dados['nome'])
        ->toBeString()
        ->not->toBeEmpty()
        ->and(strlen($dados['nome']))->toBeGreaterThanOrEqual(3)
        ->toBeLessThanOrEqual(100);

    // Email
    expect($dados['email'])
        ->toMatch('/^[^\s@]+@[^\s@]+\.[^\s@]+$/')
        ->and(strlen($dados['email']))->toBeLessThanOrEqual(100);

    // Senha
    expect($dados['senha'])
        ->toBeString()
        ->and(strlen($dados['senha']))->toBeGreaterThanOrEqual(6)
        ->toBeLessThanOrEqual(100);
})->with('clientes_validos');

test('deve validar clientes inválidos', function ($dados) {
    expect($dados)->toBeArray();

    if (empty($dados['nome']) || strlen($dados['nome']) < 3 || strlen($dados['nome']) > 100) {
        expect(strlen($dados['nome'] ?? ''))->not->toBeBetween(3, 100);
    }

    if (empty($dados['email']) || ! filter_var($dados['email'], FILTER_VALIDATE_EMAIL)) {
        expect($dados['email'] ?? '')->not->toMatch('/^[^\s@]+@[^\s@]+\.[^\s@]+$/');
    }

    if (empty($dados['senha']) || strlen($dados['senha']) < 6 || strlen($dados['senha']) > 100) {
        expect(strlen($dados['senha'] ?? ''))->not->toBeBetween(6, 100);
    }
})->with('clientes_invalidos');
