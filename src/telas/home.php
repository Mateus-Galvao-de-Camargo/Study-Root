<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Melhor Aplicativo de Estudo</title>
    <link rel="stylesheet" href="../css/reset.css">
    <link rel="stylesheet" href="../css/bootstrap.min.css">
    <link rel="stylesheet" href="../fontawesome-free-6.4.0-web/css/all.min.css">
    <link rel="stylesheet" href="../css/aside.css">
    <link rel="stylesheet" href="../css/home.css">
</head>
<body>

  <?php
  require_once('../back-end/config.php');
  session_start();
  if(empty($_SESSION)){
    print "<script>location.href='index.php'</script>";
  }
  ?>

  <aside class="sidebar"> 

    <div class="buscas">
        <input class="buscador" onkeyup="filtrar()" type ="text" id="inputDeSearch" placeholder ="Assunto desejado">
    </div>

    <div class="barra-de-ferramentas">
      <button class="btn-transparente"><i class="fa-solid fa-gear fa-lg gira" style="color: #a3a3a3;"></i></button>
      <button class="btn-transparente branco btn-branco-hover" data-bs-toggle="modal" data-bs-target="#modal"><i class="fa-solid fa-circle-plus fa-lg"></i></button>
      <button hidden id="botao-magia" data-bs-toggle="modal" data-bs-target="#modalUpdate"></button>
    </div>
   
    <nav>
      <div value="abc" class="absolute">
        <a href="./assunto.php" class="link-assunto">
          <button class="bts btn-preto-background-hover">
            <span>nome567</span>
          </button>
        </a>
        <button class="bts-options btn-preto-background-hover" onclick="mostra(#)"><i class="fa-solid fa-ellipsis-vertical branco"></i></button>

        <div class="edit" id="#" name="editors">
          <form action="../back-end/delete_assunto.php" method="post">
            <input hidden type="text" value="#" name="id">
            <button type="submit" class="btn-transparente"><i class="fa-solid fa-trash-can fa-lg btn-vermelho"></i></button>
          </form>

          <!-- Action muda de acordo com a página  -->
          <form action="./home.php" method="get">
            <input hidden name="id_assunto" type="text" value="">
            <input hidden name="titulo-btn" type="text" value="">
            <input hidden name="resumo-btn" type="text" value="">
            <button type="submit" name="mostraAtt" class="btn-transparente"><i class="fa-regular fa-pen-to-square fa-lg branco btn-branco-hover"></i></button>
          </form>
        </div>
      </div>

      <?php

      ?>

<?php
    $id = $_SESSION['id'];

    $sql = "SELECT * FROM assunto WHERE id_estudante_fk = $id";

    if($result = $conn -> query($sql)){

        while($assunto = $result -> fetch_object()){
            printf("<div class='absolute' value='%s'> <a href='./assunto.php' class='link-assunto'> <button class='bts btn-preto-background-hover'> <span>%s</span> </button> </a> <button class='bts-options btn-preto-background-hover' onclick='mostra(%d)'><i class='fa-solid fa-ellipsis-vertical branco'></i></button> <div class='edit' id='%d' name=''> <form action='../back-end/delete_assunto.php' method='post'> <input hidden type='text' value='%d' name='id'> <button type='submit' class='btn-transparente'><i class='fa-solid fa-trash-can fa-lg btn-vermelho'></i></button> </form> <form action='./home.php' method='get'><input hidden name='id_assunto' type='text' value='%d'><input hidden name='titulo-btn' type='text' value='%s'><input hidden name='resumo-btn' type='text' value='%s'><button type='submit' name='mostraAtt' class='btn-transparente'><i class='fa-regular fa-pen-to-square fa-lg branco btn-branco-hover'></i></button></form> </div> </div>", $assunto->titulo, $assunto->titulo, $assunto->id_assunto, $assunto->id_assunto, $assunto->id_assunto, $assunto->id_assunto, $assunto->titulo, $assunto->resumo);
        }

        $result -> free_result();
    }
?>
    </nav>
  </aside>

  <img src="../img/logo.jpeg" class="logo">

  <!-- Modal -->
  <div class="modal fade branco" id="modal">
    <div class="modal-dialog modal-dialog-centered">
      <div class="modal-content">

        <div class="modal-header">
          <h1 class="modal-title fs-5 titulo">Adicionar Assunto</h1>
          <button class="btn-close" data-bs-dismiss="modal"></button>
        </div>

        <form action="../back-end/cadastro_assunto.php" method="post">
          <div class="modal-body">
            <input SIZE = 26 MAXLENGTH = 52 class ="nome-assunto" required name="titulo" id="titulo" type ="text" placeholder ="Título" aria-label ="Search">
            <input SIZE = 26 MAXLENGTH = 300 class ="descricao-assunto" name="resumo" id="resumo" type ="text" placeholder ="Descrição" aria-label ="Search">
            <input hidden type="text" name="pagina" id="pagina" value="home.php">
          </div>

          <div class="modal-footer">
            <button name="cadastrar" type="submit" class="botao-concluir">Concluir</button>
          </div>
        </form>

      </div>
    </div>
  </div>

  <!-- Modal de Update -->
  <div class="modal fade branco" id="modalUpdate">
    <div class="modal-dialog modal-dialog-centered">
      <div class="modal-content">

        <div class="modal-header">
          <h1 class="modal-title fs-5 titulo">Alterar Assunto</h1>
          <button class="btn-close" data-bs-dismiss="modal"></button>
        </div>

        <form action="../back-end/update_assunto.php" method="post">
          <div class="modal-body">
            <input SIZE = 26 MAXLENGTH = 52 class ="nome-assunto" required name="tituloAtt" id="tituloAtt" type ="text" placeholder ="Título" aria-label ="Search">
            <input SIZE = 26 MAXLENGTH = 300 class ="descricao-assunto" name="resumoAtt" id="resumoAtt" type ="text" placeholder ="Descrição" aria-label ="Search">
            <input hidden name='idAssunto' id='idAssunto' type ='text'>
            <input hidden type="text" name="pagina" id="pagina" value="home.php">
          </div>

          <div class="modal-footer">
            <button name="atualizar" type="submit" class="botao-concluir">Concluir</button>
          </div>
        </form>

      </div>
    </div>
  </div>

  <script src="../js/bootstrap.bundle.min.js"></script>
  <script src="../js/bootstrap.min.js"></script>
  <script>
    function mostra(id) {
      var edit = document.getElementById(`${id}`);
      if(edit.style.display == "none"){
        edit.style.display = "block";
      } else {
        edit.style.display = "none"
      }
    }
      
      var divs = ["" <?php $id = $_SESSION['id'];$sqlTitulos = "SELECT * FROM assunto WHERE id_estudante_fk = $id";if($result = $conn -> query($sqlTitulos)){ while($assunto = $result -> fetch_object()){ printf(", '%s'", $assunto->titulo);}$result -> free_result();} ?>];
      
      function filtrar(){
        var inputDaSearch = document.querySelector("#inputDeSearch")
        var input = inputDaSearch.value.toLowerCase()
        
        for(i=1; i < divs.length; i++){
          valorId = divs[i]
          var string = `div[value='${valorId}']`
          var div = document.querySelector(string)
          if(valorId.toLowerCase().indexOf(input) > -1){
            div.style.display = "block"
          } else {
            div.style.display = "none"
          }
        }
      }
      

      var idAssunto = document.querySelector('#idAssunto');
      var titulo = document.querySelector('#tituloAtt');
      var resumo = document.querySelector('#resumoAtt');
      var botao = document.querySelector('#botao-magia')
      idAssunto.value = '<?php if(isset($_GET['mostraAtt'])){print $_GET['id_assunto'];} ?>'
      titulo.value = '<?php if(isset($_GET['mostraAtt'])){print $_GET['titulo-btn'];} ?>'
      resumo.value = '<?php if(isset($_GET['mostraAtt'])){print $_GET['resumo-btn'];} ?>'

      var navBar = document.querySelector('nav')

      <?php
      if(isset($_GET['mostraAtt'])){
        print 'botao.click()';
      } 
      ?>
  </script>
    
</body>
</html>