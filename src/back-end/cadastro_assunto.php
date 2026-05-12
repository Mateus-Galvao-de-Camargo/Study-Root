<?php

declare(strict_types=1);

require_once __DIR__ . '/lib/db.php';
require_once __DIR__ . '/lib/auth.php';
require_once __DIR__ . '/lib/helpers.php';

$estudante = require_auth();
require_csrf();

if (!isset($_POST['cadastrar'])) {
    safe_redirect('home.php');
}

$titulo  = normalize_spaces((string) ($_POST['titulo'] ?? ''));
$resumo  = normalize_spaces((string) ($_POST['resumo'] ?? ''));
$pagina  = $_POST['pagina'] ?? 'home.php';

if ($titulo === '' || mb_strlen($titulo) > 52 || mb_strlen($resumo) > 300) {
    alert_and_redirect(
        'Título obrigatório (até 52 caracteres). Resumo até 300 caracteres.',
        $pagina
    );
}

$pdo = study_root_db();

$dup = $pdo->prepare(
    'SELECT id_assunto FROM assunto WHERE id_estudante_fk = :e AND titulo = :t LIMIT 1'
);
$dup->execute([':e' => $estudante, ':t' => $titulo]);
if ($dup->fetch()) {
    alert_and_redirect('Você já tem um assunto com esse título.', $pagina);
}

$ins = $pdo->prepare(
    'INSERT INTO assunto (titulo, resumo, id_estudante_fk) VALUES (:t, :r, :e)'
);
$ins->execute([':t' => $titulo, ':r' => $resumo, ':e' => $estudante]);

safe_redirect($pagina);
