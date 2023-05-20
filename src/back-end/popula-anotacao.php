<?php
$id = $_SESSION['id'];

$sql = "SELECT * FROM anotacao WHERE id_assunto_fk = $id";

if($result = $conn -> query($sql)){

    while($anotacao = $result -> fetch_object()){
        printf("", );
    }

    $result -> free_result();
}
?>
