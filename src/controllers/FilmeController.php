<?php

class FilmeController extends Controller
{
    public function index($database)
    {
        $filmes = $database->query("SELECT * FROM filmes WHERE id = :id", Filme::class, ['id' => $_GET['id']])->fetch();
        $this->view('filme/index', ['filmes' => $filmes]);
    }
}