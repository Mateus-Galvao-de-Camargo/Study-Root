<?php

    session_start();
    include "./config.php";
    include "./bcrypt.php";

    $senhaVeia = $_POST['senhaAntiga'];
    $senhaNova = $_POST['senhaNova'];
    $senhaNova2 = $_POST['senhaNova2'];
    $idUsuario = $_SESSION['id'];

    $pagina = $_POST['pagina'];

    if($senhaNova === $senhaNova2){

        $procuraUsuario = $conn->query("SELECT * FROM estudante WHERE id_estudante = $idUsuario");
        $confere = $procuraUsuario->fetch_object();
        $senhaAntigaNoBanco = $confere->senha;

        if(Bcrypt::check($senhaVeia, $senhaAntigaNoBanco)){

            $hash = Bcrypt::hash($senhaNova);
            $altera = $conn->query("UPDATE estudante SET senha = '$hash' WHERE id_estudante = '$idUsuario'");
            print "<script>alert('Senha alterada'); location.href='../telas/$pagina'</script>";

        } else {
            print "<script>alert('Senha errada 👺'); location.href='../telas/$pagina'</script>";
        }

    } else {
        print "<script>alert('Senhas não coincidem'); location.href='../telas/$pagina'</script>";
    }

?>