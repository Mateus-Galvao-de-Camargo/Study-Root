<?php
declare(strict_types=1);

require_once __DIR__ . '/back-end/lib/auth.php';

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
    <link rel="stylesheet" href="/css/login.css">
    <title>Study Root - Login</title>
</head>
<body>
    <img src="/img/logo.jpeg" class="logo" alt="Study Root">

    <form action="/back-end/login.php" method="post" autocomplete="on">
        <?= csrf_field() ?>
        <input type="email"    name="email" class="login-email"    placeholder="Email"  required autocomplete="username">
        <input type="password" name="senha" class="login-password" placeholder="Senha" required autocomplete="current-password">
        <button type="submit" class="login-btn">Login</button>
    </form>

    <h3><a class="link" href="/telas/cadastro.php">Não tem uma conta? Cadastre-se</a></h3>
</body>
</html>
