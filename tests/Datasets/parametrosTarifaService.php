<?php

return [
    'validos' => [
        [
            'nome' => 'Tarifa Horária',
            'valor' => 5.50,
            'tipo' => 'hora',
            'estacionamento_id' => 1,
        ],
        [
            'nome' => 'Tarifa Diária',
            'valor' => 40.00,
            'tipo' => 'diaria',
            'estacionamento_id' => 2,
        ],
        [
            'nome' => 'Tarifa Mensal',
            'valor' => 350.00,
            'tipo' => 'mensal',
            'estacionamento_id' => 3,
        ],
    ],
    'invalidos' => [
        [
            'nome' => '',
            'valor' => -5,
            'tipo' => 'hora',
            'estacionamento_id' => null,
        ],
        [
            'nome' => 'Tarifa Inválida',
            'valor' => 'texto',
            'tipo' => 'semanal',
            'estacionamento_id' => 1,
        ],
        [
            'nome' => 'Muito longa '.str_repeat('x', 150),
            'valor' => 10.00,
            'tipo' => 'hora',
            'estacionamento_id' => 9999,
        ],
    ],
];
