<?php

namespace Models;

class Usuario
{
    public $id;
    public $nome;
    public $email;
    public $senha;

    public static function buscarUsuarioCredenciais($database, $email)
    {
        $usuario = $database->query(
            query: "SELECT * FROM usuarios WHERE email = :email",
            class: Usuario::class,
            params: compact('email')
        )->fetch();

        return $usuario ?: null;
    }

    public function verificarSenha($senha)
    {
        return password_verify($senha, $this->senha);
    }

    public static function cadastrarUsuario($database, $dados)
    {
        return $database->query(
            "INSERT INTO usuarios (nome, email, senha) VALUES (:nome, :email, :senha)",
            params: $dados
        );
    }

    public static function hashSenha($senha)
    {
        return password_hash($senha, PASSWORD_BCRYPT);
    }
}
