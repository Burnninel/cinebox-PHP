<?php

class AuthService extends BaseService
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

        $validador = $this->safe(
            fn() => Validacao::validarCampos($regras, $dados, $this->database),
            'Erro ao consultar banco de dados.'
        );

        return $validador->erros();
    }

    public function validarRegistro($dados)
    {
        $regras = [
            'nome' => ['required', 'string', 'min:5'],
            'email' => ['required', 'email', 'unique:usuarios'],
            'senha' => ['required', 'min:8', 'max:24', 'strong', 'confirmed'],
        ];

        $validador = $this->safe(
            fn() => Validacao::validarCampos($regras, $dados, $this->database),
            'Erro ao consultar banco de dados.'
        );

        return $validador->erros();
    }

    public function autenticar($email, $senha)
    {
        $usuario = $this->safe(
            fn() => Usuario::buscarUsuarioCredenciais($this->database, $email),
            'Erro ao consultar banco de dados.'
        );
        return $usuario && $usuario->verificarSenha($senha) ? $usuario : null;
    }

    public function registrar($dados)
    {
        $dadosFiltrados = [
            'nome' => $dados['nome'] ?? '',
            'email' => $dados['email'] ?? '',
            'senha' => Usuario::hashSenha($dados['senha'] ?? '')
        ];

        return $this->safe(
            fn() => Usuario::cadastrarUsuario($this->database, $dadosFiltrados),
            'Erro ao inserir usuario no banco de dados.'
        );
    }
}
