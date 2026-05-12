<?php

declare(strict_types=1);

require_once __DIR__ . '/lib/db.php';
require_once __DIR__ . '/lib/auth.php';
require_once __DIR__ . '/lib/helpers.php';

$estudante = require_auth();
require_csrf();

if (!isset($_POST['atualizar'])) {
    safe_redirect('home.php');
}

$idAssunto = filter_var($_POST['idAssunto'] ?? '', FILTER_VALIDATE_INT);
$titulo    = normalize_spaces((string) ($_POST['tituloAtt'] ?? ''));
$resumo    = normalize_spaces((string) ($_POST['resumoAtt'] ?? ''));
$pagina    = $_POST['pagina'] ?? 'home.php';

if ($idAssunto === false || $idAssunto === null) {
    safe_redirect('home.php');
}

if ($titulo === '' || mb_strlen($titulo) > 52 || mb_strlen($resumo) > 300) {
    alert_and_redirect(
        'Título obrigatório (até 52 caracteres). Resumo até 300 caracteres.',
        $pagina
    );
}

$pdo = study_root_db();

// Garante que o assunto pertence ao estudante.
$own = $pdo->prepare(
    'SELECT id_assunto FROM assunto WHERE id_assunto = :a AND id_estudante_fk = :e LIMIT 1'
);
$own->execute([':a' => $idAssunto, ':e' => $estudante]);
if (!$own->fetch()) {
    safe_redirect('home.php');
}

// Evita colisão de título dentro do mesmo dono (excluindo o próprio registro).
$dup = $pdo->prepare(
    'SELECT id_assunto FROM assunto
     WHERE id_estudante_fk = :e AND titulo = :t AND id_assunto <> :a LIMIT 1'
);
$dup->execute([':e' => $estudante, ':t' => $titulo, ':a' => $idAssunto]);
if ($dup->fetch()) {
    alert_and_redirect('Você já tem outro assunto com esse título.', $pagina);
}

$upd = $pdo->prepare(
    'UPDATE assunto SET titulo = :t, resumo = :r WHERE id_assunto = :a AND id_estudante_fk = :e'
);
$upd->execute([':t' => $titulo, ':r' => $resumo, ':a' => $idAssunto, ':e' => $estudante]);

safe_redirect($pagina);
