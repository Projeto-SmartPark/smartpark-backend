<?php

/**
 * Rotas da API
 *
 * Todas as rotas aqui são automaticamente prefixadas com /api
 * conforme configurado no bootstrap/app.php
 *
 * Para adicionar novos módulos:
 * 1. Crie um arquivo routes.php dentro do módulo (app/Modules/NomeModulo/routes.php)
 * 2. Registre-o aqui usando require
 */

// Módulo de Usuários
require app_path('Modules/Usuarios/routes.php');

// Módulo de Endereço
require app_path('Modules/Endereco/routes.php');

// Módulo de Estacionamento
require app_path('Modules/Estacionamento/routes.php');

// Módulo de Telefone
require app_path('Modules/Telefone/routes.php');

// Módulo de Vaga
require app_path('Modules/Vaga/routes.php');

// Módulo de Veículo
require app_path('Modules/Veiculo/routes.php');

// Módulo de Reserva
require app_path('Modules/Reserva/routes.php');

// Módulo de Tarifa
require app_path('Modules/Tarifa/routes.php');

// Módulo de Acesso
require app_path('Modules/Acesso/routes.php');
