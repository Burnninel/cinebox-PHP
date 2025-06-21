<?php

class AuthController extends Controller
{
    private $authService;

    public function __construct($database)
    {
        $this->authService = new AuthService($database);
    }

    public function index()
    {
        $this->view('login/index');
    }

    public function registrar()
    {
        redirectNotPost('/login');

        $dados = [
            'nome' => $_POST['nome'],
            'email' => $_POST['email'],
            'senha' => $_POST['senha'],
            'confirmar_senha' => $_POST['confirmar_senha'] ?? ''
        ];

        if (!$this->authService->validarRegistro($dados)) {
            redirect('/login');
        }

        $this->authService->registrar($dados);

        flash()->setMensagem('success', 'Usuário registrado com sucesso!');
        redirect('/login');
    }

    public function autenticar()
    {
        redirectNotPost('/login');

        $dados = [
            'email' => $_POST['email'],
            'senha' => $_POST['senha']
        ];

        if (!$this->authService->validarLogin($dados)) {
            redirect('/login');
        }

        $usuario = $this->authService->autenticar($dados['email'], $dados['senha']);

        if (!$usuario) {
            flashRedirect('error', 'Email ou senha inválidos.', '/login');
        }

        $_SESSION['auth'] = $usuario;

        flashRedirect('success', 'Usuario conectado!', '/');
    }

    public function logout()
    {
        redirectNotPost('/login');
        unset($_SESSION['auth']);
        flashRedirect('success', 'Usuário desconectado!', '/login');
    }
}
