<?php

require_once __DIR__ . '/../vendor/autoload.php';

use Dotenv\Dotenv;

// Carrega variáveis de ambiente se existir arquivo .env
if (file_exists(__DIR__ . '/../.env')) {
    $dotenv = Dotenv::createImmutable(__DIR__ . '/../');
    $dotenv->load();
}

// Configurações do banco de dados
$host = $_ENV['MYSQL_HOST'] ?? $_ENV['HOST'] ?? 'localhost';
$user = $_ENV['MYSQL_USERNAME'] ?? $_ENV['USER'] ?? 'root';
$pass = $_ENV['MYSQL_PASSWORD'] ?? $_ENV['PASS'] ?? 'kodejifr';
$base = $_ENV['MYSQL_DATABASE'] ?? $_ENV['BASE'] ?? 'study_root';
$port = $_ENV['MYSQL_PORT'] ?? $_ENV['PORT'] ?? 3306;

// Detecta se é PostgreSQL ou MySQL baseado na porta
if ($port == 5432 || isset($_ENV['DATABASE_URL'])) {
    // PostgreSQL
    $dsn = "pgsql:host=$host;port=$port;dbname=$base";
    try {
        $conn = new PDO($dsn, $user, $pass);
        $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    } catch(PDOException $e) {
        die("Conexão PostgreSQL falhou: " . $e->getMessage());
    }
} else {
    // MySQL
    $conn = new Mysqli($host, $user, $pass, $base, $port);
    if ($conn->connect_error) {
        die("Conexão MySQL falhou: " . $conn->connect_error);
    }
}