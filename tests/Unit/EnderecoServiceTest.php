<?php

use App\Modules\Endereco\EnderecoService;

beforeEach(function () {
    $this->service = new EnderecoService;
});

/**
 * Datasets
 */
dataset('enderecos_validos', fn () => array_map(
    fn ($v) => [$v],
    (require __DIR__.'/../Datasets/parametrosEnderecoService.php')['validos']
));

dataset('enderecos_invalidos', fn () => array_map(
    fn ($v) => [$v],
    (require __DIR__.'/../Datasets/parametrosEnderecoService.php')['invalidos']
));

/**
 * Testes
 */
test('deve validar endereços válidos', function ($dados) {
    expect($dados)->toBeArray()
        ->toHaveKeys([
            'cep', 'estado', 'cidade', 'bairro',
            'numero', 'logradouro',
        ]);

    expect($dados['cep'])->toMatch('/^\d{8}$/');
    expect($dados['estado'])->toMatch('/^[A-Z]{2}$/');
    expect($dados['cidade'])->toBeString()->not->toBeEmpty();
    expect($dados['bairro'])->toBeString()->not->toBeEmpty();
    expect($dados['numero'])->toBeString()->not->toBeEmpty();
    expect($dados['logradouro'])->toBeString()->not->toBeEmpty();

    if (isset($dados['latitude'])) {
        expect($dados['latitude'])->toBeFloat();
    }
    if (isset($dados['longitude'])) {
        expect($dados['longitude'])->toBeFloat();
    }
})->with('enderecos_validos');

test('deve validar endereços inválidos', function ($dados) {
    expect($dados)->toBeArray();

    if (empty($dados['cep']) || strlen($dados['cep']) !== 8) {
        expect($dados['cep'] ?? '')->not->toMatch('/^\d{8}$/');
    }

    if (empty($dados['estado']) || strlen($dados['estado']) !== 2) {
        expect(strlen($dados['estado'] ?? ''))->not->toBe(2);
    }

    if (empty($dados['cidade']) || strlen($dados['cidade']) > 80) {
        expect($dados['cidade'] ?? '')->toBeString();
    }

    if (empty($dados['bairro']) || strlen($dados['bairro']) > 80) {
        expect($dados['bairro'] ?? '')->toBeString();
    }

    if (empty($dados['numero']) || strlen($dados['numero']) > 10) {
        expect($dados['numero'] ?? '')->toBeString();
    }

    if (empty($dados['logradouro']) || strlen($dados['logradouro']) > 120) {
        expect($dados['logradouro'] ?? '')->toBeString();
    }

    if (isset($dados['latitude']) && ! is_numeric($dados['latitude'])) {
        expect($dados['latitude'])->not->toBeFloat();
    }

    if (isset($dados['longitude']) && ! is_numeric($dados['longitude'])) {
        expect($dados['longitude'])->not->toBeFloat();
    }
})->with('enderecos_invalidos');
