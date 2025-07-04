<?php

namespace Cinebox\App\Services;

use Firebase\JWT\JWT;
use Firebase\JWT\Key;

class JwtService
{
    private string $secretKey = '98SDFH9FPASP98HFV';
    private string $algoritmo = 'HS256';

    public function gerarToken(array $payload, int $expireInSeconds = 3600)
    {
        $payload = array_merge($payload, [
            "iat" => time(),
            "exp" => time() + $expireInSeconds
        ]);

        return JWT::encode($payload, $this->secretKey, $this->algoritmo);
    }

    public function validarToken(string $token): ?object
    {
        try {
            return JWT::decode($token, new Key($this->secretKey, $this->algoritmo));
        } catch (\Exception $e) {
            return null;
        }
    }
}
