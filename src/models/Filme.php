<?php

class Filme
{
    public $id;
    public $titulo;
    public $diretor;
    public $ano_de_lancamento;
    public $sinopse;
    public $categoria;
    public $total_avaliacoes;
    public $media_avaliacoes;

    public function queryFilmes($database, $where = '', $params = [])
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

    public static function buscarFilmePorId($database, $id)
    {
        return (new self)->queryFilmes(
            $database,
            where: 'f.id = :id',
            params: ['id' => $id]
        )->fetch() ?: null;
    }

    public static function buscarFilmes($database, $pesquisar)
    {
        if (!$pesquisar) {
            return (new self)->queryFilmes($database)->fetchAll();
        }

        $where = 'LOWER(f.titulo) LIKE :pesquisar 
                  OR LOWER(f.diretor) LIKE :pesquisar 
                  OR LOWER(f.categoria) LIKE :pesquisar';

        $params = ['pesquisar' => "%$pesquisar%"];

        return (new self)->queryFilmes($database, $where, $params)->fetchAll();
    }

    public static function buscarFilmesPorUsuario($database, $usuarioId)
    {
        $where = "f.id IN (
                SELECT uf.filme_id 
                FROM usuarios_filmes uf
                WHERE uf.usuario_id = :usuario_id
        )";

        $params = ['usuario_id' => $usuarioId];

        return (new self)->queryFilmes($database, $where, $params)->fetchAll();
    }

    public static function criarFilme($database, $dados, $usuario_id)
    {
        $database->query(
            "INSERT INTO filmes (titulo, diretor, ano_de_lancamento, sinopse, categoria)
             VALUES (:titulo, :diretor, :ano_de_lancamento, :sinopse, :categoria)",
            params: $dados
        );

        $filme_id = $database->lastInsertId();

        $database->query(
            "INSERT INTO usuarios_filmes (usuario_id, filme_id) VALUES (:usuario_id, :filme_id)",
            params: [
                'usuario_id' => $usuario_id,
                'filme_id' => $filme_id
            ]
        );

        return $filme_id;
    }

    public static function verificarFilmeFavoritado($database, $dados)
    {
        return $database->query(
            "SELECT * FROM usuarios_filmes WHERE usuario_id = :usuario_id AND filme_id = :filme_id",
            params: $dados
        )->fetch();
    }

    public static function favoritarFilme($database, $dados)
    {
        return $database->query(
            "INSERT INTO usuarios_filmes (usuario_id, filme_id) VALUES (:usuario_id, :filme_id)",
            params: $dados
        );
    }

    public static function desfavoritarFilme($database, $dados)
    {
        $stmt = $database->query(
            "DELETE FROM usuarios_filmes WHERE usuario_id = :usuario_id AND filme_id = :filme_id",
            params: $dados
        );

        return $stmt->rowCount() > 0;
    }
}
