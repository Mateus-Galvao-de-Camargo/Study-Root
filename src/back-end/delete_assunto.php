<?php
    require_once('config.php');

    $id = $_POST["id"];
    $pagina = $_POST["pagina"];

    $deleteAnotacoes = $conn->query("DELETE FROM anotacao WHERE id_assunto_fk = $id");

    $sql = "DELETE FROM assunto WHERE id_assunto = $id";
    
    $res = $conn->query($sql);

    if($res){
        print "<script>location.href='../telas/$pagina'</script>";
    } else{
        print "<script>alert('Não foi possível deletar'); location.href='../telas/$pagina'</script>";
    }
?>