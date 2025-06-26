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
        $regras = [
            'email' => ['required', 'email'],
            'senha' => ['required'],
        ];

        $validador = Validacao::validarCampos($regras, $dados, $this->database);

        return $validador->erros();
    }

    public function validarRegistro($dados)
    {
        $regras = [
            'nome' => ['required', 'string', 'min:5'],
            'email' => ['required', 'email', 'unique:usuarios'],
            'senha' => ['required', 'min:8', 'max:24', 'strong', 'confirmed'],
        ];

        $validador = Validacao::validarCampos($regras, $dados, $this->database);
        return $validador->erros();
    }

    public function autenticar($email, $senha)
    {
        $usuario = Usuario::buscarUsuarioCredenciais($this->database, $email);
        return $usuario && $usuario->verificarSenha($senha) ? $usuario : null;
    }

    public function registrar($dados)
    {
        $dadosFiltrados = [
            'nome' => $dados['nome'] ?? '',
            'email' => $dados['email'] ?? '',
            'senha' => Usuario::hashSenha($dados['senha'] ?? '')
        ];

        return Usuario::cadastrarUsuario($this->database, $dadosFiltrados);
    }
}
