<?php

header('Content-Type: application/json');
header("Access-Control-Allow-Origin: http://localhost:5173");
header("Access-Control-Allow-Headers: Content-Type, Authorization");

$uri = trim(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH), '/');
$method = $_SERVER['REQUEST_METHOD'];

$segments = explode('/', $uri);

$resource = $segments[0] ?? '';
$param = $segments[1] ?? null;
$action = $segments[2] ?? null;

switch ($resource) {
    case 'login':
        $controllerClass = 'Cinebox\App\Controllers\AuthController';

        if ($method === 'POST') {
            (new $controllerClass($database))->login();
        }
        break;

    case 'signup':
        if ($method === 'POST') {
            $controllerClass = 'Cinebox\App\Controllers\AuthController';
            (new $controllerClass($database))->store();
        }
        break;
    
    case 'usuario':
        if ($method === 'POST') {
            $controllerClass = 'Cinebox\App\Controllers\AuthController';
            (new $controllerClass($database))->validate();
        }
        break;

    case 'logout':
        if ($method === 'POST') {
            $controllerClass = 'Cinebox\App\Controllers\AuthController';
            (new $controllerClass($database))->logout();
        }
        break;

    case 'filme':
        $controllerClass = 'Cinebox\App\Controllers\FilmeController';
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
        $controllerClass = 'Cinebox\App\Controllers\AvaliacaoController';
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