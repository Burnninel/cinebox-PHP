<?php

class IndexController extends Controller
{
    public function index($database)
    {
        $pesquisa = $_REQUEST['pesquisa'] ?? '';
        $filmes = $database->query("SELECT * FROM filmes WHERE titulo LIKE :pesquisa", Filme::class, ['pesquisa' => "%$pesquisa%"])->fetchAll();
        $this->view('index', ['filmes' => $filmes]);
    }
}