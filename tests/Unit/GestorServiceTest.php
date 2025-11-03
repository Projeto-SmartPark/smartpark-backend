<?php

use App\Modules\Usuarios\Services\GestorService;

beforeEach(function () {
    $this->service = new GestorService;
});

/**
 * Datasets
 */
dataset('gestores_validos', fn () => array_map(
    fn ($v) => [$v],
    (require __DIR__.'/../Datasets/parametrosGestorService.php')['validos']
));

dataset('gestores_invalidos', fn () => array_map(
    fn ($v) => [$v],
    (require __DIR__.'/../Datasets/parametrosGestorService.php')['invalidos']
));

/**
 * Testes
 */
test('deve validar gestores válidos', function ($dados) {
    expect($dados)->toBeArray()
        ->toHaveKeys(['nome', 'email', 'senha', 'cnpj']);

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

    // CNPJ
    expect($dados['cnpj'])
        ->toMatch('/^\d{14,20}$/');
})->with('gestores_validos');

test('deve validar gestores inválidos', function ($dados) {
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

    if (empty($dados['cnpj']) || ! preg_match('/^\d{14,20}$/', $dados['cnpj'])) {
        expect($dados['cnpj'] ?? '')->not->toMatch('/^\d{14,20}$/');
    }
})->with('gestores_invalidos');
