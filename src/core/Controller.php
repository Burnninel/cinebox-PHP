<?php

class Controller
{
    protected function safe($callback)
    {
        try {
            return $callback();
        } catch (Throwable $e) {
            [$message, $detalhes] = array_pad(explode('|||', $e->getMessage()), 2, 'Ocorreu um erro interno.');

            jsonResponse([
                'success' => false,
                'message' => $message,
                'detalhes' => $detalhes
            ], 500);
        }
    }
}
