<?php

namespace Cinebox\App\Models;

use Cinebox\App\Core\Database;
use PDOStatement;

class Filme
{
    public int $id;
    public string $titulo;
    public string $diretor;
    public int $ano_de_lancamento;
    public string $sinopse;
    public string $categoria;
    public int $total_avaliacoes;
    public ?float $media_avaliacoes;
    public string $imagem;

    public function queryFilmes(Database $database, string $where = "", array $params = []): PDOStatement
    {

        $sql = "
            SELECT 
                f.*,
                COUNT(a.id) AS total_avaliacoes,
                ROUND(AVG(a.nota), 1) AS media_avaliacoes
            FROM 
                filmes AS f
            LEFT JOIN
                avaliacoes AS a ON f.id = a.filme_id
        ";

        if ($where) {
            $sql .= " WHERE $where";
        }

        $sql .= " GROUP BY f.id";

        return $database->query(
            query: $sql,
            class: self::class,
            params: $params
        );
    }

    public static function buscarFilmePorId(Database $database, int $id): ?self
    {
        return (new self)->queryFilmes(
            $database,
            where: 'f.id = :id',
            params: ['id' => $id]
        )->fetch() ?: null;
    }

    public static function buscarFilmes(Database $database, string $pesquisar, ?int $usuario_id = null): array
    {
        $whereClauses = [];
        $params = [];

        if (!$pesquisar) {
            return (new self)->queryFilmes($database)->fetchAll();
        }

        if ($pesquisar) {
            $whereClauses[] = "
                (LOWER(f.titulo) LIKE :pesquisar 
                OR LOWER(f.diretor) LIKE :pesquisar 
                OR LOWER(f.categoria) LIKE :pesquisar)
            ";
            $params['pesquisar'] = "%$pesquisar%";
        }

        if ($usuario_id !== null) {
            $whereClauses[]  = "EXISTS (
                SELECT 1
                FROM usuarios_filmes uf
                WHERE uf.filme_id = f.id AND uf.usuario_id = :usuario_id
            )";
            $params['usuario_id'] = $usuario_id;
        }
        
        $where = implode(' AND ', $whereClauses);

        return (new self)->queryFilmes($database, $where, $params)->fetchAll();
    }

    public static function buscarFilmesPorUsuario(Database $database, int $usuario_id): array
    {
        $where = "f.id IN (
                SELECT uf.filme_id 
                FROM usuarios_filmes uf
                WHERE uf.usuario_id = :usuario_id
        )";

        $params = ['usuario_id' => $usuario_id];

        return (new self)->queryFilmes($database, $where, $params)->fetchAll();
    }

    public static function criarFilme(Database $database, array $dados, int $usuario_id): string
    {
        $database->query(
            query: "INSERT INTO filmes (titulo, diretor, ano_de_lancamento, sinopse, categoria, imagem)
                VALUES (:titulo, :diretor, :ano_de_lancamento, :sinopse, :categoria, :imagem)",
            params: $dados
        );

        $filme_id = $database->lastInsertId();

        $database->query(
            query: "INSERT INTO usuarios_filmes (usuario_id, filme_id) VALUES (:usuario_id, :filme_id)",
            params: [
                'usuario_id' => $usuario_id,
                'filme_id' => $filme_id
            ]
        );

        return $filme_id;
    }

    public static function verificarFilmeFavoritado(Database $database, array $dados): bool
    {
        $resultado = $database->query(
            query: "SELECT * FROM usuarios_filmes WHERE usuario_id = :usuario_id AND filme_id = :filme_id",
            params: $dados
        )->fetch();

        return $resultado !== false;
    }

    public static function favoritarFilme(Database $database, array $dados): PDOStatement
    {
        return $database->query(
            query: "INSERT INTO usuarios_filmes (usuario_id, filme_id) VALUES (:usuario_id, :filme_id)",
            params: $dados
        );
    }

    public static function desfavoritarFilme(Database $database, array $dados): PDOStatement
    {
        return $database->query(
            query: "DELETE FROM usuarios_filmes WHERE usuario_id = :usuario_id AND filme_id = :filme_id",
            params: $dados
        );
    }
}