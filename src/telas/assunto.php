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
    <link rel="stylesheet" href="../css/assunto.css">
</head>
<body class="flex">
  <?php
    require_once('../back-end/config.php');
    session_start();

    // Validando se há um login, se tem um assunto sendo carregado e se esse assunto é pertencente ao usuário do login.
    if(empty($_SESSION)){
      header("Location: http://localhost:8081/study-root/src/telas/index.php");
    } else if(empty($_GET['getIdAssunto'])){
      header("Location: http://localhost:8081/study-root/src/telas/home.php");
    } else {
      $testaIdAssunto = $_GET['getIdAssunto'];
      $testaIdUsuario = $_SESSION['id'];

      $confereUsuario = $conn->query("SELECT * FROM assunto WHERE id_assunto = '$testaIdAssunto' AND id_estudante_fk = '$testaIdUsuario'");
      $usuarioSendoTestado = $confereUsuario->fetch_object();
      $qtdDeLinhas = $confereUsuario->num_rows;
      
      if($qtdDeLinhas > 0){
        //boa, sem gracinhas.
      } else {
        //GRACINHAS?
        header("Location: http://localhost:8081/study-root/src/telas/home.php");
      }
    }
  ?>

<div id="sidebar" class="flex column"> 

  <div id="searchBar" class="flex center">
    <input class="buscador" onkeyup="filtrar()" type ="text" id="inputDeSearch" placeholder ="Assunto desejado">
  </div>


  <div id="barra-de-ferramentas" class="flex start">
    <button id="abreModalConfig" class="btn-transparente" onclick="mostra('config')"><i class="fa-solid fa-gear fa-lg gira" style="color: #a3a3a3;"></i></button>
    <button class="btn-transparente branco btn-branco-hover" data-bs-toggle="modal" data-bs-target="#modal"><i class="fa-solid fa-circle-plus fa-lg"></i></button>
    <button hidden id="botao-magia" data-bs-toggle="modal" data-bs-target="#modalUpdate"></button>
    <button hidden id="botao-maravilha" data-bs-toggle="modal" data-bs-target="#modalDelete"></button>
    <div id="config">
      <i class="fa fa-user-circle user-botolas"></i>
      <a href="../back-end/logout.php"><button class="vermelho btn-cfgvermelho">Sair</button></a>
    </div>
  </div> 


  <div id="listaDeAssuntos" class="flex column">
    <?php
      $id = $_SESSION['id'];
      $idDoAssuntoDaPagina = $_GET['getIdAssunto'];

      if($result = $conn -> query("SELECT * FROM assunto WHERE id_estudante_fk = $id")){  
        while($assunto = $result -> fetch_object()){
          printf("<div value='%s'> <div class='flex'> <form action='./assunto.php' method='get'> <input hidden name='getIdAssunto' value='%d'> <button class='bts btn-preto-background-hover' type='submit'> <span>%s</span> </button> </form> <div class='edit column space-around' id='%d' style='display: none;'> <form action='./assunto.php' method='get'> <input hidden name='getIdAssunto' value='%d'> <input hidden type='text' value='%d' name='idAssuntoDel' id='idAssuntoDel'> <input hidden type='text' value='%s' name='tituloDel' id='tituloDel'> <button type='submit' name='mostraDelete' class='btn-transparente'><i class='fa-solid fa-trash-can fa-lg icones btn-vermelho'></i></button> </form> <form action='./assunto.php' method='get'> <input hidden name='getIdAssunto' value='%d'> <input hidden name='id_assunto' type='text' value='%d'><input hidden name='titulo-btn' type='text' value='%s'><input hidden name='resumo-btn' type='text' value='%s'><button type='submit' name='mostraAtt' class='btn-transparente'><i class='fa-regular fa-pen-to-square fa-lg branco btn-branco-hover icones'></i></button></form> </div> <button class='bts-options btn-preto-background-hover' onclick='mostra(%d)'><i class='fa-solid fa-ellipsis-vertical branco'></i></button> </div> </div>", $assunto->titulo, $assunto->id_assunto, $assunto->titulo,  $assunto->id_assunto, $idDoAssuntoDaPagina, $assunto->id_assunto, $assunto->titulo, $idDoAssuntoDaPagina, $assunto->id_assunto, $assunto->titulo, $assunto->resumo, $assunto->id_assunto);
        }
        $result -> free_result();
      }
    ?>
  </div>
</div>


  <!-- Conteúdo da página assunto -->
  <div class="flex column divas">

    <a href="./home.php" class="flex end"><p class="btn-close"></p></a>

    <?php
      $idDoAssuntoAtual = $_GET['getIdAssunto'];
      $assuntoAtual = $conn->query("SELECT * FROM assunto WHERE id_assunto = '$idDoAssuntoAtual'");
      $objeto = $assuntoAtual->fetch_object();
      print "<h1 class='titulo flex center' id='tituloDaAnotacao'>$objeto->titulo</h1> <p class='resumidamente flex center' id='resumoDaAnotacao'>$objeto->resumo</p>"
    ?>

    <div class="flex center">
      <button class="btn-normal" data-bs-toggle="modal" data-bs-target="#modalAnotacao">Criar Anotação</button>
    </div>

    <?php
      if(isset($_GET['getIdAssunto'])){
        $idDoAssunto = $_GET['getIdAssunto'];
      }

      $sql = "SELECT * FROM anotacao WHERE id_assunto_fk = $idDoAssunto";
  
      if($result = $conn -> query($sql)){
  
          while($anotacao = $result -> fetch_object()){
              printf("<div class='flex center btn-aulas'><form action='anotacao.php' method='get'><input hidden type='number' value='%d' name='idAnotacaoParaTexto'><button class ='botao-materia' type='submit'><p>%s</p></button></form><button class='bts-assunto-3p btn-preto-background-hover' onclick='mostraEditAnotacao(anotacao%d)'><i class='fa-solid fa-ellipsis-vertical branco'></i></button><div class='edit-anotacao' id='anotacao%d' name='editors-anotacao'><form action='../back-end/delete_anotacao.php' method='post'><input hidden type='text' value='%d' name='idAnotacaoDel'><button type='submit' class='btn-transparente'><i class='fa-solid fa-trash-can fa-lg btn-vermelho'></i></button></form><form action='./assunto.php' method='get'><input hidden name='getIdAssunto' type='text' value='%d'><input hidden name='idAnotacaoEdit' type='text' value='%d'><input hidden name='tituloEditar' type='text' value='%s'><button type='submit' name='mostraAnotacaoUp' class='btn-transparente'><i class='fa-regular fa-pen-to-square fa-lg branco btn-branco-hover'></i></button></form></div></div>", $anotacao->id_anotacao, $anotacao->titulo, $anotacao->id_anotacao, $anotacao->id_anotacao, $anotacao->id_anotacao, $anotacao->id_assunto_fk, $anotacao->id_anotacao, $anotacao->titulo);
/*<div class='flex center btn-aulas'>
<form action='anotacao.php' method='get'><input hidden type='number' value='%d' name='idAnotacaoParaTexto'><button class ='botao-materia' type='submit'><p>%s</p></button></form>
<button class='bts-assunto-3p btn-preto-background-hover' onclick='mostraEditAnotacao(anotacao%d)'><i class='fa-solid fa-ellipsis-vertical branco'></i></button>

<div class='edit-anotacao' id='anotacao%d' name='editors-anotacao'>
<form action='../back-end/delete_anotacao.php' method='post'>
<input hidden type='text' value='%d' name='idAnotacaoDel'>
<button type='submit' class='btn-transparente'><i class='fa-solid fa-trash-can fa-lg btn-vermelho'></i></button>
</form>

<form action='./assunto.php' method='get'>
<input hidden name='getIdAssunto' type='text' value='%d'>
<input hidden name='idAnotacaoEdit' type='text' value='%d'>
<input hidden name='tituloEditar' type='text' value='%s'>
<button type='submit' name='mostraAnotacaoUp' class='btn-transparente'><i class='fa-regular fa-pen-to-square fa-lg branco btn-branco-hover'></i></button>
</form></div></div>*/
          }
          $result -> free_result();
      }
    ?>

  </div>   

  <!-- Modal Insert -->
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
            <input hidden type="text" name="pagina" id="pagina" value='assunto.php?getIdAssunto=<?php if(isset($_GET['getIdAssunto'])){print $_GET['getIdAssunto'];} ?>'>
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
          <input SIZE = 26 MAXLENGTH = 20 class ="nome-assunto" required name="tituloAtt" id="tituloAtt" type ="text" placeholder ="Título" aria-label ="Search">
            <input SIZE = 26 MAXLENGTH = 300 class ="descricao-assunto" name="resumoAtt" id="resumoAtt" type ="text" placeholder ="Descrição" aria-label ="Search">
            <input hidden name='idAssunto' id='idAssunto' type ='text'>
            <input hidden type="text" name="pagina" id="pagina" value='assunto.php?getIdAssunto=<?php if(isset($_GET['getIdAssunto'])){print $_GET['getIdAssunto'];} ?>'>
          </div>

          <div class="modal-footer">
            <button name="atualizar" type="submit" class="botao-concluir">Concluir</button>
          </div>
        </form>

      </div>
    </div>
  </div>

  <!-- Modal de Delete -->
  <div class="modal fade branco" id="modalDelete">
    <div class="modal-dialog modal-dialog-centered">
      <div class="modal-content">

        <div class="modal-header">
          <h1 class="modal-title fs-5 titulo">Deletar o Assunto:<p id="mostraTituloDel"></p></h1>
          <button class="btn-close" data-bs-dismiss="modal"></button>
        </div>

        <form action="../back-end/delete_assunto.php" method="post">
          <div class="modal-body">
            <p>Tenha certeza antes de deletar seu assunto! Pois, todas as anotações dele também serão excluídas!</p>
            <input hidden name='idAssuntoDelelete' id='idAssuntoDelete' type ='text'>
            <input hidden type="text" name="pagina" id="pagina" value='assunto.php?getIdAssunto=<?php if(isset($_GET['getIdAssunto'])){print $_GET['getIdAssunto'];} ?>'>

            <button name="deletarAssunto" type="submit" class="vermelho btn-delete-assunto">Apagar Assunto</button>
          </div>
        </form>

      </div>
    </div>
  </div>

  <!-- Modal Insert de Anotação -->
  <div class="modal fade branco" id="modalAnotacao">
    <div class="modal-dialog modal-dialog-centered">
      <div class="modal-content">

        <div class="modal-header">
          <h1 class="modal-title fs-5 titulo">Criar Anotação</h1>
          <button class="btn-close" data-bs-dismiss="modal"></button>
        </div>

        <form action="../back-end/cadastro_anotacao.php" method="post">
          <div class="modal-body">
            <input class ="nome-assunto" name="tituloAnotacao" id="tituloAnotacao" type ="text" placeholder ="Título" aria-label ="Search">
            <input hidden name='idAssuntoInsertAnotacao' id='idAssuntoInsertAnotacao' type ='text' value="<?php if(isset($_GET['getIdAssunto'])){print $_GET['getIdAssunto'];} ?>">
            <input hidden type="text" name="paginaAnotacao" id="paginaAnotacao" value="assunto.php?getIdAssunto=<?php if(isset($_GET['getIdAssunto'])){print $_GET['getIdAssunto'];} ?>">
          </div>

          <div class="modal-footer">
            <button name="cadastrar" type="submit" class="botao-concluir">Concluir</button>
          </div>
        </form>

      </div>
    </div>
  </div>

  <!-- Modal Update de Anotação -->
  <div class="modal fade branco" id="modalUpAnotacao">
    <div class="modal-dialog modal-dialog-centered">
      <div class="modal-content">

        <div class="modal-header">
          <h1 class="modal-title fs-5 titulo">Editar Anotação</h1>
          <button class="btn-close" data-bs-dismiss="modal"></button>
        </div>

        <form action="../back-end/update_anotacao.php" method="post">
          <div class="modal-body">
            <input class ="nome-assunto" name="tituloAnotacaoUp" id="tituloAnotacaoUp" type ="text" placeholder ="Título" aria-label ="Search">
            <input hidden name='idAssuntoUpdateAnotacao' id='idAssuntoUpdateAnotacao' type ='text' value="">
            <input hidden type="text" name="paginaAnotacaoUp" id="paginaAnotacaoUp" value="assunto.php?getIdAssunto=<?php if(isset($_GET['getIdAssunto'])){print $_GET['getIdAssunto'];} ?>">
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
      var divs = ["" <?php $id = $_SESSION['id'];$sqlTitulos = "SELECT * FROM assunto WHERE id_estudante_fk = $id";if($result = $conn -> query($sqlTitulos)){ while($assunto = $result -> fetch_object()){ printf(", '%s'", $assunto->titulo);}$result -> free_result();} ?>];
      
      var idDaEditAnterior = 0;
      var editAnteriormenteAberta = document.getElementById(`${idDaEditAnterior}`)
      
      function mostra(idDaEdit) {
        var edit = document.getElementById(`${idDaEdit}`);
        var editAnterior = document.getElementById(`${idDaEditAnterior}`);
        if(idDaEditAnterior != 0){
          editAnterior.style.display = "none";
        }
        edit.style.display = "flex";
        idDaEditAnterior = idDaEdit;
      }

      var modalConfiguracoes = document.querySelector(`#config`);
      var botaoAbreConfig = document.querySelector(`#abreModalConfig`);

      function fecharModal(modal){

        var fechaModal = document.querySelector(`#${modal}`);
        fechaModal.style.display = "none";

      }

      window.addEventListener('click', (event)=>{

        if(!modalConfiguracoes.contains(event.target)){
          fecharModal('config');
        }

      })

      botaoAbreConfig.addEventListener('click', (event)=>{
        
        event.stopPropagation();
        modalConfiguracoes.display.style = "flex"
        
      })

      function filtrar(){
        var inputDaSearch = document.querySelector("#inputDeSearch")
        var input = inputDaSearch.value.toLowerCase()
        
        for(i=1; i < divs.length; i++){
          valorId = divs[i]
          var string = `div[value='${valorId}']`
          var div = document.querySelector(string)
          if(valorId.toLowerCase().indexOf(input) > -1){
            div.style.display = "flex"
          } else {
            div.style.display = "none"
          }
        }
      }
      
      // Mostra e atualiza o modal de update
      var idAssunto = document.querySelector('#idAssunto');
      var titulo = document.querySelector('#tituloAtt');
      var resumo = document.querySelector('#resumoAtt');
      var botao = document.querySelector('#botao-magia')
      idAssunto.value = '<?php if(isset($_GET['mostraAtt'])){print $_GET['id_assunto'];} ?>'
      titulo.value = '<?php if(isset($_GET['mostraAtt'])){print $_GET['titulo-btn'];} ?>'
      resumo.value = '<?php if(isset($_GET['mostraAtt'])){print $_GET['resumo-btn'];} ?>'

      var navBar = document.querySelector('nav')

      // Mostra e atualiza o modal de delete
      var idAssuntoDel = document.querySelector('#idAssuntoDelete');
      var tituloDel = document.querySelector('#mostraTituloDel');
      var botaoMaravilha = document.querySelector('#botao-maravilha');

      tituloDel.innerHTML = "<?php if(isset($_GET['mostraDelete'])){print $_GET['tituloDel'];} ?>"
      idAssuntoDel.value = "<?php if(isset($_GET['mostraDelete'])){print $_GET['idAssuntoDel'];} ?>"

      <?php
      if(isset($_GET['mostraAtt'])){
        print 'botao.click();';
      }
      if(isset($_GET['mostraDelete'])){
        print 'botaoMaravilha.click();';
      }
      ?>
  </script>
</body>
</html>