<?php

$id = $_SESSION['id'];

$sqlTitulos = "SELECT * FROM assunto WHERE id_estudante_fk = $id";

if($result = $conn -> query($sqlTitulos)){
    while($assunto = $result -> fetch_object()){ printf(", '%s'", $assunto->titulo);}

$result -> free_result();

}

?>