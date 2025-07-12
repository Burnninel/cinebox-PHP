<?php

namespace Cinebox\App\Middlewares;

use Cinebox\App\Services\JwtService;
use Cinebox\App\Helpers\Log;

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
            $dadosLog = $jwtService->extrairPayloadParaLog($token);

            Log::warning('Token inválido ou expirado', [
                'id' => $dadosLog['id'],
                'email' => $dadosLog['email'],
            ]);

            jsonResponse(['status' => false, 'message' => 'Token inválido ou expirado'], 401);
        }

        return $payload;
    }
}
