<?php

class FilmeService
{
    protected $database;
    protected $validacao;

    public function __construct($database)
    {
        $this->database = $database;
    }

    public function buscarFilmes($pesquisar)
    {
        return Filme::getFilmes($this->database, $pesquisar);
    }

    public function buscarFilmePorId($id)
    {
        return Filme::getFilmePorId($this->database, $id);
    }

    public function buscarFilmesUsuario($usuario_id)
    {
        return Filme::getFilmesPorUsuario($this->database, $usuario_id);
    }

    public function validarDados($dados)
    {
        $regras = [
            'titulo' => ['required', 'min:3', 'unique:filmes'],
            'diretor' => ['required', 'min:6', 'string'],
            'categoria' => ['required', 'string'],
            'sinopse' => ['required', 'string'],
            'ano_de_lancamento' => [
                'required', 'numeric', 'max:4', 'min:4', 'between:1900-2025', 'length:4'
            ]
        ];

        $validador = $this->validacao = Validacao::validarCampos($regras, $dados, $this->database);

        return $validador->erros();
    }

    public function criarFilme($dados, $usuario_id)
    {
        return Filme::incluirNovoFilme($this->database, $dados, $usuario_id);
    }

    public function verificarFilmeFavoritado($dados)
    {
        return Filme::verificarFavoritado($this->database, $dados);
    }

    public function favoritarFilme($dados)
    {
        if (!isset($dados['filme_id'], $dados['usuario_id']) || !is_numeric($dados['filme_id'])) {
            return false;
        }

        return Filme::favoritar($this->database, $dados);
    }

    public function obterStatusFilmeParaUsuario($filme_id, $usuario_id)
    {
        $dados = [
            'filme_id' => $filme_id,
            'usuario_id' => $usuario_id
        ];

        $filme = Filme::getFilmePorId($this->database, $filme_id);

        if (!$filme) {
            return [
                'valido' => false,
                'mensagem' => 'Filme não encontrado.'
            ];
        }

        $favoritado = Filme::verificarFavoritado($this->database, $dados);

        return [
            'valido' => true,
            'filme' => $filme,
            'favoritado' => $favoritado,
            'dados' => $dados
        ];
    }

    public function desfavoritarFilme($dados)
    {
        if (!isset($dados['filme_id'], $dados['usuario_id']) || !is_numeric($dados['filme_id'])) {
            return false;
        }

        return Filme::desfavoritar($this->database, $dados);
    }
}
