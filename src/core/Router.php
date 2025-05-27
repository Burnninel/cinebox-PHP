<?php 
$controller = str_replace('/', '', parse_url($_SERVER['REQUEST_URI'])['path']);
require __DIR__ . "/../controllers/{$controller}.controller.php";