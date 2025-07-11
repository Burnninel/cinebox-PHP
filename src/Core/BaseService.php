<?php

namespace Cinebox\App\Core;

use Cinebox\App\Helpers\Log;

class BaseService
{
    protected function safe(callable $callback, string $errorMessage): mixed
    {
        try {
            return $callback();
        } catch (\PDOException $e) {
            Log::error($errorMessage,  [
                'exception' => get_class($e),
                'message'   => $e->getMessage(),
                'file'      => $e->getFile(),
                'line'      => $e->getLine(),
                'trace'     => explode("\n", $e->getTraceAsString()),
                'url'       => $_SERVER['REQUEST_URI'] ?? null,
                'ip'        => $_SERVER['REMOTE_ADDR'] ?? null,
                'method'    => $_SERVER['REQUEST_METHOD'] ?? null,
            ]);

            throw new AppException($errorMessage, $e->getMessage(), 500);
        }
    }
}
