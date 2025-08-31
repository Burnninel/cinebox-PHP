<?php

namespace Cinebox\App\Middlewares;

use Cinebox\App\Services\JwtService;

class OptionalAuthMiddleware
{
    public function handle(): ?object
    {
        $headers = getallheaders();
        if (!isset($headers['Authorization'])) {
            return null;
        }

        $token = str_replace('Bearer ', '', $headers['Authorization']);
        $jwtService = new JwtService();
        $payload = $jwtService->validarToken($token);

        return $payload ?: null;
    }
}