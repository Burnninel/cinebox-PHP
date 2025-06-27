<?php

header('Content-Type: application/json');

$uri = trim(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH), '/');
$method = $_SERVER['REQUEST_METHOD'];

$segments = explode('/', $uri);

$resource = $segments[0] ?? '';
$param = $segments[1] ?? null;
$action = $segments[2] ?? null;
$method = $_SERVER['REQUEST_METHOD'];

switch ($resource) {
    case 'login':
        if ($method === 'POST') {
            (new AuthController($database))->login();
        }
        break;

    case 'registrar':
        if ($method === 'POST') {
            (new AuthController($database))->store();
        }
        break;

    case 'logout':
        if ($method === 'POST') {
            (new AuthController($database))->logout();
        }
        break;

    case 'filme':
        $controller = new FilmeController($database);
        switch ($method) {
            case 'GET':
                if (!$param) {
                    $controller->index();
                } elseif ($param === 'meus-filmes') {
                    $controller->meusFilmes();
                } else {
                    $controller->show($param);
                }
                break;

            case 'POST':
                if ($param && $action === 'favoritar') {
                    $controller->favoritarFilme($param);
                } elseif ($param && $action === 'desfavoritar') {
                    $controller->desfavoritarFilme($param);
                } else {
                    $controller->store();
                }
                break;
        }
        break;

    case 'avaliacao':
        $controller = new AvaliacaoController($database);
        switch ($method) {
            case 'POST':
                $controller->store($param);
                break;
            case 'DELETE':
                $controller->destroy($param);
                break;
        }
        break;

    default:
        http_response_code(404);
        echo json_encode(['error' => 'Rota não encontrada']);
}