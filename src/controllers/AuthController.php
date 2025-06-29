<?php

namespace Controllers;

use Core\BaseController;
use Services\AuthService;

class AuthController extends BaseController
{
    private AuthService $authService;

    public function __construct($database)
    {
        $this->authService = new AuthService($database);
    }

    public function store()
    {
        $this->safe(function () {
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

    public function login()
    {
        $this->safe(function () {
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

    public function logout()
    {
        unset($_SESSION['auth']);
        jsonResponse(['status' => true, 'message' => 'Usuário desconectado com sucesso.']);
    }
}
