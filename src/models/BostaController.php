<?php

class BostaController extends Controller
{
    public function index()
    {
        $filmes = ['Matrix', 'Interestelar', 'A Origem'];
        $this->view('filme', ['filmes' => $filmes]);
    }
}