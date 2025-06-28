<?php

Class Service {
    protected function safe($callback, $errorMessage)
    {
        try {
            return $callback();
        } catch (PDOException $e) {
            throw new Exception("{$errorMessage}|||{$e->getMessage()}", 500);
        }
    }
}