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

$titulo  = normalize_spaces((string) ($_POST['tituloAnotacao'] ?? ''));
$assunto = filter_var($_POST['idAssuntoInsertAnotacao'] ?? '', FILTER_VALIDATE_INT);
$pagina  = $_POST['paginaAnotacao'] ?? 'home.php';

if ($assunto === false || $assunto === null) {
    alert_and_redirect('Assunto inválido.', $pagina);
}

if ($titulo === '' || mb_strlen($titulo) > 24) {
    alert_and_redirect('Título obrigatório (até 24 caracteres).', $pagina);
}

$pdo = study_root_db();

// 1. Confirma que o assunto pertence ao estudante logado.
$own = $pdo->prepare(
    'SELECT id_assunto FROM assunto WHERE id_assunto = :a AND id_estudante_fk = :e LIMIT 1'
);
$own->execute([':a' => $assunto, ':e' => $estudante]);
if (!$own->fetch()) {
    safe_redirect('home.php');
}

// 2. Confirma que não há duplicata de título dentro do mesmo assunto.
$dup = $pdo->prepare(
    'SELECT id_anotacao FROM anotacao WHERE id_assunto_fk = :a AND titulo = :t LIMIT 1'
);
$dup->execute([':a' => $assunto, ':t' => $titulo]);
if ($dup->fetch()) {
    alert_and_redirect('Já existe uma anotação com esse título.', $pagina);
}

$ins = $pdo->prepare(
    'INSERT INTO anotacao (titulo, id_assunto_fk) VALUES (:t, :a)'
);
$ins->execute([':t' => $titulo, ':a' => $assunto]);

safe_redirect($pagina);
