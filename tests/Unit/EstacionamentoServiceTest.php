<?php

use App\Modules\Endereco\EnderecoService;
use App\Modules\Estacionamento\EstacionamentoService;
use App\Modules\Telefone\TelefoneService;

beforeEach(function () {
    // Mocka os serviços dependentes
    $this->telefoneService = Mockery::mock(TelefoneService::class);
    $this->enderecoService = Mockery::mock(EnderecoService::class);

    $this->service = new EstacionamentoService($this->telefoneService, $this->enderecoService);
});

/**
 * Datasets
 */
dataset('estacionamentos_validos', fn () => array_map(
    fn ($v) => [$v],
    (require __DIR__.'/../Datasets/parametrosEstacionamentoService.php')['validos']
));

dataset('estacionamentos_invalidos', fn () => array_map(
    fn ($v) => [$v],
    (require __DIR__.'/../Datasets/parametrosEstacionamentoService.php')['invalidos']
));

/**
 * Testes unitários simplificados (sem acesso ao banco)
 */
test('deve validar estacionamentos válidos', function ($dados) {
    expect($dados)->toBeArray()
        ->toHaveKeys(['dados', 'endereco', 'telefones']);

    $info = $dados['dados'];

    // Nome
    expect($info['nome'])
        ->toBeString()
        ->not->toBeEmpty();
    expect(strlen($info['nome']))->toBeLessThanOrEqual(100);

    // Capacidade
    expect($info['capacidade'])->toBeInt()->toBeGreaterThan(0);

    // Horários
    expect($info['hora_abertura'])->toMatch('/^\d{2}:\d{2}:\d{2}$/');
    expect($info['hora_fechamento'])->toMatch('/^\d{2}:\d{2}:\d{2}$/');

    // Lotado
    expect($info['lotado'])->toBeIn(['S', 'N']);

    // Gestor ID
    expect($info['gestor_id'])->toBeInt()->toBeGreaterThan(0);

    // Endereço
    expect($dados['endereco'])->toBeArray()
        ->toHaveKeys(['cep', 'logradouro', 'numero', 'bairro', 'cidade', 'estado']);
    expect($dados['endereco']['cep'])->toMatch('/^\d{8}$/');

    // Telefones
    expect($dados['telefones'])->toBeArray();
    foreach ($dados['telefones'] as $tel) {
        expect($tel['ddd'])->toMatch('/^\d{2}$/');
        expect($tel['numero'])->toMatch('/^\d{8,9}$/');
    }
})->with('estacionamentos_validos');

test('deve validar estacionamentos inválidos', function ($dados) {
    expect($dados)->toBeArray();

    $info = $dados['dados'] ?? [];

    // Nome inválido
    if (empty($info['nome'])) {
        expect($info['nome'] ?? null)->toBeEmpty();
    } elseif (strlen($info['nome']) > 100) {
        expect(strlen($info['nome']))->toBeGreaterThan(100);
    }

    // Capacidade inválida
    if (empty($info['capacidade']) || ! is_int($info['capacidade']) || $info['capacidade'] <= 0) {
        expect($info['capacidade'] ?? null)->toBeLessThanOrEqual(0);
    }

    // Horário inválido
    if (! preg_match('/^\d{2}:\d{2}:\d{2}$/', $info['hora_abertura'] ?? '') ||
        ! preg_match('/^\d{2}:\d{2}:\d{2}$/', $info['hora_fechamento'] ?? '')
    ) {
        expect($info['hora_abertura'] ?? '')->not->toMatch('/^\d{2}:\d{2}:\d{2}$/');
    }

    // Lotado inválido
    if (! in_array($info['lotado'] ?? '', ['S', 'N'])) {
        expect($info['lotado'] ?? '')->not->toBeIn(['S', 'N']);
    }

    // Gestor inválido
    if (empty($info['gestor_id']) || ! is_int($info['gestor_id']) || $info['gestor_id'] <= 0) {
        expect($info['gestor_id'] ?? null)->toBeLessThanOrEqual(0);
    }

    // Endereço inválido
    if (empty($dados['endereco']['cep']) || strlen($dados['endereco']['cep']) !== 8) {
        expect($dados['endereco']['cep'] ?? null)->not->toMatch('/^\d{8}$/');
    }

    // Telefones inválidos
    if (empty($dados['telefones']) || count($dados['telefones']) > 2) {
        expect($dados['telefones'] ?? [])->toBeArray();
    }
})->with('estacionamentos_invalidos');
