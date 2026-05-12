<?php
/**
 * Mantido só por compatibilidade. Toda nova chamada deve usar diretamente:
 *
 *   require_once __DIR__ . '/lib/db.php';
 *   $pdo = study_root_db();
 *
 * Este arquivo continua expondo a variável $conn como um PDO, para que
 * arquivos legados que ainda usem `require 'config.php'; $conn->...` não
 * quebrem antes da migração completa.
 */

declare(strict_types=1);

require_once __DIR__ . '/lib/db.php';

$conn = study_root_db();
