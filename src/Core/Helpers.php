<?php

use Cinebox\App\Services\JwtService;

function dd($data): never
{
    echo '<pre>';
    var_dump($data);
    echo '</pre>';
    die();
}

function config(?string $chave = null): mixed
{
    $config = require __DIR__ . '/../../config/config.php';

    if (strlen($chave) > 0) {
        return $config[$chave];
    }

    return $config;
}

function auth(): object|false
{
    if (!isset($_SESSION['auth'])) {
        return false;
    }

    return (object) $_SESSION['auth'];
}

function requireAuthenticatedUser()
{
    $headers = getallheaders();

    if (!isset($headers['Authorization'])) {
        jsonResponse(['status' => false, 'message' => 'Token não fornecido'], 401);
    }

    $token = str_replace('Bearer ', '', $headers['Authorization']);
    $jwtService = new JwtService;
    $decoded = $jwtService->validarToken($token);

    if (!$decoded) {
        jsonResponse(['status' => false, 'message' => 'Token inválido ou expirado'], 401);
    }

    return $decoded;
}

function getRequestData(): array
{
    $dados = json_decode(file_get_contents('php://input'), true) ?: [];

    if (empty($dados)) {
        jsonResponse(['status' => false, 'message' => 'Requisição inválida: nenhum dado foi enviado.'], 400);
    }

    return $dados;
}

function jsonResponse(array $dados, int $status = 200): never
{
    http_response_code($status);
    echo json_encode($dados);
    exit;
}

function ensureValidId(mixed $param): int
{
    if (!is_numeric($param) || (int)$param <= 0) {
        jsonResponse(['status' => false, 'message' => 'ID inválido!'], 400);
    }

    return (int)$param;
}
