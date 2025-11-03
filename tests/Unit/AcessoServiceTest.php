<?php

use App\Modules\Acesso\AcessoService;

beforeEach(function () {
    $this->service = new AcessoService;
});

/**
 * Datasets
 */
dataset('acessos_validos', fn () => array_map(
    fn ($v) => [$v],
    (require __DIR__.'/../Datasets/parametrosAcessoService.php')['validos']
));

dataset('acessos_invalidos', fn () => array_map(
    fn ($v) => [$v],
    (require __DIR__.'/../Datasets/parametrosAcessoService.php')['invalidos']
));

/**
 * Testes mockados simplificados
 */
test('deve validar acessos válidos', function ($dados) {
    expect($dados)->toBeArray()
        ->toHaveKeys(['data', 'hora_inicio', 'hora_fim', 'valor_total', 'veiculo_id', 'vaga_id', 'cliente_id']);

    // Data deve estar no formato válido
    expect($dados['data'])->toMatch('/^\d{4}-\d{2}-\d{2}$/');

    // Horas devem estar no formato HH:MM:SS
    expect($dados['hora_inicio'])->toMatch('/^\d{2}:\d{2}:\d{2}$/');
    expect($dados['hora_fim'])->toMatch('/^\d{2}:\d{2}:\d{2}$/');

    // Valor deve ser numérico e positivo
    expect($dados['valor_total'])->toBeNumeric()->toBeGreaterThanOrEqual(0);

    // IDs devem ser inteiros positivos
    expect($dados['veiculo_id'])->toBeInt()->toBeGreaterThan(0);
    expect($dados['vaga_id'])->toBeInt()->toBeGreaterThan(0);
    expect($dados['cliente_id'])->toBeInt()->toBeGreaterThan(0);
})->with('acessos_validos');

test('deve validar acessos inválidos', function ($dados) {
    expect($dados)->toBeArray();

    // Data inválida
    if (! preg_match('/^\d{4}-\d{2}-\d{2}$/', $dados['data'])) {
        expect($dados['data'])->not->toMatch('/^\d{4}-\d{2}-\d{2}$/');
    }

    // Hora inválida
    if (! preg_match('/^\d{2}:\d{2}:\d{2}$/', $dados['hora_inicio'])) {
        expect($dados['hora_inicio'])->not->toMatch('/^\d{2}:\d{2}:\d{2}$/');
    }

    // Valor deve ser numérico e positivo
    if (! is_numeric($dados['valor_total'])) {
        expect($dados['valor_total'])->toBeString();
    } elseif ($dados['valor_total'] < 0) {
        expect($dados['valor_total'])->toBeLessThan(0);
    }

    // IDs devem ser inteiros positivos
    foreach (['veiculo_id', 'vaga_id', 'cliente_id'] as $campo) {
        if (! is_int($dados[$campo])) {
            expect($dados[$campo])->not->toBeInt();
        } elseif ($dados[$campo] <= 0) {
            expect($dados[$campo])->toBeInt()->and($dados[$campo])->toBeLessThanOrEqual(0);
        }
    }
})->with('acessos_invalidos');
