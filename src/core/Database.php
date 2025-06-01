<?php

class Database
{
    private $db;

    public function __construct($config)
    {
        try {
            $this->db = new PDO($this->getDsn($config), $config['username'], $config['password']);
            $this->db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch (PDOException $e) {
            die('Erro ao conectar ao banco de dados: ' . $e->getMessage());
        }
    }

    public function getDsn($config)
    {
        $driver = $config['driver'];
        unset($config['driver']);

        $dsn = $driver . ':' . http_build_query($config, '', ';');

        return $dsn;
    }

    public function query($query, $class = null, $params = [])
    {
        $prepare = $this->db->prepare("$query");

         if($class) {
            $prepare->setFetchMode(PDO::FETCH_CLASS, $class);
        }

        $prepare->execute($params);
        return $prepare;
    }
}

$database = new Database(config('database'));