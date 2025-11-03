<?php

return [
    'validos' => [
        [
            'placa' => 'ABC1234',
            'cliente_id' => 1,
        ],
        [
            'placa' => 'XYZ1A23',
            'cliente_id' => 2,
        ],
        [
            'placa' => 'JKL5678',
            'cliente_id' => 3,
        ],
    ],

    'invalidos' => [
        // Placa vazia
        [
            'placa' => '',
            'cliente_id' => 1,
        ],
        // Placa longa demais
        [
            'placa' => 'ABCDEFGHIJKL',
            'cliente_id' => 2,
        ],
        // Formato incorreto e cliente_id inválido
        [
            'placa' => '1234ABC',
            'cliente_id' => -5,
        ],
    ],
];
