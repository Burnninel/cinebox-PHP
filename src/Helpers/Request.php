<?php

namespace Cinebox\App\Helpers;

class Request
{
    public static function getData(): array
    {
        $dados = json_decode(file_get_contents('php://input'), true) ?: [];

        if (empty($dados)) {
            Response::error('Requisição inválida: nenhum dado foi enviado.', [], 400);
        }

        return $dados;
    }
}
