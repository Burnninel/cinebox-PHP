<?php

namespace Utils;

class Validacao
{
    private $erros = [];

    private static $colunasPermitidas = [
        'usuarios' => ['email'],
        'filmes' => ['titulo']
    ];

    public static function validarCampos($regrasValidacao, $camposForm, $database)
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

    private function required($campo, $valor)
    {
        if (empty($valor)) {
            $this->erros[] = [$campo, "Campo é obrigatório."];
        }
    }

    private function email($campo, $valor)
    {
        if (!filter_var($valor, FILTER_VALIDATE_EMAIL)) {
            $this->erros[] = [$campo, "Formato de e-mail inválido."];
        }
    }

    private function confirmed($campo, $valor, $confirmacao)
    {
        if ($valor !== $confirmacao) {
            $this->erros[] = [$campo, "Valor e confirmação não correspondem."];
        }
    }

    private function unique($campo, $valor, $tabela, $database)
    {
        $emailExiste = $database->query(
            query: "SELECT * FROM $tabela WHERE $campo = :valor",
            params: ['valor' => $valor]
        )->fetch();

        if ($emailExiste) {
            $this->erros[] = [$campo, "Já está em uso."];
        }
    }

    private function min($campo, $valor, $min)
    {
        if (strlen((string)$valor) < (int)$min) {
            $this->erros[] = [$campo, "Mínimo de $min caracteres."];
        }
    }

    private function max($campo, $valor, $max)
    {
        if (strlen((string)$valor) > (int)$max) {
            $this->erros[] = [$campo, "Máximo de $max caracteres."];
        }
    }

    private function strong($campo, $valor)
    {
        if (!$valor || !strpbrk($valor, '!@#$%¨&*()_.-[/];|?')) {
            $this->erros[] = [$campo, "Deve conter ao menos um caractere especial."];
        }
    }

    private function string($campo, $valor)
    {
        if (!$valor || !preg_match('/^[a-zA-ZÀ-ÿ\s\.-]+$/u', $valor)) {
            $this->erros[] = [$campo, "Contém caracteres inválidos."];
        }
    }

    private function numeric($campo, $valor)
    {
        if (!is_numeric($valor)) {
            $this->erros[] = [$campo, "Deve ser um valor numérico."];
        }
    }

    private function between($campo, $valor, $between)
    {
        [$min, $max] = array_pad(explode('-', $between, 2), 2, null);

        if ($valor < $min || $valor > $max) {
            $this->erros[] = [$campo, "Deve estar entre $min e $max."];
        }
    }

    private function length($campo, $valor, $length)
    {
        if (strlen($valor) != $length) {
            $this->erros[] = [$campo, "Deve conter exatamente $length dígitos."];
        }
    }

    public function erros()
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
