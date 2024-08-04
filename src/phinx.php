<?php

require_once __DIR__ . '/vendor/autoload.php';

$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->load();

$host = $_ENV['HOST'];
$name = $_ENV['BASE'];
$user = $_ENV['USER'];
$pass = $_ENV['PASS'];
$port = $_ENV['PORT'];

return
[
    'paths' => [
        'migrations' => '%%PHINX_CONFIG_DIR%%/db/migrations',
        'seeds' => '%%PHINX_CONFIG_DIR%%/db/seeds'
    ],
    'environments' => [
        'default_migration_table' => 'phinxlog',
        'default_environment' => 'development',
        'development' => [
            'adapter' => 'mysql',
            'host' => $host,
            'name' => $name,
            'user' => 'root',
            'pass' => $pass,
            'port' => $port,
            'charset' => 'utf8',
        ]
    ],
    'version_order' => 'creation'
];
