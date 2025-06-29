<?php

namespace Core;

class BaseController
{
    protected function safe(callable $callback): mixed
    {
        try {
            return $callback();
        } catch (AppException $e) {
            jsonResponse([
                'success' => false,
                'message' => $e->getMessage(),
                'detalhes' => $e->getDetalhes()
            ], 500);
        } catch (\Throwable $e) {
            jsonResponse([
                'success' => false,
                'message' => 'Erro interno do servidor.',
                'detalhes' => $e->getMessage()
            ], 500);
        }

        return null;
    }
}
