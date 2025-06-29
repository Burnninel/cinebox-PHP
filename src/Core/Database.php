<?php

namespace Core;

use PDO;
use PDOException;
use PDOStatement;

class Database
{
    private PDO $db;

    public function __construct(array $config)
    {
        try {
            $this->db = new PDO($this->getDsn($config), $config['username'], $config['password']);
            $this->db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch (PDOException $e) {
            die('Erro ao conectar ao banco de dados: ' . $e->getMessage());
        }
    }

    public function getDsn(array $config): string
    {
        $driver = $config['driver'];
        unset($config['driver']);

        $dsn = $driver . ':' . http_build_query($config, '', ';');

        return $dsn;
    }

    public function query(string $query, ?string $class = null, array $params = []): PDOStatement
    {
        $prepare = $this->db->prepare("$query");

         if($class) {
            $prepare->setFetchMode(PDO::FETCH_CLASS, $class);
        }

        $prepare->execute($params);
        return $prepare;
    }

    public function lastInsertId(): string
    {
        return $this->db->lastInsertId();
    }
}

$database = new Database(config('database'));