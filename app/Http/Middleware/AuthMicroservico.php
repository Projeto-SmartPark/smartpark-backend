<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class AuthMicroservico
{
    /**
     * Middleware para validar o token JWT com o microserviço de autenticação.
     */
    public function handle(Request $request, Closure $next)
    {
        $token = $request->bearerToken();

        if (! $token) {
            return response()->json(['error' => 'Token não fornecido.'], 401);
        }

        $authUrl = config('services.auth.url').'/api/auth/me';

        try {
            $response = Http::withToken($token)->get($authUrl);

            if ($response->failed()) {
                return response()->json(['error' => 'Token inválido ou expirado.'], 401);
            }

            // Injeta os dados do usuário autenticado na requisição
            $request->merge(['usuario' => $response->json()]);

            return $next($request);
        } catch (\Throwable $e) {
            return response()->json([
                'error' => 'Erro ao validar token.',
                'message' => $e->getMessage(),
            ], 500);
        }
    }
}
