<?php
    session_start();

    require_once('config.php');

    if(isset($_POST['cadastrar'])){
        $usuario = $_POST['usuario'];
        $email = $_POST['email'];
        $senha = $_POST['senha'];
        $query = mysqli_query($conn, "INSERT INTO estudante (usuario, email, senha) VALUE ('$usuario', '$email', '$senha')");

        if($query){
            print "<script>alert('Cadastro realizado com sucesso');</script>";
            print "<script>location.href='home.php'</script>";
        } else{
            echo 'Não foi possível cadastrar';
        }
    }
?>