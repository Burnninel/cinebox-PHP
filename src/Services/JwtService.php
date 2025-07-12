<?php

namespace Cinebox\App\Services;

use Firebase\JWT\JWT;
use Firebase\JWT\Key;

class JwtService
{
    private string $secretKey;
    private string $algoritmo = 'HS256';

    public function __construct()
    {
        $this->secretKey = $_ENV['JWT_SECRET'];
    }

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

    public function extrairPayloadParaLog(string $token): ?array
    {
        // Separa a string em 3 parters (.) - JWT Correto: header.payload.assinatura 
        $partes = explode('.', $token);

        // Se não tiver 3 partes está incorreto
        if (count($partes) !== 3) {
            return null;
        }

        // Pega apenas o payload
        $payloadCodificado = $partes[1];

        // str_repeat - quantos "=" vai por no fim do payload;
        //  padding -> = 
        // '4 -' -> garante que o total de caracteres seja múltiplo de 4, necessário para o base64;
        // strlen = retorna o tamanho da string (ex: 110);
        // '% 4' calcula o resto da divisão por 4 (27x4 = 108 -> 110-108 = 2) -> Resto 2
        // '4 - resto' -> calcula quantos '=' são necessarios para completar % 4
        // '% 4' final -> evita adicionar 4 '=' se o resto for 0 (ajuste de segurança);
        $payloadCodificado .= str_repeat('=', (4 - strlen($payloadCodificado) % 4) % 4);

        // Decodifica o payload retornando os dados;
        // strtr() troca '-' por '+' e '_' por '/';
        // pois o JWT usa base64url (compatível com URLs), e o base64_decode exige base64 padrão.
        $payloadJson = base64_decode(strtr($payloadCodificado, '-_', '+/'));

        // Verificação de segunrança - base64_decode pode falhar
        if (!$payloadJson) {
            return null;
        }

        // transforma o JSON do payload em array associativo
        $payload = json_decode($payloadJson, true);

        return is_array($payload) ? $payload : null;
    }
}
