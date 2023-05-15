<?php
    session_start();
    require_once('config.php');

    if(isset($_POST['atualizar'])){
        $idAssunto = $_POST['idAssunto'];
        $titulo = $_POST['tituloAtt'];
        $resumo = $_POST['resumoAtt'];
        $estudante = $_SESSION['id'];

        $tituloFormatado = trim(preg_replace('/\s+/', ' ', $titulo));

        $tamanhoDoTitulo = mb_strlen($tituloFormatado);

        $resumoFormatado = trim(preg_replace('/\s+/', ' ', $resumo));

        $tamanhoDoResumo = mb_strlen($resumoFormatado);

        if($titulo == NULL || $titulo = "" || $tituloFormatado == NULL || $tamanhoDoTitulo > 52 || $tamanhoDoResumo > 300){
            print "<script>alert('Sem gracinhas, tente denovo, da maneira correta, o título é obrigatório, deve conter no máximo 52 caractéres e não pode ser vazio ou apenas conter espaços em branco.'); location.href='../telas/home.php'</script>";
        }

        $res = $conn->query("UPDATE assunto SET titulo = '$tituloFormatado', resumo = '$resumoFormatado' WHERE id_assunto = $idAssunto AND id_estudante_fk = $estudante");

        if($res){
            print "<script>location.href='../telas/home.php'</script>";
        } else{
            print 'Não foi possível cadastrar';
        }
    }
?>