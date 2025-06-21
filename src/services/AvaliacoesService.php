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
    
    public function validarDados($dados)
    {
        $this->validacao = Validacao::validarCampos([
            'nota' => ['required'],
            'comentario' => ['required', 'min:15'],
        ], $dados, $this->database);

        $this->validacao->errosValidacao();

        return !flash()->hasMensagem('error');
    }

    public function criarAvaliacao($dados)
    {
        if (!isset($dados['usuario_id'], $dados['filme_id']) || !is_numeric($dados['usuario_id']) || !is_numeric($dados['filme_id'])) {
            return false;
        }

        $this->avaliacao = Avaliacao::criarAvaliacao($this->database, $dados);

        return true;
    }

    public function listarAvaliacoes($filme_id)
    {
        if (!isset($filme_id) || !is_numeric($filme_id)) {
            return false;
        }

        $this->avaliacao = Avaliacao::buscarAvaliacoesFilme($this->database, $filme_id);

        return $this->avaliacao;
    }
}
