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

    public function query($database, $where, $params = [])
    {
        return $database->query(
            query: "
                SELECT 
                    f.*,
                    COUNT(a.id) AS total_avaliacoes,
                    ROUND(AVG(a.nota), 1) AS media_avaliacoes
                FROM 
                    filmes AS f
                LEFT JOIN
                    avaliacoes AS a ON f.id = a.filme_id
                WHERE $where
                GROUP BY f.id;
            ",
            params: $params,
            class: self::class
        );
    }

    public static function getFilme($database, $id)
    {
        return (new self)->query(
            $database,
            where: 'f.id = :id',
            params: ['id' => $id]
        )->fetch();
    }

    public static function getFilmes($database, $pesquisar)
    {
        if (!$pesquisar) {
            return [];
        }

        $where = 'LOWER(f.titulo) LIKE :pesquisar 
                  OR LOWER(f.diretor) LIKE :pesquisar 
                  OR LOWER(f.categoria) LIKE :pesquisar';

        $params = ['pesquisar' => "%$pesquisar%"];

        return (new self)->query($database, $where, $params)->fetchAll();
    }

    public static function getFilmesPorUsuario($database, $usuarioId)
    {
        $where = "f.id IN (
                SELECT uf.filme_id 
                FROM usuarios_filmes uf
                WHERE uf.usuario_id = :usuario_id
        )";

        $params = ['usuario_id' => $usuarioId];

        return (new self)->query($database, $where, $params)->fetchAll();
    }
}
