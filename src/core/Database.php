<?php

class Database
{
    private $db;

    private $host = 'localhost';
    private $dbname = 'cinebox';
    private $username = 'root';
    private $password = '';

    public function __construct()
    {
        try {
            $dsn = "mysql:host={$this->host};dbname={$this->dbname};charset=utf8";
            $this->db = new PDO($dsn, $this->username, $this->password);
            $this->db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch (PDOException $e) {
            die('Erro ao conectar ao banco de dados: ' . $e->getMessage());
        }
    }

    public function query($query, $params = [])
    {
        $prepare = $this->db->prepare("$query");
        $prepare->execute($params);
        return $prepare;
    }

}