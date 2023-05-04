<?php
    session_start();

    require_once('config.php');

    if(isset($_POST['cadastrar'])){
        $titulo = $_POST['titulo'];
        $resumo = $_POST['resumo'];
        $estudante = $_SESSION['id'];
        $query = $conn->query("INSERT INTO assunto (id_assunto, titulo, resumo, id_estudante_fk) VALUE ('NULL', '$titulo', '$resumo', '$estudante')");

        if($query){
            print "<script>alert('Cadastro realizado com sucesso');</script>";
            print "<script>location.href='../telas/home.php'</script>";
        } else{
            echo 'Não foi possível cadastrar';
        }
    }
?>