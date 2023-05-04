<?php
    require_once('config.php');

    $id = $_POST["id"];

    $sql = "DELETE FROM assunto WHERE  id_assunto = $id";
    
    $res = $conn->query($sql);

    if($res){
        print "<script>location.href='../telas/home.php'</script>";
    } else{
        echo 'Não foi possivel excluir';
    }

?>