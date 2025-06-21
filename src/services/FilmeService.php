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
        return Filme::getFilmePorId($this->database, $id);
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
        return Filme::incluirNovoFilme($this->database, $dados, $usuario_id);
    }

    public function verificarFilmeFavoritado($dados)
    {
        $salvos = Filme::verificarFavoritado($this->database, $dados);

        return !empty($salvos);
    }

    public function favoritarFilme($dados)
    {
        if (!isset($dados['usuario_id'], $dados['filme_id']) || !is_numeric($dados['usuario_id']) || !is_numeric($dados['filme_id'])) {
            return false;
        }

        Filme::favoritar($this->database, $dados);

        return true;
    }

    public function desfavoritarFilme($dados)
    {
        if (!isset($dados['usuario_id'], $dados['filme_id']) || !is_numeric($dados['usuario_id']) || !is_numeric($dados['filme_id'])) {
            return false;
        }

        Filme::desfavoritar($this->database, $dados);

        return true;
    }
}
