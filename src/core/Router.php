<?php 

$uri = trim(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH), '/');
[$rota, $acao] = array_pad(explode('/', $uri, 2), 2, 'index');

$routes = [
    '' => 'IndexController',
    'filme' => 'FilmeController',
    'login' => 'AuthController',
    'registrar' => 'RegistrarController',
    'logout' => 'AuthController',
    'avaliacoes' => 'AvaliacaoController',
];

if(!array_key_exists($rota, $routes)) {
    http_response_code(404);
    echo "Rota não encontrada.";
    exit;
}

$controllerName = $routes[$rota];
$controller = new $controllerName($database);

$controller->$acao($database);