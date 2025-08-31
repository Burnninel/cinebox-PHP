<?php

namespace Cinebox\App\Services;

use Cinebox\App\Core\BaseService;
use Cinebox\App\Core\Database;
use Cinebox\App\Models\Filme;
use Cinebox\App\Helpers\Insert;
use Cinebox\App\Helpers\Log;
use Cinebox\App\Utils\Validacao;

class FilmeService extends BaseService
{
    protected Database $database;

    public function __construct(Database $database)
    {
        $this->database = $database;
    }

    private function validarDadosDeFavorito(array $dados): void
    {
        if (
            !isset($dados['filme_id'], $dados['usuario_id']) ||
            !is_numeric($dados['filme_id']) || !is_numeric($dados['usuario_id'])
        ) {
            throw new \InvalidArgumentException('Dados inválidos para favoritar/desfavoritar filme.');
        }
    }

    public function buscarFilmes(string $pesquisar, ?int $usuario_id = null): array
    {
        return $this->safe(
            fn() => Filme::buscarFilmes($this->database, $pesquisar, $usuario_id),
            'Erro ao buscar filmes no banco de dados.'
        );
    }

    public function buscarFilmePorId(int $id): ?Filme
    {
        return $this->safe(
            fn() => Filme::buscarFilmePorId($this->database, $id),
            'Erro ao buscar filme no banco de dados.'
        );
    }

    public function buscarFilmesUsuario(int $usuario_id): array
    {
        $resultado = $this->safe(
            fn() => Filme::buscarFilmesPorUsuario($this->database, $usuario_id),
            'Erro ao buscar filmes do usuario no banco de dados.'
        );

        return is_array($resultado) ? $resultado : [];
    }

    public function validarDados(array $dados): array
    {
        $regras = [
            'titulo' => ['required', 'min:3', 'unique:filmes'],
            'diretor' => ['required', 'min:6', 'string'],
            'categoria' => ['required', 'string'],
            'sinopse' => ['required'],
            'ano_de_lancamento' => [
                'required',
                'numeric',
                'max:4',
                'min:4',
                'between:1900-2025',
                'length:4'
            ]
        ];

        $validador = Validacao::validarCampos($regras, $dados, $this->database);

        return $validador->erros();
    }

    public function criarFilme(array $dados, int $usuario_id): Filme
    {
        $filme = $this->safe(
            fn() => Insert::execute(
                fn() => Filme::criarFilme($this->database, $dados, $usuario_id),
                fn($id) => Filme::buscarFilmePorId($this->database, $id)
            ),
            'Erro ao incluir filme no banco de dados.'
        );

        Log::info('Filme criado.', [
            'id' => $filme->id,
            'usuario_id' => $usuario_id,
            'titulo' => $filme->titulo
        ]);

        return $filme;
    }

    public function favoritarFilme(array $dados): bool
    {
        $this->validarDadosDeFavorito($dados);

        $stmt = $this->safe(
            fn() => Filme::favoritarFilme($this->database, $dados),
            'Erro ao registrar filme favorito no banco de dados.'
        );

        if ($stmt !== false && $stmt->rowCount() > 0) {
            Log::info('Filme favoritado com sucesso.', [
                'filme_id' => $dados['filme_id'],
                'usuario_id' => $dados['usuario_id']
            ]);

            return true;
        } else {
            Log::warning('Tentativa de favoritar filme não resultou em alteração.', [
                'filme_id' => $dados['filme_id'],
                'usuario_id' => $dados['usuario_id']
            ]);

            return false;
        }
    }

    public function obterStatusFilmeParaUsuario(int $filme_id, int $usuario_id): array
    {
        $dados = [
            'filme_id' => $filme_id,
            'usuario_id' => $usuario_id
        ];

        $filme = $this->safe(
            fn() => Filme::buscarFilmePorId($this->database, $filme_id),
            'Erro ao consultar filme no banco de dados.'
        );

        if (!$filme) {
            return [
                'valido' => false,
                'mensagem' => 'Filme não encontrado.'
            ];
        }

        $favoritado = $this->safe(
            fn() => Filme::verificarFilmeFavoritado($this->database, $dados),
            'Erro ao consultar filme no banco de dados.'
        );

        return [
            'valido' => true,
            'filme' => $filme,
            'favoritado' => $favoritado,
            'dados' => $dados
        ];
    }

    public function desfavoritarFilme(array $dados): bool
    {
        $this->validarDadosDeFavorito($dados);

        $stmt = $this->safe(
            fn() => Filme::desfavoritarFilme($this->database, $dados),
            'Erro ao remover filme dos favoritos no banco de dados.'
        );

        if ($stmt !== false && $stmt->rowCount() > 0) {
            Log::info('Filme removido dos favoritos com sucesso.', [
                'filme_id' => $dados['filme_id'],
                'usuario_id' => $dados['usuario_id']
            ]);

            return true;
        } else {
            Log::warning('Tentativa de desfavoritar filme não resultou em alteração.', [
                'filme_id' => $dados['filme_id'],
                'usuario_id' => $dados['usuario_id']
            ]);

            return false;
        }
    }
}