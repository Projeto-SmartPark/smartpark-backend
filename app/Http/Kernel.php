<?php

namespace App\Http;

use Illuminate\Foundation\Http\Kernel as HttpKernel;

class Kernel extends HttpKernel
{
    /**
     * Middlewares globais — executados em toda requisição.
     */
    protected $middleware = [
        \Illuminate\Http\Middleware\HandleCors::class,
    ];

    /**
     * Middlewares de grupos.
     */
    protected $middlewareGroups = [
        'web' => [
            \Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse::class,
            \Illuminate\Session\Middleware\StartSession::class,
            \Illuminate\View\Middleware\ShareErrorsFromSession::class,
            \Illuminate\Routing\Middleware\SubstituteBindings::class,
        ],

        'api' => [
            \Illuminate\Routing\Middleware\SubstituteBindings::class,
        ],
    ];

    /**
     * Middlewares que podem ser aplicados manualmente em rotas.
     */
    protected $routeMiddleware = [
        // Middleware padrão de autenticação do Laravel
        'auth' => \App\Http\Middleware\Authenticate::class,

        // Seu middleware customizado de autenticação via microserviço
        'auth.microservico' => \App\Http\Middleware\AuthMicroservico::class,

        // Controle de requisições por segundo (padrão Laravel)
        'throttle' => \Illuminate\Routing\Middleware\ThrottleRequests::class,
    ];
}
