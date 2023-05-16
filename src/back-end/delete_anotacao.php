<?php
    require_once('config.php');

    $id = $_POST["id"];

    $sql = "DELETE FROM anotacao WHERE id_anotacao = $id";
    
    $res = $conn->query($sql);

    if($res){
        print "<script>location.href='../telas/assunto.php'</script>";
    } else{
        print "<script>alert('Não foi possível deletar'); location.href='../telas/assunto.php'</script>";
    }
?>