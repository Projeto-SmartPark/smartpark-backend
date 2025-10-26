<?php

namespace App\Docs;

/**
 * @OA\Info(
 *     title="SmartPark API",
 *     version="1.0.0",
 *     description="API para gerenciamento de estacionamentos inteligentes - Sistema de controle de vagas, usuários e reservas",
 *     @OA\Contact(
 *         email="contato@smartpark.com",
 *         name="Equipe SmartPark"
 *     )
 * )
 * 
 * @OA\Server(
 *     url="http://127.0.0.1:8000/api",
 *     description="Servidor Local de Desenvolvimento"
 * )
 * 
 * @OA\Server(
 *     url="https://api.smartpark.com/api",
 *     description="Servidor de Produção"
 * )
 * 
 * @OA\Tag(
 *     name="Usuários",
 *     description="Operações gerais de usuários (clientes e gestores)"
 * )
 * 
 * @OA\Tag(
 *     name="Clientes",
 *     description="Operações específicas para clientes"
 * )
 * 
 * @OA\Tag(
 *     name="Gestores",
 *     description="Operações específicas para gestores de estacionamento"
 * )
 * 
 * @OA\Tag(
 *     name="Endereços",
 *     description="Operações de gerenciamento de endereços"
 * )
 * 
 * @OA\Tag(
 *     name="Estacionamentos",
 *     description="Operações de gerenciamento de estacionamentos"
 * )
 * 
 * @OA\Tag(
 *     name="Telefones",
 *     description="Operações de gerenciamento de telefones de contato"
 * )
 * 
 * @OA\Tag(
 *     name="Vagas",
 *     description="Operações de gerenciamento de vagas de estacionamento"
 * )
 * 
 * @OA\Tag(
 *     name="Veículos",
 *     description="Operações de gerenciamento de veículos dos clientes"
 * )
 * 
 * @OA\Tag(
 *     name="Reservas",
 *     description="Operações de gerenciamento de reservas de vagas"
 * )
 * 
 * @OA\Tag(
 *     name="Tarifas",
 *     description="Operações de gerenciamento de tarifas de estacionamento"
 * )
 * 
 * @OA\Tag(
 *     name="Acessos",
 *     description="Operações de gerenciamento de acessos ao estacionamento"
 * )
 */
class SwaggerInfo
{
    // Classe utilizada apenas para armazenar anotações do Swagger
    // Não contém implementação, serve apenas como ponto central da documentação da API
}
