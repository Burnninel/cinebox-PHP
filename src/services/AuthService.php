<?php

class AuthService
{
    protected $database;
    protected $validacao;

    public function __construct($database)
    {
        $this->database = $database;
    }

    public function validarLogin($dados)
    {
        $this->validacao = Validacao::validarCampos([
            'email' => ['required', 'email'],
            'senha' => ['required']
        ], $dados, $this->database);

        $this->validacao->errosValidacao();

        return !flash()->hasMensagem('error');
    }

    public function validarRegistro($dados)
    {
        $this->validacao = Validacao::validarCampos([
            'nome' => ['required'],
            'email' => ['required', 'email', 'unique:usuarios'],
            'senha' => ['required', 'confirmed', 'min:8', 'max:24', 'strong'],
        ], $dados, $this->database);

        $this->validacao->errosValidacao();

        return !flash()->hasMensagem('error');
    }

    public function autenticar($email, $senha)
    {
        $usuario = Usuario::buscarUsuarioCredenciais($this->database, $email);

        return $usuario && $usuario->verificarSenha($senha) ? $usuario : null;
    }

    public function registrar($dados)
    {
        $dadosFiltrados = [
            'nome' => $dados['nome'],
            'email' => $dados['email'],
            'senha' => Usuario::hashSenha($dados['senha'])
        ];

        return Usuario::cadastrarUsuario($this->database, $dadosFiltrados);
    }
}
