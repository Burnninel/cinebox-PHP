<?php

header('Content-Type: application/json');

$uri = trim(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH), '/');
$method = $_SERVER['REQUEST_METHOD'];

$segments = explode('/', $uri);

$resource = $segments[0] ?? '';
$id = $segments[1] ?? null;
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
                $id ? $controller->show($id) : $controller->index();
                break;

            case 'POST':
                if ($id && $action === 'favoritar') {
                    $controller->favoritarFilme($id);
                } elseif ($id && $action === 'desfavoritar') {
                    $controller->desfavoritarFilme($id);
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
                $controller->store($id);
                break;
            case 'DELETE':
                $controller->destroy($id);
                break;
        }
        break;

    default:
        http_response_code(404);
        echo json_encode(['error' => 'Rota não encontrada']);
}
