<?php

namespace Core;

use Models\Usuario;

Class BaseService {
    protected function safe($callback, $errorMessage)
    {
        try {
            return $callback();
        } catch (\PDOException $e) {
            throw new AppException($errorMessage, $e->getMessage(), 500);
        }
    }
}

