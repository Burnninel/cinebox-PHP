<?php

namespace Cinebox\App\Controllers;

use Cinebox\App\Core\BaseController;
use Cinebox\App\Core\Database;
use Cinebox\App\Services\AuthService;
use Cinebox\App\Services\JwtService;
use Cinebox\App\Helpers\Response;
use Cinebox\App\Helpers\Request;

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
            $dados = Request::getData();

            $erros = $this->authService->validarRegistro($dados);
            if (!empty($erros)) {
                Response::error('Dados inválidos.', $erros, 400);
            }

            $usuario = $this->authService->registrar($dados);

            Response::success('Usuário cadastrado com sucesso!', [
                'nome' => $usuario->nome,
                'email' => $usuario->email
            ]);
        });
    }

    public function login(): void
    {
        $this->safe(function (): void {
            $dados = Request::getData();

            $erros = $this->authService->validarLogin($dados);
            if (!empty($erros)) {
                Response::error('Erro ao realizar login.', $erros, 400);
            }

            $usuario = $this->authService->autenticar($dados['email'], $dados['senha']);

            if (!$usuario) {
                Response::error('Email ou senha incorretos.', [], 401);
            }

            $payload = [
                'id' => $usuario->id,
                'nome' => $usuario->nome,
                'email' => $usuario->email
            ];

            $token = $this->jwtService->gerarToken($payload);

            if (!$token) {
                Response::error('Erro ao autenticar usuario.', [], 401);
            }

            Response::success('Usuário autenticado com sucesso.', [
                'nome' => $usuario->nome,
                'email' => $usuario->email,
                'token' => $token
            ]);
        });
    }

    public function validate(): void
    {
        $this->safe(function (): void {
        $headers = apache_request_headers();
        $authHeader = $headers['Authorization'] ?? null;

        if (!$authHeader || !preg_match('/Bearer\s(\S+)/', $authHeader, $matches)) {
            Response::error('Token não fornecido.', [], 401);
        }

        $token = $matches[1];
        $validate = $this->jwtService->validarToken($token);

        if (!$validate) {
            Response::error('Token inválido ou expirado.', [], 401);
        }

        Response::success('Token válido.', [
            'usuario' => [
                'id' => $validate->id,
                'nome' => $validate->nome,
                'email' => $validate->email
            ]
        ]);
    });
    }
}