<?php

return [
    'validos' => [
        [
            'cep' => '01310100',
            'estado' => 'SP',
            'cidade' => 'São Paulo',
            'bairro' => 'Bela Vista',
            'numero' => '1000',
            'logradouro' => 'Avenida Paulista',
            'complemento' => 'Sala 101',
            'ponto_referencia' => 'Próximo ao MASP',
            'latitude' => -23.561684,
            'longitude' => -46.655981,
        ],
        [
            'cep' => '70250100',
            'estado' => 'DF',
            'cidade' => 'Brasília',
            'bairro' => 'Asa Sul',
            'numero' => '10',
            'logradouro' => 'SQS 210 Bloco A',
            'complemento' => 'Apto 302',
            'ponto_referencia' => 'Perto do Pão de Açúcar',
            'latitude' => -15.826691,
            'longitude' => -47.921820,
        ],
        [
            'cep' => '30140071',
            'estado' => 'MG',
            'cidade' => 'Belo Horizonte',
            'bairro' => 'Savassi',
            'numero' => '50',
            'logradouro' => 'Rua Pernambuco',
        ],
    ],

    'invalidos' => [
        [
            'cep' => '', // faltando
            'estado' => 'S',
            'cidade' => '',
            'bairro' => '',
            'numero' => '',
            'logradouro' => '',
            'latitude' => 'ABC',
            'longitude' => 'XYZ',
        ],
        [
            'cep' => '999999999', // tamanho inválido
            'estado' => 'SPP',
            'cidade' => str_repeat('A', 100),
            'bairro' => str_repeat('B', 100),
            'numero' => str_repeat('9', 20),
            'logradouro' => str_repeat('L', 200),
            'latitude' => 'notnumber',
            'longitude' => 'notnumber',
        ],
        [
            'cep' => '01310100',
            'estado' => 'SP',
            'cidade' => 'São Paulo',
            'bairro' => 'Centro',
            'numero' => '123',
            'logradouro' => '',
            'latitude' => null,
            'longitude' => 'abc',
        ],
    ],
];
