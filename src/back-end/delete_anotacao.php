<?php

declare(strict_types=1);

require_once __DIR__ . '/lib/db.php';
require_once __DIR__ . '/lib/auth.php';
require_once __DIR__ . '/lib/helpers.php';

$estudante = require_auth();
require_csrf();

$id     = filter_var($_POST['idAnotacaoDel'] ?? '', FILTER_VALIDATE_INT);
$pagina = $_POST['pagina'] ?? 'home.php';

if ($id === false || $id === null) {
    safe_redirect($pagina);
}

$pdo = study_root_db();

// Sub-select garante ownership via assunto.
$del = $pdo->prepare(
    'DELETE FROM anotacao
      WHERE id_anotacao = :n
        AND id_assunto_fk IN (
            SELECT id_assunto FROM assunto WHERE id_estudante_fk = :e
        )'
);
$del->execute([':n' => $id, ':e' => $estudante]);

safe_redirect($pagina);
