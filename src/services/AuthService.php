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
        $usuario = $this->database->query(
            query: "SELECT * FROM usuarios WHERE email = :email",
            class: Usuario::class,
            params: compact('email')
        )->fetch();

        return $usuario && $usuario->verificarSenha($senha) ? $usuario : null;
    }

    public function registrar($dados)
    {
        $dadosFiltrados = [
            'nome' => $dados['nome'],
            'email' => $dados['email'],
            'senha' => password_hash($dados['senha'], PASSWORD_BCRYPT)
        ];

        $this->database->query(
            "INSERT INTO usuarios (nome, email, senha) VALUES (:nome, :email, :senha)",
            params: $dadosFiltrados
        );
    }
}
