<?php
    session_start();

    require_once('config.php');

    if(isset($_POST['cadastrar'])){
        $titulo = $_POST['titulo'];
        $resumo = $_POST['resumo'];
        $estudante = $_SESSION['id'];

        $row = $conn->query("INSERT INTO assunto (id_assunto, titulo, resumo, id_estudante_fk) VALUES (NULL, '$titulo', '$resumo', '$estudante');");

        if($row){
            print "<script>location.href='../telas/home.php'</script>";
        } else{
            print 'Não foi possível cadastrar';
        }
    }
?>