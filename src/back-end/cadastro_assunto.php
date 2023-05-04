<?php
    session_start();

    require_once('config.php');

    if(isset($_POST['cadastrar'])){
        $titulo = $_POST['titulo'];
        $resumo = $_POST['resumo'];
        $estudante = $_SESSION['estudante_id'];
        $query = mysqli_query($conn, "INSERT INTO assunto (titulo, resumo, id_estudante_fk) VALUE ('$titulo', '$resumo', '$estudante')");

        if($query){
            print "<script>alert('Cadastro realizado com sucesso');</script>";
            print "<script>location.href='../telas/home.php'</script>";
        } else{
            echo 'Não foi possível cadastrar';
        }
    }
?>