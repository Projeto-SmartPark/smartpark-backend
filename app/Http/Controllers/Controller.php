<?php

namespace App\Http\Controllers;

/**
 * @OA\Info(
 *     title="SmartPark API",
 *     version="1.0.0",
 *     description="API para gerenciamento do sistema SmartPark",
 *     @OA\Contact(
 *         email="contato@smartpark.com"
 *     )
 * )
 * 
 * @OA\Server(
 *     url="http://localhost:8000/api",
 *     description="Servidor de Desenvolvimento"
 * )
 * 
 * @OA\Tag(
 *     name="Usuários",
 *     description="Endpoints relacionados a usuários, clientes e gestores"
 * )
 * 
 * @OA\Tag(
 *     name="Clientes",
 *     description="Endpoints para gerenciamento de clientes"
 * )
 * 
 * @OA\Tag(
 *     name="Gestores",
 *     description="Endpoints para gerenciamento de gestores"
 * )
 */
abstract class Controller
{
    //
}
