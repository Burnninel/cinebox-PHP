<?php

namespace Cinebox\App\Core;

class AppException extends \Exception 
{
    protected string $detalhesDb;

    public function __construct(string $mensagem, string $detalhesDb = '', int $codigo = 500)
    {
        parent::__construct($mensagem, $codigo);
        $this->detalhesDb = $detalhesDb;
    }

    public function getDetalhes() : string
    {
        return $this->detalhesDb;
    }
}