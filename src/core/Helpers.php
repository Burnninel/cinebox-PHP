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