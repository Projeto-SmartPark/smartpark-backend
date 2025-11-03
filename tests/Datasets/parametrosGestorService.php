<?php

return [
    'validos' => [
        [
            'nome' => 'Maria Santos',
            'email' => 'maria@empresa.com',
            'senha' => 'senha123',
            'cnpj' => '12345678000190',
        ],
        [
            'nome' => 'João Oliveira',
            'email' => 'joao.oliveira@corp.com',
            'senha' => 'SuperSenha456',
            'cnpj' => '11222333000188',
        ],
        [
            'nome' => 'Gestora Central Ltda',
            'email' => 'gestora.central@empresa.com',
            'senha' => 'abc12345',
            'cnpj' => '99887766000144',
        ],
    ],

    'invalidos' => [
        [
            'nome' => '', // vazio
            'email' => 'emailinvalido',
            'senha' => '123',
            'cnpj' => 'abc',
        ],
        [
            'nome' => str_repeat('A', 150), // ultrapassa limite
            'email' => 'joao@empresa',
            'senha' => str_repeat('a', 3),
            'cnpj' => '123456789', // incompleto
        ],
        [
            'nome' => 'Ana',
            'email' => '', // ausente
            'senha' => str_repeat('x', 101), // ultrapassa limite
            'cnpj' => '', // ausente
        ],
    ],
];
