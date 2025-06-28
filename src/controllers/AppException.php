<?php

class AppException extends Exception 
{
    protected $detalhesDb;

    public function __construct($mensagem, $detalhesDb = '', $codigo = 500)
    {
        parent::__construct($mensagem, $codigo);
        $this->detalhesDb = $detalhesDb;
    }

    public function getDetalhes()
    {
        return $this->detalhesDb;
    }
}