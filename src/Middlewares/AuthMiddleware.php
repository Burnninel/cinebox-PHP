<?php

namespace Cinebox\App\Middlewares;

use Cinebox\App\Services\JwtService;

class AuthMiddleware
{
    function handle()
    {
        $headers = getallheaders();

        if (!isset($headers['Authorization'])) {
            jsonResponse(['status' => false, 'message' => 'Token não fornecido'], 401);
        }

        $token = str_replace('Bearer ', '', $headers['Authorization']);
        $jwtService = new JwtService;
        $payload = $jwtService->validarToken($token);

        if (!$payload) {
            jsonResponse(['status' => false, 'message' => 'Token inválido ou expirado'], 401);
        }

        return $payload;
    }
}
