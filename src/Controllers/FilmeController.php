<?php

namespace Cinebox\App\Controllers;

use Cinebox\App\Core\BaseController;
use Cinebox\App\Core\Database;
use Cinebox\App\Services\FilmeService;
use Cinebox\App\Services\AvaliacaoService;
use Cinebox\App\Middlewares\AuthMiddleware;
use Cinebox\App\Middlewares\OptionalAuthMiddleware;
use Cinebox\App\Helpers\Response;
use Cinebox\App\Helpers\Request;

class FilmeController extends BaseController
{
    private FilmeService $filmeService;
    private AvaliacaoService $avaliacaoService;
    private AuthMiddleware $authMiddleware;

    public function __construct(Database $database)
    {
        $this->filmeService = new FilmeService($database);
        $this->avaliacaoService = new AvaliacaoService($database);
        $this->authMiddleware = new AuthMiddleware($database);
    }

    public function index(): void
    {
        $this->safe(function (): void {
            $pesquisar = trim($_GET['pesquisar'] ?? '');

            $filmes = $this->filmeService->buscarFilmes($pesquisar);

            if (empty($filmes)) {
                Response::success('Nenhum filme encontrado para o termo informado.', []);
            }

            Response::success('Filmes localizados com sucesso!', $filmes);
        });
    }

    public function show(int $id): void
    {
        $this->safe(function () use ($id): void {
            $payload = (new OptionalAuthMiddleware())->handle();
            $usuario_id = $payload->id ?? null;

            $filme = $this->filmeService->buscarFilmePorId($id);

            if (!$filme) {
                Response::error('Filme não encontrado!', [], 404);
            }

            $avaliacoes = $this->avaliacaoService->listarAvaliacoes($id, $usuario_id);

            Response::success('Filme localizados com sucesso!', [
                'filme' => $filme,
                'avaliacoes' => $avaliacoes
            ]);
        });
    }

    public function meusFilmes(): void
    {
        $this->safe(function (): void {
            $usuario = $this->authMiddleware->handle();
            $pesquisar = trim($_GET['pesquisar'] ?? '');

             if ($pesquisar) {
                $filmes = $this->filmeService->buscarFilmes($pesquisar, $usuario->id);
            } else {
                $filmes = $this->filmeService->buscarFilmesUsuario($usuario->id);
            }

            if (empty($filmes)) {
                Response::success('Nenhum filme encontrado para este usuário.', [
                    'filmes' => []
                ]);
                return;
            }

            Response::success('Filmes localizados com sucesso!', ['filmes' => $filmes]);
        });
    }

    public function store(): void
    {
        $this->safe(function (): void {
            $dados = Request::getData();

            $usuario = $this->authMiddleware->handle();

            $erros = $this->filmeService->validarDados($dados);
            if (!empty($erros)) {
                Response::error('Dados inválidos!', $erros, 400);
            }

            $filme = $this->filmeService->criarFilme($dados, $usuario->id);

            Response::success('Filme cadastrado com sucesso!', [
                'filme' => [
                    'id' => $filme->id,
                    'titulo' => $filme->titulo,
                    'diretor' => $filme->diretor,
                    'categoria' => $filme->categoria,
                    'imagem' => $filme->imagem,
                ]
            ]);
        });
    }

    public function favoritarFilme(int $id): void
    {
        $this->safe(function ()  use ($id): void {
            $usuario = $this->authMiddleware->handle();

            $resultado = $this->filmeService->obterStatusFilmeParaUsuario($id, $usuario->id);

            if (!$resultado['valido']) {
                Response::error($resultado['mensagem'], [], 404);
            }

            if ($resultado['favoritado']) {
                Response::error('Filme já favoritado!', [], 409);
            }

            $favoritar = $this->filmeService->favoritarFilme($resultado['dados']);
            if (!$favoritar) {
                Response::error('Erro ao favoritar filme! Tente novamente.', [], 500);
            }

            Response::success('Adicionado aos favoritos!', [
                'filme' => [
                    'id' => $resultado['filme']->id,
                    'favoritado' => true
                ]
            ]);
        });
    }

    public function desfavoritarFilme(int $id): void
    {
        $this->safe(function ()  use ($id): void {
            $usuario = $this->authMiddleware->handle();

            $resultado = $this->filmeService->obterStatusFilmeParaUsuario($id, $usuario->id);

            if (!$resultado['valido']) {
                Response::error($resultado['mensagem'], [], 404);
            }

            if (!$resultado['favoritado']) {
                Response::error('Filme não esta favoritado!', [], 409);
            }

            $desfavoritar = $this->filmeService->desfavoritarFilme($resultado['dados']);
            if (!$desfavoritar) {
                Response::error('Erro ao desfavoritar filme! Tente novamente.', [], 500);
            }

            Response::success('Removido dos favoritos!', [
                'filme' => [
                    'id' => $resultado['filme']->id,
                    'favoritado' => false
                ]
            ]);
        });
    }
}