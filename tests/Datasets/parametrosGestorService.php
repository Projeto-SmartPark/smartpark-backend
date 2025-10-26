<?php

return [
    'validos' => [
        [
            'nome' => 'João da Silva',
            'email' => 'joao@empresa.com',
            'senha' => 'senha123',
            'cnpj' => '12345678000190',
        ],
        [
            'nome' => 'Maria Oliveira',
            'email' => 'maria@empresa.com',
            'senha' => 'segura456',
            'cnpj' => '98765432000187',
        ],
        [
            'nome' => 'Gestor com pontuação',
            'email' => 'gestor@empresa.com',
            'senha' => '12345678',
            'cnpj' => '12.345.678/0001-90', // CNPJ formatado
        ],
        [
            'nome' => 'Ana', // Exatamente 3 caracteres (mínimo)
            'email' => 'ana@empresa.com',
            'senha' => '123456', // Exatamente 6 caracteres (mínimo)
            'cnpj' => '11222333000181', // Exatamente 14 caracteres
        ],
        [
            'nome' => str_repeat('B', 100), // Exatamente 100 caracteres (máximo)
            'email' => 'max100@empresa.com',
            'senha' => str_repeat('X', 100),
            'cnpj' => '11.222.333/0001-81', // Exatamente 18 caracteres com formatação
        ],
    ],

    'invalidos' => [
        [
            'nome' => '',
            'email' => '',
            'senha' => '',
            'cnpj' => '',
        ],
        [
            'nome' => 'AB', // 2 caracteres (abaixo do mínimo)
            'email' => 'valido@empresa.com',
            'senha' => '12345', // 5 caracteres (abaixo do mínimo)
            'cnpj' => '1234567890123', // 13 caracteres (abaixo do mínimo)
        ],
        [
            'nome' => 'João',
            'email' => 'email_invalido',
            'senha' => 'senha123',
            'cnpj' => '000', // CNPJ muito curto
        ],
        [
            'nome' => 'Pedro',
            'email' => '@semlocal.com',
            'senha' => 'senha123',
            'cnpj' => '12345678000190',
        ],
        [
            'nome' => 'Maria Oliveira',
            'email' => 'maria@empresa.com', // Email duplicado
            'senha' => 'segura456',
            'cnpj' => '98765432000187',
        ],
        [
            'nome' => 'Gestor CNPJ duplicado',
            'email' => 'gestor1@empresa.com',
            'senha' => '123',
            'cnpj' => '11222333000181', // CNPJ duplicado
        ],
        [
            'nome' => str_repeat('C', 101), // 101 caracteres (acima do máximo)
            'email' => 'excede@empresa.com',
            'senha' => 'senha123',
            'cnpj' => '12345678000190',
        ],
    ],

    'borda' => [
        [
            'nome' => 'José Ávila Çedilha', // Acentuação
            'email' => 'jose.avila@empresa.com.br',
            'senha' => 'senha_válida',
            'cnpj' => '12345678000190',
        ],
        [
            'nome' => 'Gestor CNPJ nulo',
            'email' => 'gestorsemcnpj@empresa.com',
            'senha' => '1234',
            'cnpj' => null,
        ],
        [
            'nome' => 'CNPJ pontuação errada',
            'email' => 'errado@empresa.com',
            'senha' => 'senha123',
            'cnpj' => '12.34.567/8000-190', // Formatação incorreta
        ],
        [
            'nome' => '<script>alert("XSS")</script>',
            'email' => 'xss@empresa.com',
            'senha' => 'senha123',
            'cnpj' => '12345678000190',
        ],
        [
            'nome' => "'; DROP TABLE gestores--",
            'email' => 'sql@empresa.com',
            'senha' => 'senha123',
            'cnpj' => '12345678000190',
        ],
    ],

    // IDs para testes de busca, atualização e remoção
    'ids' => [
        'inexistente' => 99999,
        'negativo' => -1,
        'zero' => 0,
    ],
];
