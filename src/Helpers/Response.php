<?php

namespace Cinebox\App\Helpers;

class Response
{
     public static function json(array $dados, int $status = 200): never
    {
        http_response_code($status);
        echo json_encode($dados);
        exit;
    }

    public static function success(string $message, array $data = [], int $status = 200): never
    {
        self::json(['status' => true, 'message' => $message, 'data' => $data], $status);
    }

    public static function error(string $message, array $errors = [], int $status): never
    {
        self::json(['status' => false, 'message' => $message, 'errors' => $errors], $status);
    }
}