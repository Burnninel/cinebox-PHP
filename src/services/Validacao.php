<?php

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
            $valor = $camposForm[$campo] ?? null;
            $confirmacao = $camposForm["confirmar_$campo"] ?? null;

            foreach ($regrasCampo as $regra) {
                [$nomeRegra, $parametro] = array_pad(explode(':', $regra, 2), 2, null);

                if (!method_exists($validacao, $nomeRegra)) {
                    throw new Exception("Regra de validação '$regra' não existe.");
                }

                if ($nomeRegra === 'confirmed') {
                    $validacao->confirmed($campo, $valor, $confirmacao);
                    continue;
                }

                if ($nomeRegra === 'unique') {
                    if (!in_array($campo, self::$colunasPermitidas[$parametro] ?? [], true)) {
                        throw new Exception("Campo '$campo' ou tabela '$parametro' não são aceitos ou estão inválidos.");
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

        return $validacao->erros;
    }

    private function required($campo, $valor)
    {
        if (empty($valor)) {
            $this->erros[] = "$campo é obrigatório";
        }
    }

    private function email($campo, $valor)
    {
        if (!filter_var($valor, FILTER_VALIDATE_EMAIL)) {
            $this->erros[] = "$campo é inválido";
        }
    }

    private function confirmed($campo, $valor, $confirmacao)
    {
        if ($valor !== $confirmacao) {
            $this->erros[] = "$campo e confirmação não coincidem";
        }
    }

    private function unique($campo, $valor, $tabela, $database)
    {
        $emailExiste = $database->query(
            query: "SELECT * FROM $tabela WHERE $campo = :valor",
            params: ['valor' => $valor]
        )->fetch();

        if ($emailExiste) {
            $this->erros[] = "$campo já está em uso";
        }
    }

    private function min($campo, $valor, $min)
    {
        if (strlen((string)$valor) < (int)$min) {
            $this->erros[] = "$campo precisa ter no minimo $min caracteres";
        }
    }

    private function max($campo, $valor, $max)
    {
        if (strlen((string)$valor) > (int)$max) {
            $this->erros[] = "$campo precisa ter no máximo $max caracteres";
        }
    }

    private function strong($campo, $valor)
    {
        if (! strpbrk($valor, '!@#$%¨&*()_.-[/];|?')) {
            $this->erros[] = "$campo precisa ter um caracter especial";
        }
    }
}
