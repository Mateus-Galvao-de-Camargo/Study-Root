<?php
    session_start();

    require_once('config.php');

    if(isset($_POST['cadastrar'])){
        $titulo = $_POST['titulo'];
        $resumo = $_POST['resumo'];
        $query = mysqli_query($conn, "INSERT INTO estudante (titulo, resumo) VALUE ('$titulo', '$resumo')");

        if($query){
            print "<script>alert('Cadastro realizado com sucesso');</script>";
            print "<script>location.href='home.php'</script>";
        } else{
            echo 'Não foi possível cadastrar';
        }
    }
?>