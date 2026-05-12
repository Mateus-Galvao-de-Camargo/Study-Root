<?php

declare(strict_types=1);

require_once __DIR__ . '/lib/db.php';
require_once __DIR__ . '/lib/auth.php';
require_once __DIR__ . '/lib/helpers.php';
require_once __DIR__ . '/bcrypt.php';

study_root_session_start();
require_csrf();

$email = trim((string) ($_POST['email'] ?? ''));
$senha = (string) ($_POST['senha'] ?? '');

if ($email === '' || $senha === '') {
    alert_and_redirect('Email e/ou senha incorreto(s)', 'index.php', '/index.php');
}

$pdo = study_root_db();
$stmt = $pdo->prepare('SELECT id_estudante, senha FROM estudante WHERE email = :email LIMIT 1');
$stmt->execute([':email' => $email]);
$row = $stmt->fetch();

// Validação genérica: nunca informa se foi o email ou a senha que falhou.
if (!$row || !Bcrypt::check($senha, $row->senha)) {
    alert_and_redirect('Email e/ou senha incorreto(s)', 'index.php', '/index.php');
}

// Se o hash ficou desatualizado (custo, algoritmo), reescreve.
if (Bcrypt::needsRehash($row->senha)) {
    $novo = Bcrypt::hash($senha);
    $upd = $pdo->prepare('UPDATE estudante SET senha = :senha WHERE id_estudante = :id');
    $upd->execute([':senha' => $novo, ':id' => $row->id_estudante]);
}

log_in_as((int) $row->id_estudante);
header('Location: /telas/home.php');
exit;
