<?php

namespace Cinebox\App\Controllers;

use Cinebox\App\Core\BaseController;
use Cinebox\App\Core\Database;

use Cinebox\App\Services\AuthService;

class AuthController extends BaseController
{
    private AuthService $authService;

    public function __construct(Database $database)
    {
        $this->authService = new AuthService($database);
    }

    public function store(): void
    {
        $this->safe(function (): void {
            $dados = getRequestData();

            $erros = $this->authService->validarRegistro($dados);

            if (!empty($erros)) {
                jsonResponse(['status' => false, 'message' => 'Dados inválidos!', 'errors' => $erros], 400);
            }

            $usuario = $this->authService->registrar($dados);

            if (!$usuario) {
                jsonResponse(['status' => false, 'message' => 'Não foi possivel registrar o usuario.'], 422);
            }

            jsonResponse(['status' => true, 'message' => 'Usuário cadastrado com sucesso!']);
        });
    }

    public function login(): void
    {
        $this->safe(function (): void {
            $dados = getRequestData();

            $erros = $this->authService->validarLogin($dados);

            if (!empty($erros)) {
                jsonResponse([
                    'status' => false,
                    'message' => 'Erro ao realizar login.',
                    'errors' => $erros
                ], 400);
            }

            $usuario = $this->authService->autenticar($dados['email'], $dados['senha']);

            if (!$usuario) {
                jsonResponse(['status' => false, 'message' => 'Email ou senha incorretos.'], 401);
            }

            $_SESSION['auth'] = [
                'id' => $usuario->id,
                'nome' => $usuario->nome,
                'email' => $usuario->email
            ];

            jsonResponse([
                'status' => true,
                'message' => 'Usuário autenticado com sucesso.',
                'usuario' => $usuario
            ]);
        });
    }

    public function logout(): void
    {
        unset($_SESSION['auth']);
        jsonResponse(['status' => true, 'message' => 'Usuário desconectado com sucesso.']);
    }
}
