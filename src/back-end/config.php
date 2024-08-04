<?php

require_once __DIR__ . '/../vendor/autoload.php';

use Dotenv\Dotenv;

$dotenv = Dotenv::createImmutable(__DIR__ . '/../');
$dotenv->load();

$conn = new Mysqli($_ENV['HOST'], $_ENV['USER'], $_ENV['PASS'], $_ENV['BASE'], $_ENV['PORT']);

if ($conn->connect_error) {
    die("Conexão falhou: " . $conn->connect_error);
}