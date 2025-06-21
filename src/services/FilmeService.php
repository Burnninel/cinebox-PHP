<?php

class FilmeService
{
    protected $database;
    protected $validacao;

    public function __construct($database)
    {
        $this->database = $database;
    }

    public function buscarFilmePorId($id)
    {
        return Filme::getFilme($this->database, $id);
    }

    public function buscarFilmesUsuario($usuario_id)
    {
        return Filme::getFilmesPorUsuario($this->database, $usuario_id);
    }

    public function validarDados(array $dados)
    {
        $this->validacao = Validacao::validarCampos([
            'titulo' => ['required', 'min:3', 'unique:filmes'],
            'diretor' => ['required', 'min:6'],
            'categoria' => ['required'],
            'sinopse' => ['required'],
            'ano_de_lancamento' => ['required', 'max:4', 'min:4'],
        ], $dados, $this->database);

        $this->validacao->errosValidacao();

        return !flash()->hasMensagem('error');
    }

    public function criarFilme($dados, $usuario_id)
    {
        $this->database->query(
            "INSERT INTO filmes (titulo, diretor, ano_de_lancamento, sinopse, categoria)
             VALUES (:titulo, :diretor, :ano_de_lancamento, :sinopse, :categoria)",
            params: $dados
        );

        $filme_id = $this->database->lastInsertId();

        $this->database->query(
            "INSERT INTO usuarios_filmes (usuario_id, filme_id) VALUES (:usuario_id, :filme_id)",
            params: [
                'usuario_id' => $usuario_id,
                'filme_id' => $filme_id
            ]
        );

        return $filme_id;
    }

    public function verificarFilmeFavoritado($dados)
    {
        $salvos = $this->database->query(
            "SELECT * FROM usuarios_filmes WHERE usuario_id = :usuario_id AND filme_id = :filme_id",
            params: $dados
        )->fetch();

        return !empty($salvos);
    }

    public function favoritarFilme($dados)
    {
        if (!isset($dados['usuario_id'], $dados['filme_id']) || !is_numeric($dados['usuario_id']) || !is_numeric($dados['filme_id'])) {
            return false;
        }

        $this->database->query(
            "INSERT INTO usuarios_filmes (usuario_id, filme_id) VALUES (:usuario_id, :filme_id)",
            params: $dados
        );

        return true;
    }

    public function desfavoritarFilme($dados)
    {
        if (!isset($dados['usuario_id'], $dados['filme_id']) || !is_numeric($dados['usuario_id']) || !is_numeric($dados['filme_id'])) {
            return false;
        }

        $this->database->query(
            "DELETE FROM usuarios_filmes WHERE usuario_id = :usuario_id AND filme_id = :filme_id",
            params: $dados
        );

        return true;
    }
}
