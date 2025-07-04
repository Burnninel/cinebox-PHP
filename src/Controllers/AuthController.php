<?php

namespace Cinebox\App\Controllers;

use Cinebox\App\Core\BaseController;
use Cinebox\App\Core\Database;

use Cinebox\App\Services\AuthService;
use Cinebox\App\Services\JwtService;

class AuthController extends BaseController
{
    private AuthService $authService;
    private JwtService $jwtService;

    public function __construct(Database $database)
    {
        $this->authService = new AuthService($database);
        $this->jwtService = new JwtService;
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

            $payload = [
                'id' => $usuario->id,
                'nome' => $usuario->nome,
                'email' => $usuario->email
            ];

            $token = $this->jwtService->gerarToken($payload);

            if (!$token) {
                jsonResponse(['status' => false, 'message' => 'Email ou senha incorretos.'], 401);
            }

            jsonResponse([
                'status' => true,
                'message' => 'Usuário autenticado com sucesso.',
                'usuario' => [
                    'nome' => $usuario->nome,
                    'email' => $usuario->email
                ],
                'token' => $token
            ]);
        });
    }
}