<?php
require_once __DIR__ . '/../src/core/Helpers.php';

require_once __DIR__ . '/../src/core/Database.php';

// Obter DSN

// $config = config('database');

// $driver = $config['driver'];
// unset($config['driver']);

// $dsn = $driver .':'. http_build_query($config, '', ';');

// dd($dsn['username']);

// ---- Antes do Config ----

// $db = new Database;

// $id = 1;

// $query = "SELECT * FROM filmes";
// // $query = "SELECT * FROM filmes WHERE id = :id";

// $filmes = $db->query($query)->fetchAll();
// // $filmes = $db->query($query, compact('id'))->fetchAll();

// $lista = [];

// foreach ($filmes as $filme) {
//     $lista[] = [
//         'titulo' => $filme['titulo'],
//         'ano_de_lancamento' => $filme['ano_de_lancamento'],
//         'diretor' => $filme['diretor'],
//         'sinopse' => $filme['sinopse'],
//     ];
// }


// Consulta com o Config 

$filmes = $database->query("SELECT * FROM filmes")->fetchAll(PDO::FETCH_ASSOC);

dd($filmes);



if ($database) {
    echo "✅ Conexão com o banco de dados bem-sucedida!";
} else {
    echo "❌ Falha na conexão com o banco de dados.";
}