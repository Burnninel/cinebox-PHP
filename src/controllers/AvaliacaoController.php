<?php

class AvaliacaoController extends Controller
{
    private $avaliacaoService;

    public function __construct($database)
    {
        $this->avaliacaoService = new AvaliacoesService($database);
    }

    public function criarAvaliacao()
    {
        redirectNotPost('/');

        $filme_id = $_GET['id'] ?? null;
        $usuario_id = $this->usuarioAutenticadoOuRedireciona("/filme?id=$filme_id");

        $dados = [
            'usuario_id' => $usuario_id,
            'filme_id' => $filme_id,
            'nota' => $_POST['nota'] ?? '',
            'comentario' => $_POST['comentario'] ?? '',
        ];

        if (!$this->avaliacaoService->validarDados($dados)) {
            redirect("/filme/index?id=$filme_id");
        }

        if (!$this->avaliacaoService->criarAvaliacao($dados)) {
            flashRedirect('error', 'Erro ao salvar avaliação!', "/filme/index?id=$filme_id", 'avaliacao');
        }

        flashRedirect('success', 'Avaliação criada com sucesso!', "/filme/index?id=$filme_id", 'avaliacao');
    }

    private function usuarioAutenticadoOuRedireciona($url)
    {
        $usuario_id = $this->avaliacaoService->obterUsuarioAutenticado();

        if (!$usuario_id) {
            flashRedirect('error', 'Usuário não autenticado!', $url, 'filme');
        }

        return $usuario_id;
    }
}
