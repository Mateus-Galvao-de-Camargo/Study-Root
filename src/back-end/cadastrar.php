<?php
    session_start();

    require_once('config.php');

    if(isset($_POST['cadastrar'])){
        $usuario = $_POST['usuario'];
        $email = $_POST['email'];
        $senha = $_POST['senha'];
        $query = mysqli_query($conn, "INSERT INTO estudante (usuario, email, senha) VALUE ('$usuario', '$email', '$senha')");

        $sql = "SELECT email FROM  estudante WHERE email = '{$email}'";

        $res = $conn->query($sql);

        $qtd = $res->num_rows;

        if($qtd>0){
            print "<script>alert('Email já utilizado! Cadastro não realizado');</script>";
        } else if($query){
            print "<script>alert('Cadastro realizado com sucesso');</script>";
            require_once('./login.php');
        } else{
            echo 'Não foi possível cadastrar';
        }
    }
?>