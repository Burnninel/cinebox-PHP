<?php

class FilmeService extends BaseService
{
    protected $database;
    protected $validacao;

    public function __construct($database)
    {
        $this->database = $database;
    }

    public function buscarFilmes($pesquisar)
    {
        return $this->safe(
            fn() => Filme::buscarFilmes($this->database, $pesquisar),
            'Erro ao buscar filmes no banco de dados.'
        );
    }

    public function buscarFilmePorId($id)
    {
        return $this->safe(
            fn() => Filme::buscarFilmePorId($this->database, $id),
            'Erro ao buscar filme no banco de dados.'
        );
    }

    public function buscarFilmesUsuario($usuario_id)
    {
        return $this->safe(
            fn() => Filme::buscarFilmesPorUsuario($this->database, $usuario_id),
            'Erro ao buscar filmes do usuario no banco de dados.'
        );
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
        return $this->safe(
            fn() => Filme::criarFilme($this->database, $dados, $usuario_id),
            'Erro ao incluir filme no banco de dados.'
        );
    }

    public function verificarFilmeFavoritado($dados)
    {
        return $this->safe(
            fn() => Filme::verificarFilmeFavoritado($this->database, $dados),
            'Erro ao validar filmes favoritos no banco de dados.'
        );
    }

    public function favoritarFilme($dados)
    {
        if (!isset($dados['filme_id'], $dados['usuario_id']) || !is_numeric($dados['filme_id'])) {
            return false;
        }

        return $this->safe(
            fn() => Filme::favoritarFilme($this->database, $dados),
            'Erro ao registrar filme favorito no banco de dados.'
        );
    }

    public function obterStatusFilmeParaUsuario($filme_id, $usuario_id)
    {
        $dados = [
            'filme_id' => $filme_id,
            'usuario_id' => $usuario_id
        ];

        $filme = $this->safe(
            fn() => Filme::buscarFilmePorId($this->database, $filme_id),
            'Erro ao consultar filme no banco de dados.'
        );

        if (!$filme) {
            return [
                'valido' => false,
                'mensagem' => 'Filme não encontrado.'
            ];
        }

        $favoritado = $this->safe(
            fn() => Filme::verificarFilmeFavoritado($this->database, $dados),
            'Erro ao consultar filme no banco de dados.'
        );

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

        return $this->safe(
            fn() => Filme::desfavoritarFilme($this->database, $dados),
            'Erro ao remover filme dos favoritos no banco de dados.'
        );
    }
}
