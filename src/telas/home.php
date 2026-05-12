<?php
declare(strict_types=1);

require_once __DIR__ . '/../back-end/lib/db.php';
require_once __DIR__ . '/../back-end/lib/auth.php';
require_once __DIR__ . '/../back-end/lib/helpers.php';

$userId = require_auth();
$pdo    = study_root_db();

$stmt = $pdo->prepare('SELECT id_assunto, titulo, resumo FROM assunto WHERE id_estudante_fk = :e ORDER BY id_assunto DESC');
$stmt->execute([':e' => $userId]);
$assuntos = $stmt->fetchAll();

// Pre-popula campos do modal de update via querystring vinda dos botões "editar"
$editId     = filter_var($_GET['id_assunto']   ?? '', FILTER_VALIDATE_INT);
$editTitle  = (string) ($_GET['titulo-btn']   ?? '');
$editResume = (string) ($_GET['resumo-btn']   ?? '');
$delId      = filter_var($_GET['idAssuntoDel']?? '', FILTER_VALIDATE_INT);
$delTitle   = (string) ($_GET['tituloDel']    ?? '');
$showEdit   = isset($_GET['mostraAtt']);
$showDelete = isset($_GET['mostraDelete']);
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Study Root</title>
    <link rel="stylesheet" href="/css/reset.css">
    <link rel="stylesheet" href="/css/bootstrap.min.css">
    <link rel="stylesheet" href="/fontawesome-free-6.4.0-web/css/all.min.css">
    <link rel="stylesheet" href="/css/aside.css">
    <link rel="stylesheet" href="/css/home.css">
</head>
<body class="flex">

  <div id="sidebar" class="flex column">
    <div id="searchBar" class="flex center">
      <input class="buscador" onkeyup="filtrar()" type="text" id="inputDeSearch" placeholder="Assunto desejado">
    </div>

    <div id="barra-de-ferramentas" class="flex start">
      <button id="abreModalConfig" class="btn-transparente" onclick="mostra('config')" type="button">
        <i class="fa-solid fa-gear fa-lg gira" style="color: #a3a3a3;"></i>
      </button>
      <button class="btn-transparente branco btn-branco-hover" data-bs-toggle="modal" data-bs-target="#modal" type="button">
        <i class="fa-solid fa-circle-plus fa-lg"></i>
      </button>
      <button hidden id="botao-magia"      data-bs-toggle="modal" data-bs-target="#modalUpdate" type="button"></button>
      <button hidden id="botao-maravilha" data-bs-toggle="modal" data-bs-target="#modalDelete" type="button"></button>
      <div id="config">
        <i class="fa fa-user-circle user-botolas" data-bs-toggle="modal" data-bs-target="#modalUpdateSenha"></i>
        <form action="/back-end/logout.php" method="post" style="display:inline">
          <?= csrf_field() ?>
          <button class="btn-cfgvermelho" type="submit">Sair</button>
        </form>
      </div>
    </div>

    <div id="listaDeAssuntos" class="flex column">
      <?php foreach ($assuntos as $a): ?>
        <div data-titulo="<?= h(mb_strtolower($a->titulo)) ?>">
          <div class="flex">
            <form action="/telas/assunto.php" method="get">
              <input hidden name="getIdAssunto" value="<?= (int) $a->id_assunto ?>">
              <button class="bts btn-preto-background-hover" type="submit">
                <span><?= h($a->titulo) ?></span>
              </button>
            </form>
            <div class="edit column space-around" id="edit-<?= (int) $a->id_assunto ?>" style="display: none;">
              <form action="/telas/home.php" method="get">
                <input hidden name="idAssuntoDel" value="<?= (int) $a->id_assunto ?>">
                <input hidden name="tituloDel"    value="<?= h($a->titulo) ?>">
                <button type="submit" name="mostraDelete" class="btn-transparente">
                  <i class="fa-solid fa-trash-can fa-lg icones btn-vermelho"></i>
                </button>
              </form>
              <form action="/telas/home.php" method="get">
                <input hidden name="id_assunto" value="<?= (int) $a->id_assunto ?>">
                <input hidden name="titulo-btn" value="<?= h($a->titulo) ?>">
                <input hidden name="resumo-btn" value="<?= h($a->resumo) ?>">
                <button type="submit" name="mostraAtt" class="btn-transparente">
                  <i class="fa-regular fa-pen-to-square fa-lg branco btn-branco-hover icones"></i>
                </button>
              </form>
            </div>
            <button class="bts-options btn-preto-background-hover" type="button"
                    onclick="mostra('edit-<?= (int) $a->id_assunto ?>')">
              <i class="fa-solid fa-ellipsis-vertical branco"></i>
            </button>
          </div>
        </div>
      <?php endforeach; ?>
    </div>
  </div>

  <img src="/img/logo.jpeg" class="flex logo" alt="Study Root">

  <!-- Modal: criar assunto -->
  <div class="modal fade branco" id="modal">
    <div class="modal-dialog modal-dialog-centered">
      <div class="modal-content">
        <div class="modal-header">
          <h1 class="modal-title fs-5 titulo">Adicionar Assunto</h1>
          <button class="btn-close" data-bs-dismiss="modal" type="button"></button>
        </div>
        <form action="/back-end/cadastro_assunto.php" method="post">
          <?= csrf_field() ?>
          <div class="modal-body">
            <input size="26" maxlength="52"  class="nome-assunto"      required name="titulo" type="text" placeholder="Título">
            <input size="26" maxlength="300" class="descricao-assunto"          name="resumo" type="text" placeholder="Descrição">
            <input hidden type="text" name="pagina" value="home.php">
          </div>
          <div class="modal-footer">
            <button name="cadastrar" type="submit" class="botao-concluir">Concluir</button>
          </div>
        </form>
      </div>
    </div>
  </div>

  <!-- Modal: editar assunto -->
  <div class="modal fade branco" id="modalUpdate">
    <div class="modal-dialog modal-dialog-centered">
      <div class="modal-content">
        <div class="modal-header">
          <h1 class="modal-title fs-5 titulo">Alterar Assunto</h1>
          <button class="btn-close" data-bs-dismiss="modal" type="button"></button>
        </div>
        <form action="/back-end/update_assunto.php" method="post">
          <?= csrf_field() ?>
          <div class="modal-body">
            <input size="26" maxlength="52"  class="nome-assunto"      required name="tituloAtt" id="tituloAtt" type="text" placeholder="Título" value="<?= h($editTitle) ?>">
            <input size="26" maxlength="300" class="descricao-assunto"          name="resumoAtt" id="resumoAtt" type="text" placeholder="Descrição" value="<?= h($editResume) ?>">
            <input hidden name="idAssunto" id="idAssunto" type="text" value="<?= $editId !== false && $editId !== null ? (int) $editId : '' ?>">
            <input hidden type="text" name="pagina" value="home.php">
          </div>
          <div class="modal-footer">
            <button name="atualizar" type="submit" class="botao-concluir">Concluir</button>
          </div>
        </form>
      </div>
    </div>
  </div>

  <!-- Modal: deletar assunto -->
  <div class="modal fade branco" id="modalDelete">
    <div class="modal-dialog modal-dialog-centered">
      <div class="modal-content">
        <div class="modal-header">
          <h1 class="modal-title fs-5 titulo">Deletar o Assunto: <span id="mostraTituloDel"><?= h($delTitle) ?></span></h1>
          <button class="btn-close" data-bs-dismiss="modal" type="button"></button>
        </div>
        <form action="/back-end/delete_assunto.php" method="post">
          <?= csrf_field() ?>
          <div class="modal-body">
            <div class="flex column center">
              <p>Tenha certeza antes de deletar seu assunto! Todas as anotações dele também serão excluídas.</p>
              <input hidden name="idAssuntoDelelete" id="idAssuntoDelete" type="text" value="<?= $delId !== false && $delId !== null ? (int) $delId : '' ?>">
              <input hidden type="text" name="pagina" value="home.php">
              <button name="deletarAssunto" type="submit" class="vermelho btn-delete-assunto center">Apagar Assunto</button>
            </div>
          </div>
        </form>
      </div>
    </div>
  </div>

  <!-- Modal: trocar senha -->
  <div class="modal fade branco" id="modalUpdateSenha">
    <div class="modal-dialog modal-dialog-centered">
      <div class="modal-content">
        <div class="modal-header">
          <h1 class="modal-title fs-5 titulo">Alterar Senha</h1>
          <button class="btn-close" data-bs-dismiss="modal" type="button"></button>
        </div>
        <div class="modal-body">
          <form action="/back-end/troca_senha.php" method="post">
            <?= csrf_field() ?>
            <input required size="26" maxlength="72" class="nome-assunto" name="senhaAntiga" type="password" placeholder="Senha Antiga"   autocomplete="current-password">
            <input required size="26" maxlength="72" class="nome-assunto" name="senhaNova"   type="password" placeholder="Senha Nova"      autocomplete="new-password">
            <input required size="26" maxlength="72" class="nome-assunto" name="senhaNova2"  type="password" placeholder="Confirmar Senha" autocomplete="new-password">
            <input hidden type="text" name="pagina" value="home.php">
            <div class="modal-footer">
              <button type="submit" class="botao-concluir">Alterar</button>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>

  <script src="/js/bootstrap.bundle.min.js"></script>
  <script src="/js/bootstrap.min.js"></script>
  <script>
    var idDaEditAnterior = null;

    function mostra(idDaEdit) {
      var edit = document.getElementById(idDaEdit);
      if (!edit) return;
      if (idDaEditAnterior) {
        var anterior = document.getElementById(idDaEditAnterior);
        if (anterior) anterior.style.display = "none";
      }
      edit.style.display = "flex";
      idDaEditAnterior = idDaEdit;
    }

    var modalConfiguracoes = document.querySelector('#config');
    var botaoAbreConfig    = document.querySelector('#abreModalConfig');

    function fecharModal(modal) {
      var el = document.querySelector('#' + modal);
      if (el) el.style.display = "none";
    }

    window.addEventListener('click', function (event) {
      if (!modalConfiguracoes.contains(event.target)) {
        fecharModal('config');
      }
    });

    botaoAbreConfig.addEventListener('click', function (event) {
      event.stopPropagation();
      modalConfiguracoes.style.display = "flex";
    });

    function filtrar() {
      var input = document.querySelector('#inputDeSearch').value.toLowerCase();
      document.querySelectorAll('#listaDeAssuntos [data-titulo]').forEach(function (div) {
        var titulo = div.getAttribute('data-titulo') || '';
        div.style.display = titulo.indexOf(input) > -1 ? 'flex' : 'none';
      });
    }

    <?php if ($showEdit): ?>
    document.querySelector('#botao-magia').click();
    <?php endif; ?>
    <?php if ($showDelete): ?>
    document.querySelector('#botao-maravilha').click();
    <?php endif; ?>
  </script>
</body>
</html>
