<?php 

$rota = str_replace('/', '', parse_url($_SERVER['REQUEST_URI'])['path']);

if($rota === 'filme') {
    $controller = new FilmeController();
    $controller->index();   
} elseif ($rota === 'login') {
    $controller = new FilmeController();
    $controller->index();
} else {
    echo "Rota não encontrada.";
}