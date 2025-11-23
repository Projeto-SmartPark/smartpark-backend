<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Tymon\JWTAuth\Facades\JWTAuth;
use Tymon\JWTAuth\Exceptions\TokenExpiredException;
use Tymon\JWTAuth\Exceptions\TokenInvalidException;
use Tymon\JWTAuth\Exceptions\JWTException;

class AuthMicroservico
{
    /**
     * Valida o token JWT localmente (sem HTTP calls).
     * Ambos os serviços compartilham o mesmo JWT_SECRET.
     */
    public function handle(Request $request, Closure $next)
    {
        $token = $request->bearerToken();

        if (!$token) {
            return response()->json(['error' => 'Token não fornecido.'], 401);
        }

        try {
            // Define o token e decodifica localmente
            JWTAuth::setToken($token);
            $payload = JWTAuth::getPayload();

            // Extrai dados do usuário do payload
            $usuario = [
                'id' => $payload->get('sub'),
                'nome' => $payload->get('nome'),
                'email' => $payload->get('email'),
                'perfil' => $payload->get('perfil'),
            ];

            // Adiciona ao request (como array, não objeto)
            $request->merge(['usuario' => $usuario]);

            return $next($request);
        } catch (TokenExpiredException $e) {
            return response()->json(['error' => 'Token expirado.'], 401);
        } catch (TokenInvalidException $e) {
            return response()->json(['error' => 'Token inválido.'], 401);
        } catch (JWTException $e) {
            Log::error('Erro JWT AuthMicroservico: ' . $e->getMessage());
            return response()->json(['error' => 'Não autenticado.'], 401);
        }
    }
}
