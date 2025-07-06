<?php 

namespace Cinebox\App\Helpers;

class Insert 
{
    public static function execute(callable $insert, callable $consulta) {
        $stmt = $insert();

        if (!is_numeric($stmt) && method_exists($stmt, 'rowCount') && $stmt->rowCount() === 0 ) {
            throw new \Exception('Erro ao processar a inserção no banco de dados. Tente novamente.');
        }

        return $consulta($stmt);
    }
}