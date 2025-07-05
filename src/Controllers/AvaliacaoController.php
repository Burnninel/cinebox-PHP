<?php

namespace Cinebox\App\Controllers;

use Cinebox\App\Core\AuthenticatedController;
use Cinebox\App\Core\Database;

use Cinebox\App\Services\AvaliacaoService;

use Cinebox\App\Helpers\Response;
use Cinebox\App\Helpers\Request;

class AvaliacaoController extends AuthenticatedController
{
    private AvaliacaoService $avaliacaoService;

    public function __construct(Database $database)
    {
        parent::__construct(); 
        $this->avaliacaoService = new AvaliacaoService($database);
    }

    public function store(int $id): void
    {
        $this->safe(function () use ($id): void {
            $dados = Request::getData();

            $usuario = $this->usuario;

            $erros = $this->avaliacaoService->validarCamposAvaliacao($dados);
            if (!empty($erros)) {
                Response::error('Dados inválidos!', $erros, 400);
            }

            $filmeExiste = $this->avaliacaoService->verificarFilmeExiste($id);
            if (!$filmeExiste) {
                Response::error('Filme não encontrado!', [], 404);
            }

            $avaliado = ($this->avaliacaoService->buscarAvaliacaoUsuarioFilme($id, $usuario->id));
            if ($avaliado) {
                Response::error('Filme já avaliado!', [], 409);
            }

            $avaliacao = $this->avaliacaoService->criarAvaliacao($dados, $id, $usuario->id);

            Response::success('Filme avaliado com sucesso!', [
                'avaliacao' => ['id' => $avaliacao->id],
                'usuario' => ['id' => $avaliacao->usuario_id],
                'filme' => ['id' => $avaliacao->filme_id],
            ]);
        });
    }

    public function destroy(int $id): void
    {
        $this->safe(function () use ($id): void {
            $usuario = $this->usuario;

            $dados = [
                'id' => $id,
                'usuario_id' => $usuario->id
            ];

            $avaliacao = $this->avaliacaoService->buscarAvaliacaoPorId($id);

            if (!$avaliacao) {
                Response::error('Avaliação não encontrada.', [], 404);
            }

            if ($avaliacao->usuario_id !== $usuario->id) {
                Response::error('Você não tem permissão para excluir esta avaliação.', [], 403);
            }

            $this->avaliacaoService->excluirAvaliacao($dados);

            Response::success('Avaliação removida com sucesso!', [
                'avaliacao' => ['id' => $avaliacao->id],
                'usuario' => ['id' => $avaliacao->usuario_id],
                'filme' => ['id' => $avaliacao->filme_id]
            ]);
        });
    }
}
