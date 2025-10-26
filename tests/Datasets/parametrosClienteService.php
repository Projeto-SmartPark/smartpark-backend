<?php

return [
    'validos' => [
        [
            'nome' => 'João da Silva',
            'email' => 'joao@teste.com',
            'senha' => 'senha123',
        ],
        [
            'nome' => 'Maria Oliveira',
            'email' => 'maria@teste.com',
            'senha' => 'segura456',
        ],
        [
            'nome' => 'José com pontuação',
            'email' => 'jose.silva@dominio.com.br',
            'senha' => 'abc12345',
        ],
        [
            'nome' => 'Ana', // Exatamente 3 caracteres (mínimo)
            'email' => 'ana@exemplo.com',
            'senha' => '123456', // Exatamente 6 caracteres (mínimo)
        ],
        [
            'nome' => str_repeat('B', 100), // Exatamente 100 caracteres (máximo)
            'email' => 'max100@teste.com',
            'senha' => str_repeat('X', 100), // Senha máxima
        ],
    ],

    'invalidos' => [
        [
            'nome' => '',
            'email' => '',
            'senha' => '',
        ],
        [
            'nome' => 'AB', // 2 caracteres (abaixo do mínimo)
            'email' => 'valido@teste.com',
            'senha' => '12345', // 5 caracteres (abaixo do mínimo)
        ],
        [
            'nome' => 'João',
            'email' => 'email_invalido', // Sem @
            'senha' => 'senha123',
        ],
        [
            'nome' => 'Pedro',
            'email' => '@semlocal.com', // @ no início
            'senha' => 'senha123',
        ],
        [
            'nome' => 'Maria Oliveira',
            'email' => 'maria@teste.com', // Email duplicado
            'senha' => 'segura456',
        ],
        [
            'nome' => str_repeat('C', 101), // 101 caracteres (acima do máximo)
            'email' => 'excede@teste.com',
            'senha' => 'senha123',
        ],
    ],

    'borda' => [
        [
            'nome' => 'José Ávila Çedilha', // Acentuação e cedilha
            'email' => 'jose.avila@dominio.com',
            'senha' => 'senha_válida',
        ],
        [
            'nome' => 'Nome#com@caracteres!especiais',
            'email' => 'especial@teste.com',
            'senha' => 'pass@word#2024',
        ],
        [
            'nome' => '   João   com   espaços   ',
            'email' => 'espacos@teste.com',
            'senha' => 'senha123',
        ],
        [
            'nome' => "Nome com 'aspas' e \"duplas\"",
            'email' => 'aspas@teste.com',
            'senha' => 'senha"com\'aspas',
        ],
        [
            'nome' => '<script>alert("XSS")</script>', // Teste XSS
            'email' => 'xss@teste.com',
            'senha' => 'senha123',
        ],
        [
            'nome' => "'; DROP TABLE clientes--", // Teste SQL Injection
            'email' => 'sql@teste.com',
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
