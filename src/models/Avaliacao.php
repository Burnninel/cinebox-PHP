<?php

class Avaliacao
{
    public $id;
    public $filme_id;
    public $usuario_id;
    public $usuario;
    public $comentario;
    public $nota;

    public static function buscarAvaliacoesFilme($database, $id)
    {
        return $database->query(
            query: "
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
                WHERE f.id = :filme_id
            ",
            class: self::class,
            params: ['filme_id' => $id]
        )->fetchAll();
    }

    public static function criarAvaliacao($database, $dados)
    {
        return $database->query(
            "INSERT INTO avaliacoes (usuario_id, filme_id, nota, comentario)
             VALUES (:usuario_id, :filme_id, :nota, :comentario)",
            params: $dados
        );
    }

    public static function buscarAvaliacao($database, $id)
    {
        $avaliacao = $database->query(
            "SELECT * FROM avaliacoes WHERE id = :id",
            params: ['id' => $id]
        )->fetch();

        return $avaliacao ?: null;
    }

    public static function removerAvaliacao($database, $dados)
    {
        $stmt = $database->query(
            "DELETE FROM avaliacoes WHERE id = :id AND usuario_id = :usuario_id;",
            params: $dados
        );

        return $stmt->rowCount() > 0;
    }
}
