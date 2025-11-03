<?php

return [
    'validos' => [
        [
            'dados' => [
                'nome' => 'Estacionamento Central',
                'capacidade' => 100,
                'hora_abertura' => '08:00:00',
                'hora_fechamento' => '22:00:00',
                'lotado' => 'N',
                'gestor_id' => 1,
            ],
            'endereco' => [
                'cep' => '01310100',
                'logradouro' => 'Av. Paulista',
                'numero' => '1000',
                'bairro' => 'Bela Vista',
                'cidade' => 'São Paulo',
                'estado' => 'SP',
            ],
            'telefones' => [
                ['ddd' => '11', 'numero' => '987654321'],
            ],
        ],
        [
            'dados' => [
                'nome' => 'ParkSul Premium',
                'capacidade' => 80,
                'hora_abertura' => '07:00:00',
                'hora_fechamento' => '21:00:00',
                'lotado' => 'S',
                'gestor_id' => 2,
            ],
            'endereco' => [
                'cep' => '70250100',
                'logradouro' => 'SQS 210 Bloco A',
                'numero' => '5',
                'bairro' => 'Asa Sul',
                'cidade' => 'Brasília',
                'estado' => 'DF',
            ],
            'telefones' => [
                ['ddd' => '61', 'numero' => '998877665'],
                ['ddd' => '61', 'numero' => '332211445'],
            ],
        ],
        [
            'dados' => [
                'nome' => 'Garagem Norte',
                'capacidade' => 150,
                'hora_abertura' => '06:30:00',
                'hora_fechamento' => '23:00:00',
                'lotado' => 'N',
                'gestor_id' => 3,
            ],
            'endereco' => [
                'cep' => '70750100',
                'logradouro' => 'SHN Quadra 2',
                'numero' => '200',
                'bairro' => 'Asa Norte',
                'cidade' => 'Brasília',
                'estado' => 'DF',
            ],
            'telefones' => [
                ['ddd' => '61', 'numero' => '912345678'],
            ],
        ],
    ],

    'invalidos' => [
        [
            'dados' => [
                'nome' => '',
                'capacidade' => 0,
                'hora_abertura' => '25:00:00',
                'hora_fechamento' => '99:99:99',
                'lotado' => 'X',
                'gestor_id' => null,
            ],
            'endereco' => [
                'cep' => '999',
                'logradouro' => '',
                'numero' => '',
                'bairro' => '',
                'cidade' => '',
                'estado' => 'XX',
            ],
            'telefones' => [],
        ],
        [
            'dados' => [
                'nome' => str_repeat('A', 150),
                'capacidade' => -10,
                'hora_abertura' => '08:60:00',
                'hora_fechamento' => '23:70:00',
                'lotado' => '',
                'gestor_id' => -2,
            ],
            'endereco' => [
                'cep' => '',
                'logradouro' => 'Rua sem nome',
                'numero' => 'SN',
                'bairro' => 'Desconhecido',
                'cidade' => '',
                'estado' => '',
            ],
            'telefones' => [
                ['ddd' => '1', 'numero' => '9999999999'],
            ],
        ],
        [
            'dados' => [
                'nome' => 'Nome Válido',
                'capacidade' => 50,
                'hora_abertura' => '08:00:00',
                'hora_fechamento' => '20:00:00',
                'lotado' => 'S',
                'gestor_id' => 1,
            ],
            'endereco' => [
                'cep' => '01310100',
                'logradouro' => 'Av. Paulista',
                'numero' => '1000',
                'bairro' => 'Bela Vista',
                'cidade' => 'São Paulo',
                'estado' => 'SP',
            ],
            'telefones' => [
                ['ddd' => 'AB', 'numero' => '1234ABC'],
                ['ddd' => '12', 'numero' => ''], // inválido
            ],
        ],
    ],
];
