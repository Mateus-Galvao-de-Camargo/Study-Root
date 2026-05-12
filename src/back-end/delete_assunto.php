<?php

declare(strict_types=1);

require_once __DIR__ . '/lib/db.php';
require_once __DIR__ . '/lib/auth.php';
require_once __DIR__ . '/lib/helpers.php';

$estudante = require_auth();
require_csrf();

$id     = filter_var($_POST['idAssuntoDelelete'] ?? '', FILTER_VALIDATE_INT);
$pagina = $_POST['pagina'] ?? 'home.php';

if ($id === false || $id === null) {
    safe_redirect($pagina);
}

$pdo = study_root_db();

// O DELETE checa o dono diretamente — não é preciso SELECT antes.
// CASCADE no schema garante que as anotações associadas vão junto.
$del = $pdo->prepare(
    'DELETE FROM assunto WHERE id_assunto = :a AND id_estudante_fk = :e'
);
$del->execute([':a' => $id, ':e' => $estudante]);

safe_redirect($pagina);
