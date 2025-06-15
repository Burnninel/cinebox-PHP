<?php

function dd($data)
{
    echo '<pre>';
    var_dump($data);
    echo '</pre>';
    die();
}

function config($chave = null)
{
    $config = require __DIR__ . '/../../config/config.php';

    if(strlen($chave) > 0) {
        return $config[$chave];
    }

    return $config;
}

function flash()
{
    return new Flash;
}

function auth()
{
    if (!isset($_SESSION['auth'])) {
        return false;
    }

    return $_SESSION['auth'];
}