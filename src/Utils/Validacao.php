<?php

namespace Cinebox\App\Utils;

use Cinebox\App\Core\Database;

class Validacao
{
    private array $erros = [];

    private static array $colunasPermitidas = [
        'usuarios' => ['email'],
        'filmes' => ['titulo']
    ];

    public static function validarCampos(array $regrasValidacao, array $camposForm, Database $database): self
    {
        $validacao = new self;
        $validacaoBanco = [];

        foreach ($regrasValidacao as $campo => $regrasCampo) {
            $valor = array_key_exists($campo, $camposForm) ? $camposForm[$campo] : null;
            $confirmacao = $camposForm["confirmar_$campo"] ?? null;

            foreach ($regrasCampo as $regra) {
                [$nomeRegra, $parametro] = array_pad(explode(':', $regra, 2), 2, null);

                if (!method_exists($validacao, $nomeRegra)) {
                    throw new \Exception("Regra de validação '$regra' não existe.");
                }

                if ($nomeRegra === 'confirmed') {
                    $validacao->confirmed($campo, $valor, $confirmacao);
                    continue;
                }

                if ($nomeRegra === 'unique') {
                    if (!in_array($campo, self::$colunasPermitidas[$parametro] ?? [], true)) {
                        throw new \Exception("Campo '$campo' ou tabela '$parametro' não são aceitos ou estão inválidos.");
                    }
                    $validacaoBanco[] = [$campo, $valor, $nomeRegra, $parametro];
                    continue;
                }

                $validacao->$nomeRegra($campo, $valor, $parametro);
            }
        }

        if (empty($validacao->erros)) {
            foreach ($validacaoBanco as [$campo, $valor, $nomeRegra, $parametro]) {
                $validacao->$nomeRegra($campo, $valor, $parametro, $database);
            }
        }

        return $validacao;
    }

    private function required(string $campo, mixed $valor): void
    {
        if (empty($valor)) {
            $this->erros[] = [$campo, "Campo é obrigatório."];
        }
    }

    private function email(string $campo, mixed $valor): void
    {
        if (!filter_var($valor, FILTER_VALIDATE_EMAIL)) {
            $this->erros[] = [$campo, "Formato de e-mail inválido."];
        }
    }

    private function confirmed(string $campo, mixed $valor, mixed $confirmacao): void
    {
        if ($valor !== $confirmacao) {
            $this->erros[] = [$campo, "Valor e confirmação não correspondem."];
        }
    }

    private function unique(string $campo, mixed $valor, string $tabela, Database $database): void
    {
        $emailExiste = $database->query(
            query: "SELECT * FROM $tabela WHERE $campo = :valor",
            params: ['valor' => $valor]
        )->fetch();

        if ($emailExiste) {
            $this->erros[] = [$campo, "Já está em uso."];
        }
    }

    private function min(string $campo, mixed $valor, mixed $min): void
    {
        if (strlen((string)$valor) < (int)$min) {
            $this->erros[] = [$campo, "Mínimo de $min caracteres."];
        }
    }

    private function max(string $campo, mixed $valor, mixed $max): void
    {
        if (strlen((string)$valor) > (int)$max) {
            $this->erros[] = [$campo, "Máximo de $max caracteres."];
        }
    }

    private function strong(string $campo, mixed $valor): void
    {
        if (!$valor || !strpbrk($valor, '!@#$%¨&*()_.-[/];|?')) {
            $this->erros[] = [$campo, "Deve conter ao menos um caractere especial."];
        }
    }

    private function string(string $campo, mixed $valor): void
    {
        if (!$valor || !preg_match('/^[a-zA-ZÀ-ÿ\s\.-]+$/u', $valor)) {
            $this->erros[] = [$campo, "Contém caracteres inválidos."];
        }
    }

    private function numeric(string $campo, mixed $valor): void
    {
        if (!is_numeric($valor)) {
            $this->erros[] = [$campo, "Deve ser um valor numérico."];
        }
    }

    private function between(string $campo, mixed $valor, string $between): void
    {
        [$min, $max] = array_pad(explode('-', $between, 2), 2, null);

        if ($valor < $min || $valor > $max) {
            $this->erros[] = [$campo, "Deve estar entre $min e $max."];
            return;
        }
    }

    private function length(string $campo, mixed $valor, int $length): void
    {
        if (strlen((string) $valor) != $length) {
            $this->erros[] = [$campo, "Deve conter exatamente $length dígitos."];
        }
    }

    public function erros(): array
    {
        $resultado = [];

        foreach ($this->erros as $erro) {
            [$campo, $mensagem] = $erro;

            if (!isset($resultado[$campo])) {
                $resultado[$campo] = $mensagem;
            }
        }

        return $resultado;
    }
}
