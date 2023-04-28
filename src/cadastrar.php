<?php
    session_start();

    require_once('config.php');

    if(isset($_POST['cadastrar'])){
        $email = $_POST['email'];
        $senha = $_POST['senha'];
        $query = mysqli_query($conn, "INSERT INTO estudante (email, senha) VALUE ('$email', '$senha')");

        if($query){
            print "<script>alert('Cadastro realizado com sucesso');</script>";
            print "<script>location.href='home.php'</script>";
        } else{
            echo 'Não foi possível cadastrar';
        }
    }
?>