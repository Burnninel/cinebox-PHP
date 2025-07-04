<?php

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
