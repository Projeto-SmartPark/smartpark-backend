<?php

return [
    'validos' => [
        [
            'data' => '2025-11-10',
            'hora_inicio' => '08:00:00',
            'hora_fim' => '09:30:00',
            'valor_total' => 12.50,
            'veiculo_id' => 1,
            'vaga_id' => 2,
            'cliente_id' => 3,
        ],
        [
            'data' => '2025-11-11',
            'hora_inicio' => '10:15:00',
            'hora_fim' => '12:00:00',
            'valor_total' => 20.00,
            'veiculo_id' => 2,
            'vaga_id' => 4,
            'cliente_id' => 5,
        ],
        [
            'data' => '2025-11-12',
            'hora_inicio' => '18:00:00',
            'hora_fim' => '19:45:00',
            'valor_total' => 15.75,
            'veiculo_id' => 3,
            'vaga_id' => 6,
            'cliente_id' => 7,
        ],
    ],

    'invalidos' => [
        [
            'data' => '', // data vazia
            'hora_inicio' => '08:00:00',
            'hora_fim' => '09:30:00',
            'valor_total' => 10.00,
            'veiculo_id' => 1,
            'vaga_id' => 2,
            'cliente_id' => 3,
        ],
        [
            'data' => '2025-11-10',
            'hora_inicio' => '25:99:99', // hora inválida
            'hora_fim' => '09:30:00',
            'valor_total' => 'dez', // não numérico
            'veiculo_id' => null,
            'vaga_id' => 2,
            'cliente_id' => 3,
        ],
        [
            'data' => '2025-11-10',
            'hora_inicio' => '08:00:00',
            'hora_fim' => '07:00:00', // hora final antes da inicial
            'valor_total' => -5.00, // valor negativo
            'veiculo_id' => 1,
            'vaga_id' => 0,
            'cliente_id' => -2, // id inválido
        ],
    ],
];
