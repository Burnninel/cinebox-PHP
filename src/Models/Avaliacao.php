<?php

namespace Cinebox\App\Models;

use Cinebox\App\Core\Database;
use PDOStatement;

class Avaliacao
{
    public int $id;
    public int $filme_id;
    public int $usuario_id;
    public string $usuario;
    public string $comentario;
    public int $nota;

    public static function queryFilmeAvaliacao(Database $database, string $where, array $params = []): PDOStatement
    {
        $sql = "
            SELECT 
                f.id as filme_id,
                u.id as usuario_id,
                u.nome AS usuario,
                a.id,
                a.comentario,
                a.nota
            FROM avaliacoes AS a
            LEFT JOIN usuarios AS u ON u.id = a.usuario_id
            LEFT JOIN filmes AS f ON f.id = a.filme_id
        ";

        if ($where) {
            $sql .= " WHERE $where";
        }

        return $database->query(
            query: $sql,
            class: self::class,
            params: $params
        );
    }

    public static function buscarAvaliacoesFilme(Database $database, int $filme_id): array
    {
         return self::queryFilmeAvaliacao(
            $database,
            where: 'f.id = :filme_id',
            params: ['filme_id' => $filme_id]
        )->fetchAll();
    }

    public static function buscarAvaliacaoUsuarioFilme(Database $database, int $filme_id, int $usuario_id): ?Avaliacao
    {
        $avaliacao = self::queryFilmeAvaliacao(
            $database,
            where: 'a.filme_id = :filme_id AND a.usuario_id = :usuario_id',
            params: ['filme_id' => $filme_id, 'usuario_id' => $usuario_id]
        )->fetch();

        return $avaliacao ?: null;
    }

    public static function criarAvaliacao(Database $database, array $dados): PDOStatement
    {
        return $database->query(
            "INSERT INTO avaliacoes (usuario_id, filme_id, nota, comentario)
             VALUES (:usuario_id, :filme_id, :nota, :comentario)",
            params: $dados
        );
    }

    public static function buscarAvaliacao(Database $database, int $id): array|null
    {
        $avaliacao = $database->query(
            "SELECT * FROM avaliacoes WHERE id = :id",
            params: ['id' => $id]
        )->fetch();

        return $avaliacao ?: null;
    }

    public static function removerAvaliacao(Database $database, array $dados): bool
    {
        $stmt = $database->query(
            "DELETE FROM avaliacoes WHERE id = :id AND usuario_id = :usuario_id;",
            params: $dados
        );

        return $stmt->rowCount() > 0;
    }
}
