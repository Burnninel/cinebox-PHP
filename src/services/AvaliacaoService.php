<?php

namespace Services;

use Core\BaseService;
use Utils\Validacao;
use Models\Filme;
use Models\Avaliacao;

class AvaliacaoService extends BaseService
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

        return $this->safe(
            fn() => Filme::buscarFilmePorId($this->database, $id),
            'Erro ao buscar filme no banco de dados.'
        );
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

        return $this->safe(
            fn() => Avaliacao::criarAvaliacao($this->database, $dados),
            'Erro ao incluir avaliação no banco de dados.'
        );
    }

    public function listarAvaliacoes($id)
    {
        if ($this->idInvalido($id)) return false;

        return $this->safe(
            fn() => Avaliacao::buscarAvaliacoesFilme($this->database, $id),
            'Erro ao consultar avaliações do filme no banco de dados.'
        );
    }

    public function buscarAvaliacaoUsuarioFilme($id, $usuario)
    {
        if ($this->idInvalido($id)) return false;

        return $this->safe(
            fn() => Avaliacao::buscarAvaliacaoUsuarioFilme($this->database, $id, $usuario),
            'Erro ao consultar avaliacoes do usuario no banco de dados.'
        );
    }

    public function buscarAvaliacaoPorId($id)
    {
        if ($this->idInvalido($id)) return false;

        return $this->safe(
            fn() => Avaliacao::buscarAvaliacao($this->database, $id),
            'Erro ao consultar avaliacao no banco de dados.'
        );
    }

    public function excluirAvaliacao($dados)
    {
        return $this->safe(
            fn() => Avaliacao::removerAvaliacao($this->database, $dados),
            'Erro ao consultar avaliacao no banco de dados.'
        );
    }
}
