<?php

class UsuarioFilmesController extends Controller
{
    public function index($database)
    {
        $usuarioID = auth()->id;

        if (!$usuarioID) {
            flash()->setMensagem('error', 'Usuário não autenticado', 'usuario');
            header('Location: /login');
            exit;
        }

        $filmes = Filme::getFilmesPorUsuario($database, $usuarioID);

        $this->view('usuarioFilmes/index', ['filmes' => $filmes]);
    }
}