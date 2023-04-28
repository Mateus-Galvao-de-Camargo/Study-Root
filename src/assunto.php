<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Melhor Aplicativo de Estudo</title>
    <link rel="stylesheet" href="./css/reset.css">
    <link rel="stylesheet" href="./css/bootstrap.min.css">
    <link rel="stylesheet" href="./css/assunto.css">
</head>
<body>

    <aside class="sidebar"> 
        
            <form  class ="buscas" role ="search">
                <input  class ="buscador" type ="text" placeholder ="Assunto desejado" aria-label ="Search">
                <button class ="botao-pesquisa" type ="submit" >Buscar</button>
            </form> 
           
            <nav>
                <a href="./assunto.php"> <button class="bts">
                    <span>
                        <span>nome assunto01</span>
                    </span>
                </button> </a>
                <button class="bts">
                    <span>
                        <span>nome assunto02</span>
                    </span>
                </button>
                <button class="bts">
                    <span>
                        <span>nome assunto03</span>
                    </span>
                </button>
            </nav>

            <button type="button" class="botao-cadastro-aside" data-bs-toggle="modal" data-bs-target="#exampleModal">Cadastrar</button>
        </header>

        <?php
           print "<a href='logout.php' class='btn btn-danger'>Sair</a>";
        ?>
    </aside>

    <h1 class="titulo">nome assunto01 <button class ="botao-sair" type ="submit"><a class="link-sair" href="./home.php"><p class="btn-close"></p></a></button></h1> 

    <a class="primeira-aula" href="./anotacao.php"><button class ="botao-materia" type ="submit"><p>ex: primeira aula</p></button></a>

    <button class ="botao-cadastro-assunto" type ="submit">Cadastrar</button>

    <!-- Modal -->
    <div class="modal fade modale" id="exampleModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
      <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
          <div class="modal-header">
            <h1 class="modal-title fs-5 titulo" id="staticBackdropLabel">Adicionar Matéria</h1>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
          </div>

          <form action="./cadastro_assunto.php" method="post">
          <div class="modal-body">
            <input  class ="nome-assunto" name="titulo" id="titulo" type ="text" placeholder ="Título" aria-label ="Search">

            <input  class ="descricao-assunto" name="resumo" id="resumo" type ="text" placeholder ="Descrição" aria-label ="Search">
          </div>

          <div class="modal-footer">
            <button type="submit" class="botao-concluir">Concluir</button>
          </div>
          </form>
        </div>
      </div>
    </div>
  
    <script src="./js/bootstrap.bundle.min.js"></script>
    <script src="./js/bootstrap.min.js"></script>
    
</body>
</html>