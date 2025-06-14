<?php

session_start();

class RegistrarController extends Controller
{
    public function index($database)
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $validacao = Validacao::validarCampos([
                'nome' => ['required'],
                'email' => ['required', 'email', 'unique:usuarios'],
                'senha' => ['required', 'confirmed', 'min:8', 'max:24', 'strong'],
            ], $_POST, $database);

            $validacao->errosValidacao();

            if (flash()->hasMensagem('error')) {
                header('Location: /login');
                exit;
            }

            $database->query(
                "INSERT INTO usuarios (nome, email, senha) VALUES (:nome, :email, :senha)",
                params: [
                    'nome' => $_POST['nome'],
                    'email' => $_POST['email'],
                    'senha' => $_POST['senha'],
                ]
            );

            flash()->setMensagem('success', 'usuario', 'Usuario registrado com sucesso!');
            header('Location: /login');
            exit;
        }
    }
}
