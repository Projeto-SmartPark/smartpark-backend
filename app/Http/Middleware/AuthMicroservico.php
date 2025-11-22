<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Tymon\JWTAuth\Facades\JWTAuth;

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

        // Tenta buscar dados do usuário do cache
        $cacheKey = 'auth_user_'.$token;
        $usuario = Cache::get($cacheKey);

        if (! $usuario) {
            // Se não estiver em cache, valida com o microserviço de autenticação
            $authUrl = config('services.auth.url').'/api/auth/me';

            try {
                $response = Http::timeout(3)->withToken($token)->get($authUrl);

                if ($response->failed()) {
                    return response()->json(['error' => 'Token inválido ou expirado.'], 401);
                }

                $usuario = $response->json();

                // Armazena no cache por 5 minutos
                Cache::put($cacheKey, $usuario, 300);
            } catch (\Throwable $e) {
                \Log::error('Error validating token', [
                    'message' => $e->getMessage(),
                    'auth_url' => $authUrl,
                ]);

                return response()->json([
                    'error' => 'Erro ao validar token.',
                    'message' => $e->getMessage(),
                ], 500);
            }
        }

        // Injeta os dados do usuário autenticado na requisição
        $request->merge(['usuario' => $usuario]);

        return $next($request);
    }
}
