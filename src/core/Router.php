<?php 

$path = str_replace('/', '', parse_url($_SERVER['REQUEST_URI'])['path']);

$routes = [
    '' => 'IndexController',
    'filme' => 'FilmeController',
];

if(!array_key_exists($path, $routes)) {
    http_response_code(404);
    echo "Rota não encontrada.";
    exit;
}

$controllerName = $routes[$path];

$controller = new $controllerName();
$controller->index($database);   