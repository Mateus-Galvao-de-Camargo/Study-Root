<?php
declare(strict_types=1);

require_once __DIR__ . '/../back-end/lib/auth.php';

study_root_session_start();

if (!empty($_SESSION['user_id'])) {
    header('Location: /telas/home.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="/css/reset.css">
    <link rel="stylesheet" href="/css/cadastro.css">
    <title>Study Root - Cadastro</title>
</head>
<body>
    <img src="/img/logo.jpeg" class="logo" alt="Study Root">

    <form action="/back-end/cadastrar.php" method="post" autocomplete="on">
        <?= csrf_field() ?>
        <input type="text"     name="usuario" class="login-usuario"  placeholder="Usuário" required maxlength="30" autocomplete="username">
        <input type="email"    name="email"   class="login-email"    placeholder="Email"   required maxlength="50" autocomplete="email">
        <input type="password" name="senha"   class="login-password" placeholder="Senha"   required minlength="4" maxlength="72" autocomplete="new-password">
        <button name="cadastrar" class="login-btn" type="submit">Cadastrar</button>
    </form>

    <h3><a class="link" href="/index.php">Já possui uma conta?</a></h3>
</body>
</html>
