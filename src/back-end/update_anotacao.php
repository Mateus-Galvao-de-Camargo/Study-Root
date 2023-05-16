<?php
    session_start();

    require_once('config.php');

    if(isset($_POST['cadastrar'])){
        $titulo = $_POST['titulo'];
        $estudante = $_SESSION['id'];
        $paginaQueEnviou = $_POST['pagina'];

        $tituloFormatado = trim(preg_replace('/\s+/', ' ', $titulo));

        $tamanhoDoTitulo = mb_strlen($tituloFormatado);

        $tituloRepetido = $conn->query("SELECT * FROM anotacao WHERE id_assunto_fk = '$estudante' AND titulo = '$tituloFormatado'");

        $linha = $tituloRepetido->fetch_object();

        $qtd = $tituloRepetido->num_rows;

        if($qtd > 0){
            printf("<script>alert('O título %s já é registrado na sua conta'); location.href='../telas/$paginaQueEnviou'</script>", $linha->titulo);
        } else if($tituloFormatado == NULL || $tituloFormatado == "" || $tamanhoDoTitulo > 24){
            print "<script>alert('Sem gracinhas. tente denovo da maneira correta, o título é obrigatório, deve conter no máximo 24 caractéres e não pode ser vazio ou apenas conter espaços em branco.'); location.href='../telas/$paginaQueEnviou'</script>";
        } else {
            $row = $conn->query("UPDATE anotacao SET titulo = '$tituloFormatado' WHERE id_assunto = $idAssunto AND id_estudante_fk = $estudante");
        }

        if($row){
            print "<script>location.href='../telas/$paginaQueEnviou'</script>";
        } else{
            print "<script>alert('Não foi possível cadastrar'); location.href='../telas/$paginaQueEnviou'</script>";
        }
    }
?>