<?php

class AvaliacaoController extends Controller
{
    private $avaliacaoService;

    public function __construct($database)
    {
        $this->avaliacaoService = new AvaliacaoService($database);
    }

    public function store($id)
    {
        $dados = getRequestData();

        $usuario = requireAuthenticatedUser();

        $erros = $this->avaliacaoService->validarDados($dados);

        if (!empty($erros)) {
            jsonResponse(['success' => false, "message" => "Dados inválidos!", "errors" => $erros], 400);
        }

        $filme = $this->avaliacaoService->buscarFilmePorId($id);

        if (!$filme) {
            jsonResponse(['success' => false, "message" => "Filme não encontrado!"], 400);
        }

        $avaliado = ($this->avaliacaoService->buscarAvaliacaoUsuarioFilme($id, $usuario->id));

        if (!empty($avaliado)) {
            jsonResponse(['success' => false, "message" => "Filme já avaliado!"], 400);
        }

        $avaliacao = $this->avaliacaoService->criarAvaliacao($dados, $id, $usuario->id);

        if (!$avaliacao) {
            jsonResponse(['success' => false, "message" => "Erro ao salvar avaliação!"], 400);
        }

        jsonResponse(['success' => true, "message" => "Filme avaliado com sucesso!"]);
    }

    public function destroy($id)
    {
        $usuario = requireAuthenticatedUser();

        $dados = [
            'id' => $id,
            'usuario_id' => $usuario->id
        ];

        $avaliacao = $this->avaliacaoService->buscarAvaliacaoPorId($id);

        if (!$avaliacao) {
            jsonResponse(['success' => false, 'message' => 'Avaliação não encontrada.'], 400);
        }

        if ($avaliacao['usuario_id'] !== $usuario->id) {
            jsonResponse(['success' => false, 'message' => 'Você não tem permissão para excluir esta avaliação.'], 403);
        }

        if (!$this->avaliacaoService->excluirAvaliacao($dados)) {
            jsonResponse(['success' => false, "message" => "Erro ao excluir avaliação!"], 400);
        }

        jsonResponse(['success' => true, "message" => "Avaliação removida com sucesso!"]);
    }
}
