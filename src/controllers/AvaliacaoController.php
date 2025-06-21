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

        $filme_id = validarIdOuRedirecionar('id', '/', 'Filme não encontrado!');
        $usuario_id = usuarioAutenticadoOuRedireciona("/filme?id=$filme_id");

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
            flashRedirect('error', 'Erro ao salvar avaliação!', "/filme/index?id=$filme_id");
        }

        flashRedirect('success', 'Avaliação criada com sucesso!', "/filme/index?id=$filme_id");
    }
}
