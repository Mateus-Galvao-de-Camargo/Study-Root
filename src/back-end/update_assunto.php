<?php
    session_start();
    require_once('config.php');

    if(isset($_POST['atualizar'])){
        $idAssunto = $_POST['idAssunto'];
        $titulo = $_POST['tituloAtt'];
        $resumo = $_POST['resumoAtt'];
        $estudante = $_SESSION['id'];
        $pagina = $_POST['paginaUp'];

        $tituloFormatado = trim(preg_replace('/\s+/', ' ', $titulo));

        $tamanhoDoTitulo = mb_strlen($tituloFormatado);

        $resumoFormatado = trim(preg_replace('/\s+/', ' ', $resumo));

        $tamanhoDoResumo = mb_strlen($resumoFormatado);

        $tituloRepetido = $conn->query("SELECT * FROM assunto WHERE id_estudante_fk = '$estudante' AND titulo = '$tituloFormatado'");

        $linha = $tituloRepetido->fetch_object();

        $qtd = $tituloRepetido->num_rows;

        if($qtd > 0){
            printf("<script>alert('O título %s já é registrado na sua conta'); location.href='../telas/$pagina'</script>", $linha->titulo);
        } else if($titulo == NULL || $titulo = "" || $tituloFormatado == NULL || $tamanhoDoTitulo > 20 || $tamanhoDoResumo > 300){
            print "<script>alert('Sem gracinhas, tente denovo, da maneira correta, o título é obrigatório, deve conter no máximo 20 caractéres e não pode ser vazio ou apenas conter espaços em branco. Assim como o resumo deve conter no máximo 300 caractéres.'); location.href='../telas/$pagina'</script>";
        } else {
            $res = $conn->query("UPDATE assunto SET titulo = '$tituloFormatado', resumo = '$resumoFormatado' WHERE id_assunto = $idAssunto AND id_estudante_fk = $estudante");
        }

        if($res){
            print "<script>location.href='../telas/$pagina'</script>";
        } else{
            print "<script>alert('Não foi possível cadastrar'); location.href='../telas/$pagina'</script>";
        }
    }
?>