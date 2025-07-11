<?php

namespace Cinebox\App\Core;

use Cinebox\App\Helpers\Log;

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
            Log::error('Erro interno no servidor.',  [
                'exception' => get_class($e),
                'message'   => $e->getMessage(),
                'file'      => $e->getFile(),
                'line'      => $e->getLine(),
                'trace'     => explode("\n", $e->getTraceAsString()),
                'url'       => $_SERVER['REQUEST_URI'] ?? null,
                'ip'        => $_SERVER['REMOTE_ADDR'] ?? null,
                'method'    => $_SERVER['REQUEST_METHOD'] ?? null,
            ]);
            jsonResponse([
                'success' => false,
                'message' => 'Erro interno do servidor.',
                'detalhes' => $e->getMessage()
            ], 500);
        }

        return null;
    }
}
