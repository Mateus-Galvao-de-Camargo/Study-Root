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
      <form action="">
        <input class="buscador" type ="text" placeholder ="Assunto desejado" aria-label ="Search">
        <button class="btn-buscador btn-branco-background-hover" type="submit"><i class="fa-solid fa-search"></i></button>
      </form>
    </div>

    <div class="barra-de-ferramentas">
      <button class="btn-transparente"><i class="fa-solid fa-gear fa-lg gira" style="color: #a3a3a3;"></i></button>
      <button class="btn-transparente branco btn-branco-hover" data-bs-toggle="modal" data-bs-target="#modal"><i class="fa-solid fa-circle-plus fa-lg"></i></button>
      <button hidden id="botao-magia" data-bs-toggle="modal" data-bs-target="#modalUpdate"></button>
    </div>
   
    <nav>
      <div class="absolute">
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
        include('../back-end/popula-assuntos.php');
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
            <input  class ="nome-assunto" name="titulo" id="titulo" type ="text" placeholder ="Título" aria-label ="Search">
            <input  class ="descricao-assunto" name="resumo" id="resumo" type ="text" placeholder ="Descrição" aria-label ="Search">
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
            <input class ="nome-assunto" name="tituloAtt" id="tituloAtt" type ="text" placeholder ="Título" aria-label ="Search">
            <input class ="descricao-assunto" name="resumoAtt" id="resumoAtt" type ="text" placeholder ="Descrição" aria-label ="Search">
            <input hidden name='idAssunto' id='idAssunto' type ='text'>
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

      if(isset($_GET['search'])){

      }
      ?>

      



     Array.from(navBar.children).forEach(filtrar(navBar.children.id.value.toLowerCase()))

     function filtrar(tituloAssunto){
      txtValue = tituloAssunto.textContent || tituloAssunto.innerText;
        if (txtValue.toUpperCase().indexOf(filter) > -1) {
            li[i].style.display = "";
        } else {
            li[i].style.display = "none";
        }
        $_GET['search_valor']
     }
  </script>
    
</body>
</html>