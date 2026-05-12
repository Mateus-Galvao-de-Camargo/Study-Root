<?php

declare(strict_types=1);

require_once __DIR__ . '/lib/db.php';
require_once __DIR__ . '/lib/auth.php';
require_once __DIR__ . '/lib/helpers.php';
require_once __DIR__ . '/bcrypt.php';

study_root_session_start();
require_csrf();

if (!isset($_POST['cadastrar'])) {
    header('Location: /telas/cadastro.php');
    exit;
}

$usuario = trim((string) ($_POST['usuario'] ?? ''));
$email   = trim((string) ($_POST['email']   ?? ''));
$senha   = (string)        ($_POST['senha']   ?? '');

// trim já removeu bordas; remove espaços internos só do email
$email = preg_replace('/\s+/', '', $email) ?? '';

if ($usuario === '' || $email === '' || $senha === '') {
    alert_and_redirect('Preencha todos os campos.', 'cadastro.php', '/telas/cadastro.php');
}

if (mb_strlen($usuario) > 30 || mb_strlen($email) > 50 || mb_strlen($senha) > 72) {
    alert_and_redirect('Algum campo excedeu o tamanho permitido.', 'cadastro.php', '/telas/cadastro.php');
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    alert_and_redirect('Email inválido.', 'cadastro.php', '/telas/cadastro.php');
}

$pdo = study_root_db();

$check = $pdo->prepare('SELECT id_estudante FROM estudante WHERE email = :email LIMIT 1');
$check->execute([':email' => $email]);
if ($check->fetch()) {
    alert_and_redirect('Email já utilizado.', 'cadastro.php', '/telas/cadastro.php');
}

$hash = Bcrypt::hash($senha);

$ins = $pdo->prepare(
    'INSERT INTO estudante (usuario, email, senha) VALUES (:u, :e, :s)'
);
$ins->execute([':u' => $usuario, ':e' => $email, ':s' => $hash]);

// Pega o id de forma portável (lastInsertId precisa do nome da sequence em pgsql)
if (study_root_db_driver() === 'pgsql') {
    $newId = (int) $pdo->lastInsertId('estudante_id_estudante_seq');
} else {
    $newId = (int) $pdo->lastInsertId();
}

if ($newId <= 0) {
    alert_and_redirect('Não foi possível cadastrar.', 'cadastro.php', '/telas/cadastro.php');
}

log_in_as($newId);
header('Location: /telas/home.php');
exit;
