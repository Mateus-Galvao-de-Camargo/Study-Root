<?php
    session_start();
    require_once('config.php');

    if(isset($_POST['atualizar'])){
        $idAssunto = $_POST['idAssunto'];
        $titulo = $_POST['tituloAtt'];
        $resumo = $_POST['resumoAtt'];
        $estudante = $_SESSION['id'];

        $tituloFormatado = trim(preg_replace('/\s+/', ' ', $titulo));

        if($titulo == NULL || $titulo = "" || $tituloFormatado == NULL){
            print "<script>alert('Sem gracinhas, tente denovo, da maneira correta, o título é obrigatório e não pode ser vazio ou apenas espaços em branco.'); location.href='../telas/home.php'</script>";
        }

        $res = $conn->query("UPDATE assunto SET titulo = '$titulo', resumo = '$resumo' WHERE id_assunto = $idAssunto AND id_estudante_fk = $estudante");

        if($res){
            print "<script>location.href='../telas/home.php'</script>";
        } else{
            print 'Não foi possível cadastrar';
        }
    }
?>