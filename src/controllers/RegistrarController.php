<?php
session_start();

class RegistrarController extends Controller
{
    public function index($database)
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $nome = $_POST['nome'];
            $email = $_POST['email'];
            $senha = $_POST['senha'];

            $erros = [];

            if (empty($nome)) {
                $erros[] = 'Nome é obrigatório';
            }

            if (empty($email)) {
                $erros[] = 'Email é obrigatório';
            } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $erros[] = 'Email inválido';
            }

            if (empty($senha)) {
                $erros[] = 'Senha é obrigatória';
            } elseif (strlen($senha) < 6) {
                $erros[] = 'Senha deve ter pelo menos 6 caracteres';
            }

            if (empty($erros)) {
                $existingUser = $database->query(
                    "SELECT * FROM usuarios WHERE email = :email",
                    params: ['email' => $email]
                )->fetch();

                if ($existingUser) {
                    $erros[] = 'Email já cadastrado';
                }
            }

            if (!empty($erros)) {
                $_SESSION['errors'] = $erros;
                header('Location: /login');
                exit;
            }

            $database->query(
                "INSERT INTO usuarios (nome, email, senha) VALUES (:nome, :email, :senha)",
                params: [
                    'nome' => $nome,
                    'email' => $email,
                    'senha' => $senha,
                ]
            );

            $_SESSION['success'] = 'Usuário registrado com sucesso!';
            header('Location: /login');
            exit;
        }
    }
}
