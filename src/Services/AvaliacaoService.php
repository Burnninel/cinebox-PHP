<?php

namespace Services;

use Core\BaseService;
use Core\Database;
use Utils\Validacao;
use Models\Filme;
use Models\Avaliacao;

class AvaliacaoService extends BaseService
{
    protected Database $database;

    public function __construct(Database $database)
    {
        $this->database = $database;
    }

    private function idInvalido(int $id): bool
    {
        return $id <= 0;
    }

    public function buscarFilmePorId(int $id): bool|array
    {
        if ($this->idInvalido($id)) return false;

        return $this->safe(
            fn() => Filme::buscarFilmePorId($this->database, $id),
            'Erro ao buscar filme no banco de dados.'
        );
    }

    public function validarDados(array $dados): array
    {
        $regras = [
            'nota' => ['required', 'numeric', 'between:1-5', 'length:1'],
            'comentario' => ['required', 'min:15']
        ];

        $validador = Validacao::validarCampos($regras, $dados, $this->database);

        return $validador->erros();
    }

    public function criarAvaliacao(array $dados, int $id, int $usuario_id): bool|\PDOStatement
    {
        if ($this->idInvalido($id)) return false;

        $dados += [
            'filme_id' => $id,
            'usuario_id' => $usuario_id
        ];

        return $this->safe(
            fn() => Avaliacao::criarAvaliacao($this->database, $dados),
            'Erro ao incluir avaliação no banco de dados.'
        );
    }

    public function listarAvaliacoes(int $id): bool|array
    {
        if ($this->idInvalido($id)) return false;

        return $this->safe(
            fn() => Avaliacao::buscarAvaliacoesFilme($this->database, $id),
            'Erro ao consultar avaliações do filme no banco de dados.'
        );
    }

    public function buscarAvaliacaoUsuarioFilme(int $id, int $usuario_id): bool|Avaliacao|null
    {
        if ($this->idInvalido($id)) return false;

        return $this->safe(
            fn() => Avaliacao::buscarAvaliacaoUsuarioFilme($this->database, $id, $usuario_id),
            'Erro ao consultar avaliacoes do usuario no banco de dados.'
        );
    }

    public function buscarAvaliacaoPorId(int $id): bool|Avaliacao|null
    {
        if ($this->idInvalido($id)) return false;

        return $this->safe(
            fn() => Avaliacao::buscarAvaliacao($this->database, $id),
            'Erro ao consultar avaliacao no banco de dados.'
        );
    }

    public function excluirAvaliacao(array $dados): bool
    {
        return $this->safe(
            fn() => Avaliacao::removerAvaliacao($this->database, $dados),
            'Erro ao consultar avaliacao no banco de dados.'
        );
    }
}
