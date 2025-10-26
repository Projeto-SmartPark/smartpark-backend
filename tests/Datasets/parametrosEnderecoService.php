<?php

// tests/Datasets/parametrosEnderecoService.php

return [
    'validos' => [
        // Caso 1: Endereço completo em São Paulo
        [
            'cep' => '01001000', 'estado' => 'SP', 'cidade' => 'São Paulo', 'bairro' => 'Sé', 'numero' => '1', 'logradouro' => 'Praça da Sé', 'complemento' => 'Lado A', 'ponto_referencia' => 'Em frente à Catedral', 'latitude' => -23.550520, 'longitude' => -46.633308
        ],
        // Caso 2: Endereço no Rio de Janeiro sem complemento
        [
            'cep' => '20040030', 'estado' => 'RJ', 'cidade' => 'Rio de Janeiro', 'bairro' => 'Centro', 'numero' => '100', 'logradouro' => 'Avenida Rio Branco', 'complemento' => null, 'ponto_referencia' => null, 'latitude' => -22.9083, 'longitude' => -43.1772
        ],
        // Caso 3: Endereço em Belo Horizonte com número composto
        [
            'cep' => '30130000', 'estado' => 'MG', 'cidade' => 'Belo Horizonte', 'bairro' => 'Centro', 'numero' => '200B', 'logradouro' => 'Avenida Afonso Pena', 'complemento' => 'Bloco 2', 'ponto_referencia' => null, 'latitude' => -19.9167, 'longitude' => -43.9333
        ],
        // Caso 4: Endereço em Brasília sem número
        [
            'cep' => '70150900', 'estado' => 'DF', 'cidade' => 'Brasília', 'bairro' => 'Zona Cívico-Administrativa', 'numero' => 'S/N', 'logradouro' => 'Praça dos Três Poderes', 'complemento' => null, 'ponto_referencia' => null, 'latitude' => -15.7998, 'longitude' => -47.8638
        ],
        // Caso 5: Endereço em Salvador com todos os campos
        [
            'cep' => '40026900', 'estado' => 'BA', 'cidade' => 'Salvador', 'bairro' => 'Centro Histórico', 'numero' => '30', 'logradouro' => 'Largo do Pelourinho', 'complemento' => 'Casa de Jorge Amado', 'ponto_referencia' => 'Próximo à Igreja', 'latitude' => -12.9711, 'longitude' => -38.5108
        ],
    ],
    'invalidos' => [
        // Casos com campos obrigatórios nulos
        ['cep' => null, 'estado' => 'SP', 'cidade' => 'Cidade', 'bairro' => 'Bairro', 'numero' => '1', 'logradouro' => 'Logradouro', 'complemento' => null, 'ponto_referencia' => null, 'latitude' => null, 'longitude' => null],
        ['cep' => '12345678', 'estado' => null, 'cidade' => 'Cidade', 'bairro' => 'Bairro', 'numero' => '1', 'logradouro' => 'Logradouro', 'complemento' => null, 'ponto_referencia' => null, 'latitude' => null, 'longitude' => null],
        ['cep' => '12345678', 'estado' => 'SP', 'cidade' => null, 'bairro' => 'Bairro', 'numero' => '1', 'logradouro' => 'Logradouro', 'complemento' => null, 'ponto_referencia' => null, 'latitude' => null, 'longitude' => null],
        ['cep' => '12345678', 'estado' => 'SP', 'cidade' => 'Cidade', 'bairro' => null, 'numero' => '1', 'logradouro' => 'Logradouro', 'complemento' => null, 'ponto_referencia' => null, 'latitude' => null, 'longitude' => null],
        ['cep' => '12345678', 'estado' => 'SP', 'cidade' => 'Cidade', 'bairro' => 'Bairro', 'numero' => null, 'logradouro' => 'Logradouro', 'complemento' => null, 'ponto_referencia' => null, 'latitude' => null, 'longitude' => null],
        ['cep' => '12345678', 'estado' => 'SP', 'cidade' => 'Cidade', 'bairro' => 'Bairro', 'numero' => '1', 'logradouro' => null, 'complemento' => null, 'ponto_referencia' => null, 'latitude' => null, 'longitude' => null],
    ],
    'borda' => [
        // Testando os limites máximos que são válidos
        ['cep' => '87654321', 'estado' => 'RS', 'cidade' => str_repeat('C', 80), 'bairro' => str_repeat('B', 80), 'numero' => str_repeat('9', 10), 'logradouro' => str_repeat('L', 120), 'complemento' => null, 'ponto_referencia' => null, 'latitude' => null, 'longitude' => null],
        ['cep' => '11111111', 'estado' => 'AA', 'cidade' => 'Cidade', 'bairro' => 'Bairro', 'numero' => 'Num', 'logradouro' => 'Logradouro', 'complemento' => str_repeat('C', 100), 'ponto_referencia' => null, 'latitude' => null, 'longitude' => null],
        ['cep' => '22222222', 'estado' => 'BB', 'cidade' => 'Cidade', 'bairro' => 'Bairro', 'numero' => 'Num', 'logradouro' => 'Logradouro', 'complemento' => null, 'ponto_referencia' => str_repeat('P', 100), 'latitude' => null, 'longitude' => null],
        // Teste com campos obrigatórios vazios, que deve falhar no banco
        ['cep' => '', 'estado' => '', 'cidade' => '', 'bairro' => '', 'numero' => '', 'logradouro' => '', 'complemento' => null, 'ponto_referencia' => null, 'latitude' => null, 'longitude' => null],
    ],
    'ids' => [
        'inexistente' => 99999,
        'negativo' => -1,
        'zero' => 0,
    ],
];