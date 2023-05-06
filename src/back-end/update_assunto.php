<?php
    session_start();
    require_once('config.php');

    if(isset($_POST['atualizar'])){
        $idAssunto = $_POST['idAssunto'];
        $titulo = $_POST['tituloAtt'];
        $resumo = $_POST['resumoAtt'];
        $estudante = $_SESSION['id'];

        $res = $conn->query("UPDATE assunto SET titulo = '$titulo', resumo = '$resumo' WHERE id_assunto = $idAssunto AND id_estudante_fk = $estudante");

        if($res){
            print "<script>location.href='../telas/home.php'</script>";
        } else{
            print 'Não foi possível cadastrar';
        }
    }
?>