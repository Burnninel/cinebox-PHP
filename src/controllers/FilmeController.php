<?php

class FilmeController extends Controller
{
    public function index($database)
    {
        $filmeId = $_GET['id'] ?? null;

        $filme = Filme::getFilme($database, $filmeId);
        
        if (!$filme) {
            flash()->setMensagem('error', 'Filme não encontrado', 'filme');
            header('Location: /filmes');
            exit;
        }

        $this->view('filme/index', ['filme' => $filme]);
    }
}