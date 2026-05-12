<?php
/**
 * Conexão única com o banco via PDO.
 *
 * Suporta dois modos de configuração:
 *   1. DATABASE_URL (formato Neon/Heroku/Render Postgres)
 *      ex.: postgres://user:pass@host:5432/dbname?sslmode=require
 *   2. Variáveis discretas DB_HOST, DB_PORT, DB_NAME, DB_USER, DB_PASS,
 *      DB_DRIVER (pgsql|mysql). Defaults mantêm compatibilidade com o
 *      docker-compose.yml de desenvolvimento.
 */

declare(strict_types=1);

// Composer autoload é opcional aqui. Em produção (Render/Fly) as variáveis
// vêm direto do ambiente, então mesmo sem phpdotenv o app sobe. Em dev,
// se o autoload existir, carregamos pra ler .env automaticamente.
$autoload = __DIR__ . '/../../vendor/autoload.php';
if (is_file($autoload)) {
    require_once $autoload;
}

if (!function_exists('study_root_db')) {

    // Carrega .env só uma vez, se possível
    $envPath = __DIR__ . '/../../';
    if (file_exists($envPath . '.env') && class_exists(\Dotenv\Dotenv::class)) {
        $dotenv = \Dotenv\Dotenv::createImmutable($envPath);
        $dotenv->safeLoad();
    }

    /**
     * Retorna a instância PDO compartilhada para a request atual.
     */
    function study_root_db(): PDO
    {
        static $pdo = null;
        if ($pdo instanceof PDO) {
            return $pdo;
        }

        $databaseUrl = $_ENV['DATABASE_URL'] ?? getenv('DATABASE_URL') ?: null;

        if ($databaseUrl) {
            $parts = parse_url($databaseUrl);
            if ($parts === false || !isset($parts['scheme'], $parts['host'])) {
                throw new RuntimeException('DATABASE_URL inválida.');
            }
            $scheme = $parts['scheme'];
            $driver = ($scheme === 'postgres' || $scheme === 'postgresql') ? 'pgsql' : $scheme;
            $host   = $parts['host'];
            $port   = $parts['port'] ?? ($driver === 'pgsql' ? 5432 : 3306);
            $dbname = isset($parts['path']) ? ltrim($parts['path'], '/') : '';
            $user   = $parts['user'] ?? '';
            $pass   = isset($parts['pass']) ? urldecode($parts['pass']) : '';

            $dsn = "{$driver}:host={$host};port={$port};dbname={$dbname}";

            // sslmode vem na query string em URLs do Neon
            if ($driver === 'pgsql' && !empty($parts['query'])) {
                parse_str($parts['query'], $q);
                if (!empty($q['sslmode'])) {
                    $dsn .= ";sslmode={$q['sslmode']}";
                }
            }
        } else {
            $driver = $_ENV['DB_DRIVER'] ?? getenv('DB_DRIVER') ?: 'pgsql';
            $host   = $_ENV['DB_HOST']   ?? getenv('DB_HOST')   ?: 'localhost';
            $port   = $_ENV['DB_PORT']   ?? getenv('DB_PORT')   ?: ($driver === 'pgsql' ? '5432' : '3306');
            $dbname = $_ENV['DB_NAME']   ?? getenv('DB_NAME')   ?: 'study_root';
            $user   = $_ENV['DB_USER']   ?? getenv('DB_USER')   ?: 'postgres';
            $pass   = $_ENV['DB_PASS']   ?? getenv('DB_PASS')   ?: '';

            $dsn = "{$driver}:host={$host};port={$port};dbname={$dbname}";
            if ($driver === 'mysql') {
                $dsn .= ';charset=utf8mb4';
            }
        }

        try {
            $pdo = new PDO($dsn, $user, $pass, [
                PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_OBJ,
                PDO::ATTR_EMULATE_PREPARES   => false,
            ]);
        } catch (PDOException $e) {
            // Não vaza credenciais nem o DSN no front
            error_log('DB connection failed: ' . $e->getMessage());
            http_response_code(503);
            exit('Serviço temporariamente indisponível. Tente novamente em instantes.');
        }

        return $pdo;
    }

    /**
     * Retorna o driver atual (pgsql|mysql). Útil pra ajustar SQL específico.
     */
    function study_root_db_driver(): string
    {
        return study_root_db()->getAttribute(PDO::ATTR_DRIVER_NAME);
    }
}
