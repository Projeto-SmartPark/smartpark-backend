<?php

use App\Modules\Reserva\ReservaService;

beforeEach(function () {
    $this->service = new ReservaService;
});

/**
 * Datasets
 */
dataset('reservas_validas', fn () => array_map(
    fn ($v) => [$v],
    (require __DIR__.'/../Datasets/parametrosReservaService.php')['validos']
));

dataset('reservas_invalidas', fn () => array_map(
    fn ($v) => [$v],
    (require __DIR__.'/../Datasets/parametrosReservaService.php')['invalidos']
));

/**
 * Testes estruturais (sem mock, apenas validação de dataset)
 */
test('deve validar reservas válidas', function ($dados) {
    expect($dados)->toBeArray()
        ->toHaveKeys([
            'data',
            'hora_inicio',
            'hora_fim',
            'status',
            'cliente_id',
            'veiculo_id',
            'vaga_id',
        ]);

    // Data deve ser string não vazia (ex: "2025-11-02")
    expect($dados['data'])->toBeString()->not->toBeEmpty();

    // Hora de início e fim devem estar no formato HH:MM:SS
    expect($dados['hora_inicio'])->toMatch('/^\d{2}:\d{2}:\d{2}$/');
    expect($dados['hora_fim'])->toMatch('/^\d{2}:\d{2}:\d{2}$/');

    // Status deve estar dentro do conjunto permitido
    $statusValidos = ['ativa', 'cancelada', 'concluida', 'expirada'];
    expect($dados['status'])->toBeIn($statusValidos);

    // IDs devem ser inteiros positivos
    expect($dados['cliente_id'])->toBeInt()->toBeGreaterThan(0);
    expect($dados['veiculo_id'])->toBeInt()->toBeGreaterThan(0);
    expect($dados['vaga_id'])->toBeInt()->toBeGreaterThan(0);
})->with('reservas_validas');

test('deve validar reservas inválidas', function ($dados) {
    expect($dados)->toBeArray();

    // Data ausente ou formato incorreto
    if (empty($dados['data']) || ! preg_match('/^\d{4}-\d{2}-\d{2}$/', $dados['data'])) {
        expect($dados['data'] ?? null)->not->toMatch('/^\d{4}-\d{2}-\d{2}$/');
    }

    // Hora início/fim ausente ou inválida
    foreach (['hora_inicio', 'hora_fim'] as $campo) {
        if (empty($dados[$campo]) || ! preg_match('/^\d{2}:\d{2}:\d{2}$/', $dados[$campo])) {
            expect($dados[$campo] ?? null)->not->toMatch('/^\d{2}:\d{2}:\d{2}$/');
        }
    }

    // Status fora do conjunto permitido
    $statusValidos = ['ativa', 'cancelada', 'concluida', 'expirada'];
    if (! in_array($dados['status'], $statusValidos)) {
        expect($dados['status'])->not->toBeIn($statusValidos);
    }

    // IDs ausentes ou não inteiros
    foreach (['cliente_id', 'veiculo_id', 'vaga_id'] as $campo) {
        if (empty($dados[$campo]) || ! is_int($dados[$campo]) || $dados[$campo] <= 0) {
            expect($dados[$campo] ?? null)->not->toBeInt();
        }
    }
})->with('reservas_invalidas');
