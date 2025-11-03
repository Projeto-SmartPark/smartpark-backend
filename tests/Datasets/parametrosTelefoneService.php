<?php

return [
    'validos' => [
        [
            'ddd' => '11',
            'numero' => '987654321',
        ],
        [
            'ddd' => '61',
            'numero' => '998877665',
        ],
        [
            'ddd' => '21',
            'numero' => '34567890',
        ],
    ],

    'invalidos' => [
        // DDD vazio
        [
            'ddd' => '',
            'numero' => '987654321',
        ],
        // DDD com letras
        [
            'ddd' => 'AB',
            'numero' => '123456789',
        ],
        // Número longo demais e DDD incorreto
        [
            'ddd' => '123',
            'numero' => '99988877766',
        ],
    ],
];
