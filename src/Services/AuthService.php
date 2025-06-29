<?php

namespace Services;

use Core\BaseService;
use Core\Database;
use Utils\Validacao;
use Models\Usuario;

class AuthService extends BaseService
{
    protected Database $database;

    public function __construct(Database $database)
    {
        $this->database = $database;
    }

    public function validarLogin(array $dados): array
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

    public function validarRegistro(array $dados): array
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

    public function autenticar(string $email, string $senha): ?Usuario
    {
        $usuario = $this->safe(
            fn() => Usuario::buscarUsuarioCredenciais($this->database, $email),
            'Erro ao consultar banco de dados.'
        );
        return $usuario && $usuario->verificarSenha($senha) ? $usuario : null;
    }

    public function registrar(array $dados): bool
    {
        $dadosFiltrados = [
            'nome' => $dados['nome'] ?? '',
            'email' => $dados['email'] ?? '',
            'senha' => Usuario::hashSenha($dados['senha'] ?? '')
        ];

        $stmt = $this->safe(
            fn() => Usuario::cadastrarUsuario($this->database, $dadosFiltrados),
            'Erro ao inserir usuario no banco de dados.'
        );

        return $stmt->rowCount() > 0;
    }
}
