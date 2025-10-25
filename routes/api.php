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

// Módulo de Usuários (Clientes e Gestores)
require app_path('Modules/Usuarios/routes.php');

// Módulo de Endereço
require app_path('Modules/Endereco/routes.php');
