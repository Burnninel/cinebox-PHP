<?php

class IndexController extends Controller
{
    public function index($database)
    {
        $pesquisar = $_REQUEST['pesquisar'] ?? '';

        if (empty($pesquisar)) {
            flash()->setMensagem('error', 'Por favor, insira um termo de pesquisa.', 'index');
            $filmes = [];
        } else {
            $filmes = Filme::getFilmes($database, $pesquisar);

            if (empty($filmes)) {
                flash()->setMensagem('error', 'Nenhum filme encontrado para a pesquisa: ' . htmlspecialchars($pesquisar), 'index');
            }
        }

        return $this->view('index', ['filmes' => $filmes ?? [], 'pesquisar' => $pesquisar]);
    }
}