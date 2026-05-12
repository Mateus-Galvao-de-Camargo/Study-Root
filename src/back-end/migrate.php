<?php
/**
 * Roda o schema.sql contra o banco apontado pelas variáveis de ambiente.
 * Idempotente. Pensado para rodar no entrypoint do container.
 *
 * Uso: php /var/www/html/back-end/migrate.php
 */

declare(strict_types=1);

require_once __DIR__ . '/lib/db.php';

$schemaPath = __DIR__ . '/../db/schema.sql';
if (!is_readable($schemaPath)) {
    fwrite(STDERR, "schema.sql não encontrado em {$schemaPath}\n");
    exit(1);
}

$sql = file_get_contents($schemaPath);
if ($sql === false || trim($sql) === '') {
    fwrite(STDERR, "schema.sql vazio\n");
    exit(1);
}

try {
    $pdo = study_root_db();
    $pdo->exec($sql);
    echo "[migrate] OK\n";
} catch (PDOException $e) {
    fwrite(STDERR, "[migrate] FALHOU: " . $e->getMessage() . "\n");
    exit(1);
}
