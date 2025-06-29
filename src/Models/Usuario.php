<?php

namespace Models;

use Core\Database;
use PDOStatement;

class Usuario
{
    public int $id;
    public string $nome;
    public string $email;
    public string $senha;

    public static function buscarUsuarioCredenciais(Database $database, string $email): ?self
    {
        $usuario = $database->query(
            query: "SELECT * FROM usuarios WHERE email = :email",
            class: self::class,
            params: compact('email')
        )->fetch();

        return $usuario ?: null;
    }

    public function verificarSenha(string $senha): bool
    {
        return password_verify($senha, $this->senha);
    }

    public static function cadastrarUsuario(Database $database, array $dados): PDOStatement
    {
        return $database->query(
            "INSERT INTO usuarios (nome, email, senha) VALUES (:nome, :email, :senha)",
            params: $dados
        );
    }

    public static function hashSenha(string $senha): string|false
    {
        return password_hash($senha, PASSWORD_BCRYPT);
    }
}
