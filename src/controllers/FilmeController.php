<?php

class FilmeController extends Controller
{
    private $filmeService;

    public function __construct($database)
    {
        $this->filmeService = new FilmeService($database);
    }

    private function redirecionar($url)
    {
        header("Location: $url");
        exit;
    }

    public function index()
    {
        $filmeId = $_GET['id'] ?? null;
        $filme = $filmeId ? $this->filmeService->buscarFilmePorId($filmeId) : null;

        if (!$filme) {
            $this->redirecionarComMensagem('error', 'Filme não encontrado!', '/', 'filme');
        }

        $this->view('filme/index', ['filme' => $filme]);
    }

    public function meusFilmes()
    {
        $usuario_id = $this->usuarioAutenticadoOuRedireciona('/login');

        $filmes = $this->filmeService->buscarFilmesUsuario($usuario_id);

        $this->view('usuarioFilmes/index', ['filmes' => $filmes]);
    }

    public function novoFilme()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->view('filme/novoFilme');
            return;
        }

        $usuario_id = $this->usuarioAutenticadoOuRedireciona('/filme/novoFilme');

        $dados = [
            'titulo' => $_POST['titulo'] ?? '',
            'diretor' => $_POST['diretor'] ?? '',
            'categoria' => $_POST['categoria'] ?? '',
            'sinopse' => $_POST['sinopse'] ?? '',
            'ano_de_lancamento' => $_POST['ano_de_lancamento'],
        ];

        if (!$this->filmeService->validarDados($dados)) {
            $this->redirecionar('/filme/novoFilme');
        }

        $this->filmeService->criarFilme($dados, $usuario_id);

        $this->redirecionarComMensagem('success', 'Filme cadastrado com sucesso!', '/filme/novoFilme', 'filme');
    }

    private function redirecionarComMensagem($status, $mensagem, $url, $chave)
    {
        flash()->setMensagem($status, $mensagem, $chave);
        header("Location: $url");
        exit;
    }

    private function usuarioAutenticadoOuRedireciona($url)
    {
        $usuario_id = $this->filmeService->obterUsuarioAutenticado();

        if (!$usuario_id) {
            $this->redirecionarComMensagem('error', 'Usuário não autenticado!', $url, 'filme');
        }

        return $usuario_id;
    }
}
