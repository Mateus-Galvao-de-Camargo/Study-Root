<?php
    require_once('config.php');
    
    $id = $_SESSION['id'];

    $sql = "SELECT * FROM assunto WHERE id_estudante_fk = $id";

    if($result = $conn -> query($sql)){
        while($assunto = $result -> fetch_object()){
            printf("<div class='absolute'> <a href='./assunto.php' class='link-assunto'> <button class='bts btn-preto-background-hover'> <span>%s</span> </button> </a> <button class='bts-options btn-preto-background-hover' onclick='mostra(%d)'><i class='fa-solid fa-ellipsis-vertical branco'></i></button> <div class='edit' id='%d' name='editors'> <form action='../back-end/delete_assunto.php' method='post'> <input hidden type='text' value='%d' name='id'> <button type='submit' class='btn-transparente'><i class='fa-solid fa-trash-can fa-lg btn-vermelho'></i></button> </form> <button class='btn-transparente'><i class='fa-regular fa-pen-to-square fa-lg branco btn-branco-hover'></i></button> </div> </div>", $assunto->titulo, $assunto->id_assunto, $assunto->id_assunto, $assunto->id_assunto);
        }
        $result -> free_result();
    }
?>