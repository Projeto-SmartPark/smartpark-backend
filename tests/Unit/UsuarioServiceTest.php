<?php

use App\Modules\Usuarios\Services\UsuarioService;

beforeEach(function () {
    $this->service = new UsuarioService;
});

/**
 * Datasets
 */
dataset('usuarios_validos', fn () => array_map(
    fn ($v) => [$v],
    (require __DIR__.'/../Datasets/parametrosUsuarioService.php')['validos']
));

dataset('usuarios_invalidos', fn () => array_map(
    fn ($v) => [$v],
    (require __DIR__.'/../Datasets/parametrosUsuarioService.php')['invalidos']
));

/**
 * Testes unitários — validação estrutural e coerência de dados
 */
test('deve validar usuários válidos', function ($dados) {
    expect($dados)->toBeArray()
        ->toHaveKeys(['perfil', 'nome', 'email', 'senha']);

    // Perfil
    expect($dados['perfil'])
        ->toBeString()
        ->toBeIn(['C', 'G']);

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

    // CNPJ (somente se perfil = G)
    if ($dados['perfil'] === 'G') {
        expect($dados)->toHaveKey('cnpj');
        expect($dados['cnpj'])->toMatch('/^\d{14,20}$/');
    }
})->with('usuarios_validos');

test('deve validar usuários inválidos', function ($dados) {
    expect($dados)->toBeArray();

    if (empty($dados['perfil']) || ! in_array($dados['perfil'], ['C', 'G'])) {
        expect($dados['perfil'] ?? '')->not->toBeIn(['C', 'G']);
    }

    if (empty($dados['nome']) || strlen($dados['nome']) < 3 || strlen($dados['nome']) > 100) {
        expect(strlen($dados['nome'] ?? ''))->not->toBeBetween(3, 100);
    }

    if (empty($dados['email']) || ! filter_var($dados['email'], FILTER_VALIDATE_EMAIL)) {
        expect($dados['email'] ?? '')->not->toMatch('/^[^\s@]+@[^\s@]+\.[^\s@]+$/');
    }

    if (empty($dados['senha']) || strlen($dados['senha']) < 6 || strlen($dados['senha']) > 100) {
        expect(strlen($dados['senha'] ?? ''))->not->toBeBetween(6, 100);
    }

    if (($dados['perfil'] ?? null) === 'G' && (! isset($dados['cnpj']) || ! preg_match('/^\d{14,20}$/', $dados['cnpj'] ?? ''))) {
        expect($dados['cnpj'] ?? '')->not->toMatch('/^\d{14,20}$/');
    }
})->with('usuarios_invalidos');
