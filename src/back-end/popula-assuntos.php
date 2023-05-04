<?php
    include('config.php');

    $id = $_SESSION['id'];

    $sql = "SELECT * FROM assunto WHERE id_estudante_fk = $id";

    if($result = $conn -> query($sql)){
        while($assunto = $result -> fetch_object()){
            printf("<a href=''> <button class='bts'> <span>%s</span> <i class='fa-solid fa-trash-can btn-vermelho'></i> </button> </a>", $assunto->titulo);
        }
        $result -> free_result();
    }
?>