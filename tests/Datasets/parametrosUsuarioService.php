<?php

return [
    'validos' => [
        [
            'perfil' => 'C',
            'nome' => 'João Silva',
            'email' => 'joao@exemplo.com',
            'senha' => 'senha123',
        ],
        [
            'perfil' => 'G',
            'nome' => 'Maria Santos',
            'email' => 'maria.santos@empresa.com',
            'senha' => 'adminSenha456',
            'cnpj' => '12345678000190',
        ],
        [
            'perfil' => 'C',
            'nome' => 'Carlos Oliveira',
            'email' => 'carlos.oliveira@corp.com',
            'senha' => 'abcde12345',
        ],
    ],

    'invalidos' => [
        [
            'perfil' => '', // ausente
            'nome' => 'Jo', // curto
            'email' => 'emailinvalido',
            'senha' => '123',
        ],
        [
            'perfil' => 'G',
            'nome' => str_repeat('A', 150), // muito longo
            'email' => 'gestor@empresa', // email inválido
            'senha' => '', // vazia
            'cnpj' => '1234', // incompleto
        ],
        [
            'perfil' => 'X', // perfil inválido
            'nome' => '',
            'email' => '',
            'senha' => str_repeat('x', 101), // excede limite
        ],
    ],
];
