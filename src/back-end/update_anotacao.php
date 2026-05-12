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

$titulo     = normalize_spaces((string) ($_POST['tituloAnotacaoUp'] ?? ''));
$assunto    = filter_var($_POST['idDoAssuntoPraUpdateDaAnotacao'] ?? '', FILTER_VALIDATE_INT);
$idAnotacao = filter_var($_POST['idAnotacaoEdit']                ?? '', FILTER_VALIDATE_INT);
$pagina     = $_POST['paginaAnotacaoUp'] ?? 'home.php';

if ($assunto === false || $assunto === null || $idAnotacao === false || $idAnotacao === null) {
    safe_redirect('home.php');
}

if ($titulo === '' || mb_strlen($titulo) > 24) {
    alert_and_redirect('Título obrigatório (até 24 caracteres).', $pagina);
}

$pdo = study_root_db();

// Garante que a anotação pertence a um assunto do estudante logado.
$own = $pdo->prepare(
    'SELECT a.id_anotacao
       FROM anotacao a
       JOIN assunto s ON s.id_assunto = a.id_assunto_fk
      WHERE a.id_anotacao = :n AND a.id_assunto_fk = :a AND s.id_estudante_fk = :e
      LIMIT 1'
);
$own->execute([':n' => $idAnotacao, ':a' => $assunto, ':e' => $estudante]);
if (!$own->fetch()) {
    safe_redirect('home.php');
}

// Evita duplicata de título no mesmo assunto.
$dup = $pdo->prepare(
    'SELECT id_anotacao FROM anotacao
     WHERE id_assunto_fk = :a AND titulo = :t AND id_anotacao <> :n LIMIT 1'
);
$dup->execute([':a' => $assunto, ':t' => $titulo, ':n' => $idAnotacao]);
if ($dup->fetch()) {
    alert_and_redirect('Já existe outra anotação com esse título nesse assunto.', $pagina);
}

$upd = $pdo->prepare('UPDATE anotacao SET titulo = :t WHERE id_anotacao = :n');
$upd->execute([':t' => $titulo, ':n' => $idAnotacao]);

safe_redirect($pagina);
