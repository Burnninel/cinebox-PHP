<?php

namespace Cinebox\App\Services;

use Cinebox\App\Core\BaseService;
use Cinebox\App\Core\Database;

use Cinebox\App\Models\Filme;
use Cinebox\App\Models\Avaliacao;

use Cinebox\App\Utils\Validacao;

use Cinebox\App\Helpers\Insert;
use Cinebox\App\Helpers\Log;

class AvaliacaoService extends BaseService
{
    protected Database $database;

    public function __construct(Database $database)
    {
        $this->database = $database;
    }

    private function ensureValidId(int $id): void
    {
        if ($id <= 0) {
            throw new \InvalidArgumentException('ID inválido.');
        }
    }

    public function verificarFilmeExiste(int $id): bool
    {
        $this->ensureValidId($id);

        $resultado = $this->safe(
            fn() => Filme::buscarFilmePorId($this->database, $id),
            'Erro ao buscar filme no banco de dados.'
        );

        return $resultado !== null;
    }

    public function validarCamposAvaliacao(array $dados): array
    {
        $regras = [
            'nota' => ['required', 'numeric', 'between:1-5', 'length:1'],
            'comentario' => ['required', 'min:15']
        ];

        $validador = Validacao::validarCampos($regras, $dados, $this->database);

        return $validador->erros();
    }

    public function criarAvaliacao(array $dados, int $id, int $usuario_id): ?Avaliacao
    {
        $this->ensureValidId($id);

        $dados += [
            'filme_id' => $id,
            'usuario_id' => $usuario_id
        ];

        $avaliacao = $this->safe(
            fn() => Insert::execute(
                fn() => Avaliacao::criarAvaliacao($this->database, $dados),
                fn() => Avaliacao::buscarAvaliacaoUsuarioFilme($this->database, $dados['filme_id'], $dados['usuario_id'])
            ),
            'Erro ao incluir avaliação no banco de dados.'
        );

        Log::info('Avaliação registrada.', [
            'id' => $avaliacao->id,
            'usuario_id' => $avaliacao->usuario_id,
            'filme_id' => $avaliacao->filme_id
        ]);

        return $avaliacao;
    }

    public function listarAvaliacoes(int $id): array
    {
        $this->ensureValidId($id);

        return $this->safe(
            fn() => Avaliacao::buscarAvaliacoesFilme($this->database, $id),
            'Erro ao consultar avaliações do filme no banco de dados.'
        );
    }

    public function buscarAvaliacaoUsuarioFilme(int $id, int $usuario_id): ?Avaliacao
    {
        $this->ensureValidId($id);

        return $this->safe(
            fn() => Avaliacao::buscarAvaliacaoUsuarioFilme($this->database, $id, $usuario_id),
            'Erro ao consultar avaliacoes do usuario no banco de dados.'
        );
    }

    public function buscarAvaliacaoPorId(int $id): ?Avaliacao
    {
        $this->ensureValidId($id);

        $avaliacao = $this->safe(
            fn() => Avaliacao::buscarAvaliacao($this->database, $id),
            'Erro ao consultar avaliação no banco de dados.'
        );
        return $avaliacao;
    }

    public function excluirAvaliacao(array $dados): bool
    {
        $avaliacao = $this->safe(
            fn() => Avaliacao::removerAvaliacao($this->database, $dados),
            'Erro ao consultar avaliacao no banco de dados.'
        );

        Log::info('Avaliação removida.', [
            'id' => $dados['id'],
            'usuario_id' => $dados['usuario_id']
        ]);

        return $avaliacao;
    }
}
