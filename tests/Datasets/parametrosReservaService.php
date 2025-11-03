<?php

return [
    'validos' => [
        [
            'data' => '2025-11-05',
            'hora_inicio' => '08:00:00',
            'hora_fim' => '10:00:00',
            'status' => 'ativa',
            'cliente_id' => 1,
            'veiculo_id' => 2,
            'vaga_id' => 3,
        ],
        [
            'data' => '2025-11-06',
            'hora_inicio' => '12:00:00',
            'hora_fim' => '14:00:00',
            'status' => 'concluida',
            'cliente_id' => 2,
            'veiculo_id' => 4,
            'vaga_id' => 5,
        ],
        [
            'data' => '2025-11-07',
            'hora_inicio' => '18:30:00',
            'hora_fim' => '20:30:00',
            'status' => 'cancelada',
            'cliente_id' => 3,
            'veiculo_id' => 6,
            'vaga_id' => 7,
        ],
    ],

    'invalidos' => [
        [
            'data' => '', // data vazia
            'hora_inicio' => '08:00:00',
            'hora_fim' => '10:00:00',
            'status' => 'ativa',
            'cliente_id' => 1,
            'veiculo_id' => 2,
            'vaga_id' => 3,
        ],
        [
            'data' => '2025-11-05',
            'hora_inicio' => '25:99:99', // hora inválida
            'hora_fim' => '10:00:00',
            'status' => 'ativa',
            'cliente_id' => 1,
            'veiculo_id' => 2,
            'vaga_id' => 3,
        ],
        [
            'data' => '2025-11-05',
            'hora_inicio' => '09:00:00',
            'hora_fim' => '08:00:00', // hora final antes da inicial
            'status' => 'ativa',
            'cliente_id' => null, // cliente ausente
            'veiculo_id' => 2,
            'vaga_id' => 3,
        ],
    ],
];
