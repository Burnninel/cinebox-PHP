<?php

namespace Core;

Class BaseService {
    protected function safe(callable $callback, string $errorMessage): mixed
    {
        try {
            return $callback();
        } catch (\PDOException $e) {
            throw new AppException($errorMessage, $e->getMessage(), 500);
        }
    }
}

