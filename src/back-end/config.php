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

$conn = new Mysqli($host, $user, $pass, $base, $port);

if ($conn->connect_error) {
    die("Conexão falhou: " . $conn->connect_error);
}