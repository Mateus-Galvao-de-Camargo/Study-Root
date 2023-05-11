<?php
    session_start();

    require_once('config.php');

    if(isset($_POST['cadastrar'])){
        $usuario = $_POST['usuario'];
        $email = $_POST['email'];
        $senha = $_POST['senha'];

        $sql = "SELECT * FROM  estudante WHERE email = '$email'";

        $res = $conn->query($sql);

        $qtd = $res->num_rows;

        if($qtd>0){
            print "<script>alert('Email já utilizado! Cadastro não realizado'); location.href='../telas/cadastro.php'</script>";
        }
        
        $row = $conn->query("INSERT INTO estudante (usuario, email, senha) VALUE ('$usuario', '$email', '$senha')");

        if($row){
            $select = $conn->query($sql);

            $res = $select->fetch_object();

            $_SESSION["id"] = $res->id_estudante;

            print "<script>location.href='../telas/home.php'</script>";
        } else{
            echo 'Não foi possível cadastrar';
        }
    }
?>