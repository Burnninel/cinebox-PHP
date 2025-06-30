<?php

header('Content-Type: application/json');

$uri = trim(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH), '/');
$method = $_SERVER['REQUEST_METHOD'];

$segments = explode('/', $uri);

$resource = $segments[0] ?? '';
$param = $segments[1] ?? null;
$action = $segments[2] ?? null;

switch ($resource) {
    case 'login':
        $controllerClass = 'Controllers\AuthController';
        $controller = new $controllerClass($database);
        if ($method === 'POST') {
            (new $controllerClass($database))->login();
        }
        break;

    case 'registrar':
        if ($method === 'POST') {
            $controllerClass = 'Controllers\AuthController';
            (new $controllerClass($database))->store();
        }
        break;

    case 'logout':
        if ($method === 'POST') {
            $controllerClass = 'Controllers\AuthController';
            (new $controllerClass($database))->logout();
        }
        break;

    case 'filme':
        $controllerClass = 'Controllers\FilmeController';
        $controller = new $controllerClass($database);
        switch ($method) {
            case 'GET':
                if (!$param) {
                    $controller->index();
                } elseif ($param === 'meus-filmes') {
                    $controller->meusFilmes();
                } else {
                    $controller->show(ensureValidId($param));
                } 
                break;

            case 'POST':
                if ($param && $action === 'favoritar') {
                    $controller->favoritarFilme(ensureValidId($param));
                } elseif ($param && $action === 'desfavoritar') {
                    $controller->desfavoritarFilme(ensureValidId($param));
                } else {
                    $controller->store();
                }
                break;
        }
        break;

    case 'avaliacao':
        $controllerClass = 'Controllers\AvaliacaoController';
        $controller = new $controllerClass($database);
        switch ($method) {
            case 'POST':
                $controller->store(ensureValidId($param));
                break;
            case 'DELETE':
                $controller->destroy(ensureValidId($param));
                break;
        }
        break;

    default:
        http_response_code(404);
        echo json_encode(['error' => 'Rota não encontrada']);
}
