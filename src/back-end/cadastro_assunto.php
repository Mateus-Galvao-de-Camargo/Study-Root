<?php
    session_start();

    require_once('config.php');

    if(isset($_POST['cadastrar'])){
        $titulo = $_POST['titulo'];
        $resumo = $_POST['resumo'];
        $estudante = $_SESSION['id'];

        $tituloFormatado = trim(preg_replace('/\s+/', ' ', $titulo));

        if($titulo == NULL || $titulo = "" || $tituloFormatado == NULL){
            print "<script>alert('Sem gracinhas, tente denovo, da maneira correta, o título é obrigatório e não pode ser vazio ou apenas espaços em branco.'); location.href='../telas/home.php'</script>";
        }

        $row = $conn->query("INSERT INTO assunto (id_assunto, titulo, resumo, id_estudante_fk) VALUES (NULL, '$tituloFormatado', '$resumo', '$estudante');");

        if($row){
            print "<script>location.href='../telas/home.php'</script>";
        } else{
            print 'Não foi possível cadastrar';
        }
    }
?>