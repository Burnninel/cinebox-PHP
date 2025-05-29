<?php

class FilmeController extends Controller
{
    public function index()
    {
        $filmes = ['Matrix'];
        $this->view('filme/index', ['filmes' => $filmes]);
    }
}