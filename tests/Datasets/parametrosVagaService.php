<?php

return [
    'validos' => [
        [
            'identificacao' => 'A-101',
            'tipo' => 'carro',
            'disponivel' => 'S',
            'estacionamento_id' => 1,
        ],
        [
            'identificacao' => 'B-202',
            'tipo' => 'moto',
            'disponivel' => 'N',
            'estacionamento_id' => 2,
        ],
        [
            'identificacao' => 'C-303',
            'tipo' => 'deficiente',
            'disponivel' => 'S',
            'estacionamento_id' => 3,
        ],
    ],

    'invalidos' => [
        [
            'identificacao' => '',
            'tipo' => 'carro',
            'disponivel' => 'S',
            'estacionamento_id' => 1,
        ],
        [
            'identificacao' => 'D-404',
            'tipo' => 'invalido',
            'disponivel' => 'S',
            'estacionamento_id' => 2,
        ],
        [
            'identificacao' => 'E-505',
            'tipo' => 'moto',
            'disponivel' => 'X',
            'estacionamento_id' => null,
        ],
    ],
];
