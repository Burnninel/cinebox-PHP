<?php

class FilmeController extends Controller
{
    private $filmeService;

    public function __construct($database)
    {
        $this->filmeService = new FilmeService($database);
    }

    public function index()
    {
        $filmeId = $_GET['id'] ?? null;
        $filme = $filmeId ? $this->filmeService->buscarFilmePorId($filmeId) : null;

        if (!$filme) {
            flashRedirect('error', 'Filme não encontrado!', '/', 'filme');
        }

        $this->view('filme/index', ['filme' => $filme]);
    }

    public function meusFilmes()
    {
        $usuario_id = usuarioAutenticadoOuRedireciona('/login');

        $filmes = $this->filmeService->buscarFilmesUsuario($usuario_id);

        $this->view('usuarioFilmes/index', ['filmes' => $filmes]);
    }

    public function novoFilme()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->view('filme/novoFilme');
            return;
        }

        $usuario_id = usuarioAutenticadoOuRedireciona('/filme/novoFilme');

        $dados = [
            'titulo' => $_POST['titulo'] ?? '',
            'diretor' => $_POST['diretor'] ?? '',
            'categoria' => $_POST['categoria'] ?? '',
            'sinopse' => $_POST['sinopse'] ?? '',
            'ano_de_lancamento' => $_POST['ano_de_lancamento'],
        ];

        if (!$this->filmeService->validarDados($dados)) {
            redirect('/filme/novoFilme');
        }

        $this->filmeService->criarFilme($dados, $usuario_id);

        flashRedirect('success', 'Filme cadastrado com sucesso!', '/filme/novoFilme', 'filme');
    }

     public function favoritarFilme()
    {
        redirectNotPost('/');
        
        $filme_id = $_GET['id'] ?? null;
        $usuario_id = usuarioAutenticadoOuRedireciona("/filme?id=$filme_id");
        $dados = [
            'usuario_id' => $usuario_id,
            'filme_id' => $filme_id
        ];

        $filme = $filme_id ? $this->filmeService->buscarFilmePorId($filme_id) : null;

        if (!$filme) {
            flashRedirect('error', 'Erro ao salvar: Filme não encontrado!', '/', 'filme');
        }
        
        if ($this->filmeService->verificarFilmeFavoritado($dados)) {
            flashRedirect('error', 'Filme já salvo!', "/filme?id=$filme_id", 'filme');
        }

        $this->filmeService->favoritarFilme($dados);
        flashRedirect('success', 'Filme salvo com sucesso!', "/filme?id=$filme_id", 'filme');
    }

     public function desfavoritarFilme()
    {
        redirectNotPost('/');
        
        $filme_id = $_GET['id'] ?? null;
        $usuario_id = usuarioAutenticadoOuRedireciona("/filme?id=$filme_id");
        $dados = [
            'usuario_id' => $usuario_id,
            'filme_id' => $filme_id
        ];

        $filme = $filme_id ? $this->filmeService->buscarFilmePorId($filme_id) : null;

        if (!$filme) {
            flashRedirect('error', 'Erro ao salvar: Filme não encontrado!', '/', 'filme');
        }
        
        if (!$this->filmeService->verificarFilmeFavoritado($dados)) {
            flashRedirect('error', 'Filme não está favoritado!', "/filme?id=$filme_id", 'filme');
        }

        $this->filmeService->desfavoritarFilme($dados);
        flashRedirect('success', 'Filme removido dos salvos!', "/filme?id=$filme_id", 'filme');
    }
}
