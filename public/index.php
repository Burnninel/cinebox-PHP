<?php

use Dotenv\Dotenv;

require_once __DIR__ . '/../vendor/autoload.php';


$dotenv = Dotenv::createImmutable(__DIR__ . '/../');
$dotenv->load();

require __DIR__ . '/../src/Core/Helpers.php';
require __DIR__ . '/../src/Core/Database.php';

session_start();

require __DIR__ . '/../src/Core/Router.php';