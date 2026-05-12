<?php

declare(strict_types=1);

require_once __DIR__ . '/lib/db.php';
require_once __DIR__ . '/lib/auth.php';
require_once __DIR__ . '/lib/helpers.php';
require_once __DIR__ . '/bcrypt.php';

$estudante = require_auth();
require_csrf();

$senhaAntiga = (string) ($_POST['senhaAntiga'] ?? '');
$senhaNova   = (string) ($_POST['senhaNova']   ?? '');
$senhaNova2  = (string) ($_POST['senhaNova2']  ?? '');
$pagina      = $_POST['pagina'] ?? 'home.php';

if ($senhaAntiga === '' || $senhaNova === '' || $senhaNova2 === '') {
    alert_and_redirect('Preencha todos os campos.', $pagina);
}

if ($senhaNova !== $senhaNova2) {
    alert_and_redirect('As senhas novas não coincidem.', $pagina);
}

if (mb_strlen($senhaNova) < 4 || mb_strlen($senhaNova) > 72) {
    alert_and_redirect('A senha nova deve ter entre 4 e 72 caracteres.', $pagina);
}

$pdo = study_root_db();

$sel = $pdo->prepare('SELECT senha FROM estudante WHERE id_estudante = :e LIMIT 1');
$sel->execute([':e' => $estudante]);
$row = $sel->fetch();

if (!$row || !Bcrypt::check($senhaAntiga, $row->senha)) {
    alert_and_redirect('Senha atual incorreta.', $pagina);
}

$hash = Bcrypt::hash($senhaNova);
$upd = $pdo->prepare('UPDATE estudante SET senha = :s WHERE id_estudante = :e');
$upd->execute([':s' => $hash, ':e' => $estudante]);

// Regenera sessão depois de troca de credencial.
session_regenerate_id(true);

alert_and_redirect('Senha alterada com sucesso.', $pagina);
