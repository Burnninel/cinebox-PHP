<?php

function dd($data)
{
    echo '<pre>';
    var_dump($data);
    echo '</pre>';
    die();
}

function config($chave = null)
{
    $config = require __DIR__ . '/../../config/config.php';

    if (strlen($chave) > 0) {
        return $config[$chave];
    }

    return $config;
}

function auth()
{
    if (!isset($_SESSION['auth'])) {
        return false;
    }

    return (object) $_SESSION['auth'];
}

function requireAuthenticatedUser()
{
    $usuario = auth();

    if (!isset($usuario->id)) {
        http_response_code(401);
        echo json_encode(['error' => 'Usuário não autenticado.']);
        exit;
    }

    return $usuario;
}

function getRequestData()
{
    $dados = json_decode(file_get_contents('php://input'), true) ?: [];

    if (empty($dados)) {
        jsonResponse(['status' => false, 'message' => 'Requisição inválida: nenhum dado foi enviado.'], 400);
    }

    return $dados;
}

function jsonResponse($dados, $status = 200)
{
    http_response_code($status);
    echo json_encode($dados);
    exit;
}