<?php

namespace App\Docs;

/**
 * @OA\Info(
 *     title="SmartPark API",
 *     version="1.0.0",
 *     description="Documentação da API do SmartPark.",
 *
 * )
 *
 * @OA\Server(
 *     url="http://localhost:8000/api",
 *     description="Servidor Principal (Local)"
 * )
 *
 * @OA\SecurityScheme(
 *     securityScheme="bearerAuth",
 *     type="http",
 *     scheme="bearer",
 *     bearerFormat="JWT",
 *     description="Insira o token JWT obtido no login"
 * )
 *
 * @OA\Tag(
 *     name="Usuários",
 *     description="Operações de gerenciamento de usuários, clientes e gestores"
 * )
 * @OA\Tag(
 *     name="Clientes",
 *     description="Operações de gerenciamento de clientes"
 * )
 * @OA\Tag(
 *     name="Gestores",
 *     description="Operações de gerenciamento de gestores"
 * )
 * @OA\Tag(
 *     name="Endereços",
 *     description="Operações de gerenciamento de endereços"
 * )
 * @OA\Tag(
 *     name="Estacionamentos",
 *     description="Operações de gerenciamento de estacionamentos"
 * )
 * @OA\Tag(
 *     name="Telefones",
 *     description="Operações de gerenciamento de telefones de contato"
 * )
 * @OA\Tag(
 *     name="Vagas",
 *     description="Operações de gerenciamento de vagas de estacionamento"
 * )
 * @OA\Tag(
 *     name="Veículos",
 *     description="Operações de gerenciamento de veículos dos clientes"
 * )
 * @OA\Tag(
 *     name="Reservas",
 *     description="Operações de gerenciamento de reservas de vagas"
 * )
 * @OA\Tag(
 *     name="Tarifas",
 *     description="Operações de gerenciamento de tarifas de estacionamento"
 * )
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
