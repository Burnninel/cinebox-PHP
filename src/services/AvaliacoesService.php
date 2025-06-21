<?php

class AvaliacoesService
{
    protected $database;
    protected $validacao;

    public function __construct($database)
    {
        $this->database = $database;
    }

    public function obterUsuarioAutenticado()
    {
        $usuario_id = auth()->id;
        return $usuario_id ?: null;
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

        $this->database->query(
            "INSERT INTO avaliacoes (usuario_id, filme_id, nota, comentario)
             VALUES (:usuario_id, :filme_id, :nota, :comentario)",
            params: $dados
        );

        return true;
    }
}
