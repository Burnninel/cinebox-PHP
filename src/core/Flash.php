<?php

class Flash
{
    public static function setMensagem($tipo, $mensagem, $campo = null)
    {
        if (!isset($_SESSION['flash'][$tipo])) {
            $_SESSION['flash'][$tipo] = [];
        }

        if($campo) {
            $_SESSION['flash'][$tipo][] = ['campo' => $campo, 'mensagem' => $mensagem];
            return;
        }
        
        $_SESSION['flash'][$tipo][] = ['mensagem' => $mensagem];
    }

    public static function getMensagem($tipo)
    {
        if (isset($_SESSION['flash'][$tipo])) {
            $mensagem = $_SESSION['flash'][$tipo];
            unset($_SESSION['flash'][$tipo]);
            return $mensagem;
        }
        return null;
    }

    public static function hasMensagem($tipo)
    {
        return isset($_SESSION['flash'][$tipo]);
    }
}
