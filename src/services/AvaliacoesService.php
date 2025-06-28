<?php

class AvaliacoesService
{
    protected $database;
    protected $validacao;
    protected $avaliacao;

    public function __construct($database)
    {
        $this->database = $database;
    }

    private function idInvalido($id)
    {
        return !isset($id) || !is_numeric($id);
    }

    public function buscarFilmePorId($id)
    {
        if ($this->idInvalido($id)) return false;
        return Filme::buscarFilmePorId($this->database, $id);
    }

    public function validarDados($dados)
    {
        $regras = [
            'nota' => ['required', 'numeric', 'between:1-5', 'length:1'],
            'comentario' => ['required', 'min:15']
        ];

        $validador = Validacao::validarCampos($regras, $dados, $this->database);

        return $validador->erros();
    }

    public function criarAvaliacao($dados, $id, $usuario)
    {
        if ($this->idInvalido($id)) return false;

        $dados += [
            'filme_id' => $id,
            'usuario_id' => $usuario
        ];

        return Avaliacao::criarAvaliacao($this->database, $dados);
    }

    public function listarAvaliacoes($id)
    {
        if ($this->idInvalido($id)) return false;
        return Avaliacao::buscarAvaliacoesFilme($this->database, $id);
    }

    public function buscarAvaliacaoUsuarioFilme($id, $usuario)
    {
        if ($this->idInvalido($id)) return false;
        return Avaliacao::buscarAvaliacaoUsuarioFilme($this->database, $id, $usuario);
    }

    public function buscarAvaliacaoPorId($id)
    {
        if ($this->idInvalido($id)) return false;
        return Avaliacao::buscarAvaliacao($this->database, $id);
    }

    public function excluirAvaliacao($dados)
    {
        return Avaliacao::removerAvaliacao($this->database, $dados);
    }
}
