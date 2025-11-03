<?php

return [
    'validos' => [
        [
            'nome' => 'João Silva',
            'email' => 'joao@exemplo.com',
            'senha' => 'senha123',
        ],
        [
            'nome' => 'Maria Oliveira',
            'email' => 'maria.oliveira@empresa.com',
            'senha' => 'minhaSenha456',
        ],
        [
            'nome' => 'Carlos Pereira',
            'email' => 'carlos.pereira@dominio.org',
            'senha' => 'segura789',
        ],
    ],

    'invalidos' => [
        [
            'nome' => '', // vazio
            'email' => 'emailinvalido',
            'senha' => '123',
        ],
        [
            'nome' => str_repeat('A', 150), // ultrapassa limite
            'email' => 'maria@empresa', // sem domínio completo
            'senha' => '', // vazia
        ],
        [
            'nome' => 'An', // muito curto
            'email' => '', // ausente
            'senha' => str_repeat('x', 101), // ultrapassa limite
        ],
    ],
];
