<?php

return [
    'validos' => [
        [
            'perfil' => 'C',
            'nome' => 'João da Silva',
            'email' => 'joao@teste.com',
            'senha' => 'senha123',
        ],
        [
            'perfil' => 'G',
            'nome' => 'Maria Oliveira',
            'email' => 'maria@empresa.com',
            'senha' => 'segura456',
            'cnpj' => '12345678000190',
        ],
        [
            'perfil' => 'C',
            'nome' => 'Ana', // Exatamente 3 caracteres (mínimo)
            'email' => 'ana@teste.com',
            'senha' => '123456', // Exatamente 6 caracteres (mínimo)
        ],
        [
            'perfil' => 'G',
            'nome' => 'Gestor com pontuação',
            'email' => 'gestor@empresa.com',
            'senha' => '12345678',
            'cnpj' => '12.345.678/0001-90', // CNPJ formatado
        ],
        [
            'perfil' => 'C',
            'nome' => str_repeat('B', 100), // Máximo caracteres
            'email' => 'max@teste.com',
            'senha' => str_repeat('X', 100),
        ],
    ],

    'invalidos' => [
        [
            'perfil' => '',
            'nome' => '',
            'email' => '',
            'senha' => '',
        ],
        [
            'perfil' => 'X', // Perfil inválido
            'nome' => 'João',
            'email' => 'email_invalido',
            'senha' => 'senha123',
        ],
        [
            'perfil' => 'C',
            'nome' => 'AB', // 2 caracteres (abaixo do mínimo)
            'email' => 'valido@teste.com',
            'senha' => '12345', // 5 caracteres (abaixo do mínimo)
        ],
        [
            'perfil' => 'G',
            'nome' => 'Maria Oliveira',
            'email' => '@semlocal.com', // Email inválido
            'senha' => 'senha123',
            'cnpj' => '000', // CNPJ muito curto
        ],
        [
            'perfil' => 'G',
            'nome' => 'Gestor sem CNPJ',
            'email' => 'gestor@empresa.com',
            'senha' => 'senha123',
            'cnpj' => '', // Gestor sem CNPJ
        ],
        [
            'perfil' => 'C',
            'nome' => str_repeat('C', 101), // 101 caracteres (acima do máximo)
            'email' => 'excede@teste.com',
            'senha' => 'senha123',
        ],
    ],

    'borda' => [
        [
            'perfil' => 'C',
            'nome' => 'José Ávila Çedilha',
            'email' => 'jose.avila@dominio.com.br',
            'senha' => 'senha_válida',
        ],
        [
            'perfil' => 'G',
            'nome' => 'Gestor CNPJ nulo',
            'email' => 'gestorsemcnpj@empresa.com',
            'senha' => '1234',
            'cnpj' => null,
        ],
        [
            'perfil' => 'G',
            'nome' => 'CNPJ pontuação errada',
            'email' => 'teste@empresa.com',
            'senha' => '123456',
            'cnpj' => '12.34.5678/0001-9',
        ],
        [
            'perfil' => 'C',
            'nome' => '<script>alert("XSS")</script>',
            'email' => 'xss@teste.com',
            'senha' => 'senha123',
        ],
        [
            'perfil' => 'G',
            'nome' => "'; DROP TABLE usuarios--",
            'email' => 'sql@empresa.com',
            'senha' => 'senha123',
            'cnpj' => '12345678000190',
        ],
        [
            'perfil' => 'c', // Perfil minúsculo
            'nome' => 'Teste Case Sensitive',
            'email' => 'case@teste.com',
            'senha' => 'senha123',
        ],
    ],

    // IDs para testes de busca, atualização e remoção
    'ids' => [
        'inexistente' => 99999,
        'negativo' => -1,
        'zero' => 0,
    ],
];
