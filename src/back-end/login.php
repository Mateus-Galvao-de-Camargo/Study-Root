<?php
    session_start();

    if(empty($_POST) or (empty($_POST['email']) or (empty($_POST['senha'])))){
        print "<script>location.href='../telas/index.php';</script>";
    }

    include('config.php');
    include('bcrypt.php');

    $email = $_POST["email"];
    $senha = $_POST["senha"];

    $sql = "SELECT id_estudante FROM estudante WHERE email = '{$email}'";

    $res = $conn->query($sql) or die($conn->error);

    $row = $res->fetch_object();

    $qtd = $res->num_rows;

    $hash = $row->senha;

    if(Bcrypt::check($senha, $hash)){
        print "<script>alert('Senha errada');</script>";
    }

    if($qtd > 0){
        $_SESSION["id"] = $row->id_estudante;

        print "<script>location.href='../telas/home.php'</script>";
    } else{
        print "<script>alert('Email e/ou senha incorreto(s)');</script>";
        print "<script>location.href='../telas/index.php';</script>";
        header('Location: ../telas/index.php');
    }