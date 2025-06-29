<?php

require_once __DIR__ . '/../src/Core/Helpers.php';
require_once __DIR__ . '/../src/Core/Database.php';

spl_autoload_register(function ($class) {
    $classPath = str_replace('\\', DIRECTORY_SEPARATOR, $class) . '.php';

    $baseDir = __DIR__ . '/../src/';

    $file = $baseDir . $classPath;

    if (file_exists($file)) {
        require_once $file;
    }
});

session_start();

require __DIR__ . '/../src/Core/Router.php';