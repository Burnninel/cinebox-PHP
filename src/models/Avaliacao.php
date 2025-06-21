<?php

class Avaliacao
{
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
                    a.comentario,
                    a.nota
                FROM avaliacoes AS a
                LEFT JOIN usuarios AS u ON u.id = a.usuario_id
                LEFT JOIN filmes AS f ON f.id = a.filme_id
                WHERE f.id = :filme_id
            ",
            params: ['filme_id' => $id],
            class: self::class
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
}
