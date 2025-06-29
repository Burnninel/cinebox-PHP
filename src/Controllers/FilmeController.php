<?php

namespace Controllers;

use Core\BaseController;
use Core\Database;
use Services\FilmeService;
use Services\AvaliacaoService;

class FilmeController extends BaseController
{
    private FilmeService $filmeService;
    private AvaliacaoService $avaliacaoService;

    public function __construct(Database $database)
    {
        $this->filmeService = new FilmeService($database);
        $this->avaliacaoService = new AvaliacaoService($database);
    }

    public function index(): void
    {
        $this->safe(function (): void {
            $pesquisar = trim($_GET['pesquisar'] ?? '');

            $filmes = $this->filmeService->buscarFilmes($pesquisar);

            if (empty($filmes)) {
                jsonResponse(['status' => false, 'message' => 'Nenhum filme encontrado para o termo informado.', 'filmes' => []]);
            }

            jsonResponse(['status' => true, 'message' => 'Filmes localizados com sucesso!', 'filmes' => $filmes]);
        });
    }

    public function show(int $id): void
    {
        $this->safe(function () use ($id): void {
            $filme = $this->filmeService->buscarFilmePorId($id);

            if (!$filme) {
                jsonResponse(['status' => false, 'message' => 'Filme não encontrado!'], 400);
            }

            $avaliacoes = $this->avaliacaoService->listarAvaliacoes($id);

            jsonResponse([
                'status' => true,
                'message' => 'Filme localizado com sucesso!',
                'data' => [
                    'filme' => $filme,
                    'avaliacoes' => $avaliacoes
                ]
            ]);
        });
    }

    public function meusFilmes(): void
    {
        $this->safe(function (): void {
            $usuario = requireAuthenticatedUser();

            $filmes = $this->filmeService->buscarFilmesUsuario($usuario->id);

            if (empty($filmes)) {
                jsonResponse([
                    'status' => false,
                    'message' => 'Nenhum filme encontrado para este usuário.',
                    'filmes' => []
                ]);
            }

            jsonResponse([
                'status' => true,
                'message' => 'Filmes localizados com sucesso!',
                'filmes' => $filmes
            ]);
        });
    }

    public function store(): void
    {
        $this->safe(function (): void {
            $dados = getRequestData();

            $usuario = requireAuthenticatedUser();

            $erros = $this->filmeService->validarDados($dados);

            if (!empty($erros)) {
                jsonResponse([
                    'status' => false,
                    'message' => 'Erro ao cadastrar filme.',
                    'errors' => $erros
                ], 400);
            }

            $this->filmeService->criarFilme($dados, $usuario->id);

            jsonResponse(['status' => true, 'message' => 'Filme cadastrado com sucesso!']);
        });
    }

    public function favoritarFilme(int $id): void
    {
        $this->safe(function ()  use ($id): void {
            $usuario = requireAuthenticatedUser();

            $resultado = $this->filmeService->obterStatusFilmeParaUsuario($id, $usuario->id);

            if (!$resultado['valido']) {
                jsonResponse(['status' => false, 'message' => $resultado['mensagem']], 400);
            }

            if ($resultado['favoritado']) {
                jsonResponse(['status' => false, 'message' => 'Filme já favoritado!'], 400);
            }

            $favoritar = $this->filmeService->favoritarFilme($resultado['dados']);

            if (!$favoritar) {
                jsonResponse(['status' => false, 'message' => 'Erro ao favoritar filme!'], 400);
            }

            jsonResponse(['status' => true, 'message' => 'Adicionado aos favoritos!']);
        });
    }

    public function desfavoritarFilme(int $id): void
    {
        $this->safe(function ()  use ($id): void {
            $usuario = requireAuthenticatedUser();

            $resultado = $this->filmeService->obterStatusFilmeParaUsuario($id, $usuario->id);

            if (!$resultado['valido']) {
                jsonResponse(['status' => false, 'message' => $resultado['mensagem']], 400);
            }

            if (!$resultado['favoritado']) {
                jsonResponse(['status' => false, 'message' => 'Filme não esta favoritado!'], 400);
            }

            $desfavoritar = $this->filmeService->desfavoritarFilme($resultado['dados']);

            if (!$desfavoritar) {
                jsonResponse(['status' => false, 'message' => 'Erro ao desfavoritar filme!'], 400);
            }

            jsonResponse(['status' => true, 'message' => 'Removido dos favoritos!']);
        });
    }
}
