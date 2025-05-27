<?php

class Controller
{
    function view($view, $data = [])
    {
        foreach ($data as $key => $value) {
            $$key = $value;
        }

        require __DIR__ . "/../views/templates/app.php";
    }
}
