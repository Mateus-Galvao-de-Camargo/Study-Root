<?php
declare(strict_types=1);

require_once __DIR__ . '/../back-end/lib/db.php';
require_once __DIR__ . '/../back-end/lib/auth.php';
require_once __DIR__ . '/../back-end/lib/helpers.php';

$userId = require_auth();
$pdo    = study_root_db();

$idAssunto  = filter_var($_GET['getIdAssunto']        ?? '', FILTER_VALIDATE_INT);
$idAnotacao = filter_var($_GET['idAnotacaoParaTexto'] ?? '', FILTER_VALIDATE_INT);

if (!$idAssunto || !$idAnotacao) {
    header('Location: /telas/home.php');
    exit;
}

$q = $pdo->prepare(
    'SELECT a.id_anotacao, a.titulo, a.conteudo, s.titulo AS assunto_titulo
       FROM anotacao a
       JOIN assunto s ON s.id_assunto = a.id_assunto_fk
      WHERE a.id_anotacao = :n AND a.id_assunto_fk = :a AND s.id_estudante_fk = :e
      LIMIT 1'
);
$q->execute([':n' => $idAnotacao, ':a' => $idAssunto, ':e' => $userId]);
$anotacao = $q->fetch();

if (!$anotacao) {
    header('Location: /telas/home.php');
    exit;
}

$sa = $pdo->prepare('SELECT id_assunto, titulo, resumo FROM assunto WHERE id_estudante_fk = :e ORDER BY id_assunto DESC');
$sa->execute([':e' => $userId]);
$assuntos = $sa->fetchAll();

$paginaContexto = 'anotacao.php?getIdAssunto=' . $idAssunto . '&idAnotacaoParaTexto=' . $idAnotacao;
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Study Root - <?= h($anotacao->titulo) ?></title>
    <link rel="stylesheet" href="/css/reset.css">
    <link rel="stylesheet" href="/css/bootstrap.min.css">
    <link rel="stylesheet" href="/fontawesome-free-6.4.0-web/css/all.min.css">
    <link rel="stylesheet" href="/css/aside.css">
    <link rel="stylesheet" href="/css/anotacao.css">
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
        </div>
      </div>
    <?php endforeach; ?>
  </div>
</div>

<div class="flex column container-geral">
  <div class="flex">
    <div class="container-titulo flex center">
      <h1 class="titulo"><?= h($anotacao->titulo) ?></h1>
    </div>
    <div class="container-btn-fecha">
      <form action="/telas/assunto.php" method="get" class="botao-volta-assunto">
        <input hidden name="getIdAssunto" value="<?= (int) $idAssunto ?>">
        <button class="botao-sair" type="submit"><p class="btn-close"></p></button>
      </form>
    </div>
  </div>

  <form method="post" action="/back-end/update_texto.php" class="editor" id="editor-form">
    <?= csrf_field() ?>
    <div class="editor-toolbar" style="margin-left: 10px;">
      <span id="autosave-status" aria-live="polite" style="font-size:.85em;color:#999;">Salvo</span>
    </div>
    <textarea name="editor" id="editor"><?= h($anotacao->conteudo) ?></textarea>
    <input hidden type="text"   name="pagina"     value="<?= h($paginaContexto) ?>">
    <input hidden type="number" name="idAnotacao" value="<?= (int) $idAnotacao ?>">
  </form>
</div>

<div class="modal fade branco" id="modal">
  <div class="modal-dialog modal-dialog-centered"><div class="modal-content">
    <div class="modal-header">
      <h1 class="modal-title fs-5 titulo">Adicionar Assunto</h1>
      <button class="btn-close" data-bs-dismiss="modal" type="button"></button>
    </div>
    <form action="/back-end/cadastro_assunto.php" method="post">
      <?= csrf_field() ?>
      <div class="modal-body">
        <input class="nome-assunto"      maxlength="52"  name="titulo" type="text" placeholder="Titulo" required>
        <input class="descricao-assunto" maxlength="300" name="resumo" type="text" placeholder="Descricao">
        <input hidden type="text" name="pagina" value="<?= h($paginaContexto) ?>">
      </div>
      <div class="modal-footer">
        <button name="cadastrar" type="submit" class="botao-concluir">Concluir</button>
      </div>
    </form>
  </div></div>
</div>

<div class="modal fade branco" id="modalUpdateSenha">
  <div class="modal-dialog modal-dialog-centered"><div class="modal-content">
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
        <input hidden type="text" name="pagina" value="<?= h($paginaContexto) ?>">
        <div class="modal-footer">
          <button type="submit" class="botao-concluir">Alterar</button>
        </div>
      </form>
    </div>
  </div></div>
</div>

<script type="text/javascript" src="/plugin/tinymce/js/tinymce/tinymce.min.js"></script>
<script type="text/javascript" src="/plugin/tinymce/js/tinymce/init-tinymce.js"></script>
<script src="/js/bootstrap.bundle.min.js"></script>
<script src="/js/bootstrap.min.js"></script>
<script>
  function filtrar() {
    var input = document.querySelector('#inputDeSearch').value.toLowerCase();
    document.querySelectorAll('#listaDeAssuntos [data-titulo]').forEach(function (div) {
      var titulo = div.getAttribute('data-titulo') || '';
      div.style.display = titulo.indexOf(input) > -1 ? 'flex' : 'none';
    });
  }

  var modalConfiguracoes = document.querySelector('#config');
  var botaoAbreConfig    = document.querySelector('#abreModalConfig');

  function mostra(modal) {
    var el = document.querySelector('#' + modal);
    if (el) el.style.display = "flex";
  }
  function fecharModal(modal) {
    var el = document.querySelector('#' + modal);
    if (el) el.style.display = "none";
  }

  window.addEventListener('click', function (event) {
    if (!modalConfiguracoes.contains(event.target)) fecharModal('config');
  });
  botaoAbreConfig.addEventListener('click', function (event) {
    event.stopPropagation();
    modalConfiguracoes.style.display = "flex";
  });

  // ===== Autosave =====
  // Salva o conteudo automaticamente alguns segundos depois que o usuario
  // para de digitar. Nao substitui o botao Salvar, so elimina a obrigacao
  // de clicar nele.
  (function () {
    var FORM     = document.getElementById('editor-form');
    var STATUS   = document.getElementById('autosave-status');
    var BTN      = document.getElementById('btn-salvar');
    var ENDPOINT = '/back-end/save_anotacao.php';
    var DEBOUNCE = 1500;
    var MAX_WAIT = 15000;
    var timer    = null;
    var lastSave = Date.now();
    var inFlight = false;
    var dirty    = false;

    function setStatus(text, color) {
      if (!STATUS) return;
      STATUS.textContent = text;
      STATUS.style.color = color || '';
    }

    function getContent() {
      if (window.tinymce && window.tinymce.activeEditor) {
        return window.tinymce.activeEditor.getContent();
      }
      var ta = document.getElementById('editor');
      return ta ? ta.value : '';
    }

    function nowHHMMSS() {
      var d = new Date();
      var pad = function (n) { return n < 10 ? '0' + n : '' + n; };
      return pad(d.getHours()) + ':' + pad(d.getMinutes()) + ':' + pad(d.getSeconds());
    }

    function save() {
      if (inFlight) { dirty = true; return; }
      inFlight = true;
      dirty    = false;
      setStatus('Salvando...', '#999');

      var body = new URLSearchParams();
      body.append('_csrf',      FORM.querySelector('[name="_csrf"]').value);
      body.append('idAnotacao', FORM.querySelector('[name="idAnotacao"]').value);
      body.append('editor',     getContent());

      fetch(ENDPOINT, {
        method: 'POST',
        credentials: 'same-origin',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: body.toString()
      })
        .then(function (res) {
          return res.json().then(function (data) { return { status: res.status, data: data }; });
        })
        .then(function (r) {
          if (r.status === 200 && r.data && r.data.ok) {
            lastSave = Date.now();
            setStatus('Salvo as ' + nowHHMMSS(), '#3a3');
          } else if (r.status === 401) {
            setStatus('Sessao expirou - recarregue a pagina', '#c66');
          } else if (r.status === 403) {
            setStatus('Token invalido - recarregue a pagina', '#c66');
          } else {
            setStatus('Erro ao salvar [HTTP ' + r.status + ']', '#c66');
          }
        })
        .catch(function () {
          setStatus('Sem conexao - vou tentar de novo', '#c66');
        })
        .then(function () {
          inFlight = false;
          if (dirty) save();
        });
    }

    function schedule() {
      setStatus('Editando...', '#999');
      if (Date.now() - lastSave > MAX_WAIT) {
        if (timer) clearTimeout(timer);
        save();
        return;
      }
      if (timer) clearTimeout(timer);
      timer = setTimeout(save, DEBOUNCE);
    }

    if (window.tinymce) {
      window.tinymce.on('AddEditor', function (e) {
        e.editor.on('Input Change KeyUp Undo Redo Paste', schedule);
        e.editor.on('Init', function () { setStatus('Pronto', ''); });
      });
    }

    var ta = document.getElementById('editor');
    if (ta) {
      ta.addEventListener('input', schedule);
    }

    if (BTN) {
      BTN.addEventListener('click', function (ev) {
        ev.preventDefault();
        if (timer) clearTimeout(timer);
        save();
      });
    }

    window.addEventListener('beforeunload', function (e) {
      if (timer || inFlight || dirty) {
        e.preventDefault();
        e.returnValue = '';
      }
    });
  })();
</script>
</body>
</html>
