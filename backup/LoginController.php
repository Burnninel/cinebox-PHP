<?php

class LoginController extends Controller
{
    public function index($database)
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $email = $_POST['email'];
            $senha = $_POST['senha'];

            $validacao = Validacao::validarCampos([
                'email' => ['required', 'email'],
                'senha' => ['required']
            ], $_POST, $database);

            $validacao->errosValidacao();

            if (flash()->hasMensagem('error')) {
                header('Location: /login');
                exit;
            }

            $usuario = $database->query(
                query: "SELECT * FROM usuarios WHERE email = :email",
                class: Usuario::class,
                params: compact('email')
            )->fetch();

            if (! $usuario->verificarSenha($senha)) {
                flash()->setMensagem('error', 'Email ou senha inválidos', 'email');
                header('Location: /login');
                exit;
            }

            $_SESSION['auth'] = $usuario;

            flash()->setMensagem('success', 'Parabens!', 'usuario');
            header('Location: /login');
            exit;
        }

        $this->view('login/index');
    }
}


