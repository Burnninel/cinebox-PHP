<?php

class Usuario 
{
    public $id;
    public $nome;
    public $email;
    public $senha;

    public function verificarSenha($senha) {
        return password_verify($senha, $this->senha);
    }
}