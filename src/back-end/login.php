<?php
    session_start();

    include('config.php');
    include('bcrypt.php');

    $email = $_POST["email"];
    $senha = $_POST["senha"];

    if( empty($_POST) || (empty($_POST['email'])) || (empty($_POST['senha'])) || $email == "" || $senha == "" ){
        print "<script>alert('Email e/ou senha incorreto(s)'); location.href='../index.php';</script>";
    }

    $sql = "SELECT * FROM estudante WHERE email = ?";
    
    if ($conn instanceof PDO) {
        // PostgreSQL
        $stmt = $conn->prepare($sql);
        $stmt->execute([$email]);
        $row = $stmt->fetch(PDO::FETCH_OBJ);
        $qtd = $stmt->rowCount();
    } else {
        // MySQL
        $res = $conn->query($sql) or die($conn->error);
        $row = $res->fetch_object();
        $qtd = $res->num_rows;
    }

    if($qtd > 0){
        $hash = $row->senha;
        if(Bcrypt::check($senha, $hash)){
            $_SESSION["id"] = $row->id_estudante;
            print "<script>location.href='../telas/home.php'</script>";
        } else {
            print "<script>alert('Email e/ou senha incorreto(s)'); location.href='../index.php';</script>";
        }
    } else {
        print "<script>alert('Email e/ou senha incorreto(s)'); location.href='../index.php';</script>";
    }