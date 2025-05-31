<?php

require_once __DIR__ . '/../src/core/Helpers.php';

spl_autoload_register(function ($class) {
    $paths = [
        __DIR__ . '/../src/core/' . $class . '.php',
        __DIR__ . '/../src/controllers/' . $class . '.php',
        __DIR__ . '/../src/models/' . $class . '.php',
    ];

    foreach ($paths as $file) {
        if (file_exists($file)) {
            require_once $file;
            return;
        }
    }
});

require __DIR__ . '/../src/core/Router.php';