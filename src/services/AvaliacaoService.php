<?php

class AvaliacaoService
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
        try {
            if ($this->idInvalido($id)) return false;
            return Filme::buscarFilmePorId($this->database, $id);
        } catch (PDOException $e) {
            throw new Exception('Erro ao buscar filme no banco de dados.');
        }
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
        try {
            if ($this->idInvalido($id)) return false;

            $dados += [
                'filme_id' => $id,
                'usuario_id' => $usuario
            ];

            return Avaliacao::criarAvaliacao($this->database, $dados);
        } catch (PDOException $e) {
            throw new Exception('Erro ao incluir avaliação no banco de dados.');
        }
    }

    public function listarAvaliacoes($id)
    {
        try {
            if ($this->idInvalido($id)) return false;
            return Avaliacao::buscarAvaliacoesFilme($this->database, $id);
        } catch (PDOException $e) {
            throw new Exception('Erro ao consultar avaliações do filme no banco de dados.');
        }
    }

    public function buscarAvaliacaoUsuarioFilme($id, $usuario)
    {
        try {
            if ($this->idInvalido($id)) return false;
            return Avaliacao::buscarAvaliacaoUsuarioFilme($this->database, $id, $usuario);
        } catch (PDOException $e) {
            throw new Exception('Erro ao consultar avaliacoes do usuario no banco de dados.');
        }
    }

    public function buscarAvaliacaoPorId($id)
    {
        try {
            if ($this->idInvalido($id)) return false;
            return Avaliacao::buscarAvaliacao($this->database, $id);
        } catch (PDOException $e) {
            throw new Exception('Erro ao consultar avaliacao no banco de dados.');
        }
    }

    public function excluirAvaliacao($dados)
    {
        try {
            return Avaliacao::removerAvaliacao($this->database, $dados);
        } catch (PDOException $e) {
            throw new Exception('Erro ao consultar avaliacao no banco de dados.');
        }
    }
}
