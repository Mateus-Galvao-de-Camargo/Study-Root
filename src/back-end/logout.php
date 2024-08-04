<?php
    session_start();
    unset($_SESSION["id"]);
    session_destroy();
    print "<script>location.href='../index.php'</script>";
    exit;