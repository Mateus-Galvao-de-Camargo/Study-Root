<?php

declare(strict_types=1);

require_once __DIR__ . '/lib/db.php';
require_once __DIR__ . '/lib/auth.php';
require_once __DIR__ . '/lib/helpers.php';
require_once __DIR__ . '/lib/html.php';

$estudante = require_auth();
require_csrf();

if (!isset($_POST['salvaTexto'])) {
    safe_redirect('home.php');
}

$idAnotacao = filter_var($_POST['idAnotacao'] ?? '', FILTER_VALIDATE_INT);
$texto      = (string) ($_POST['editor'] ?? '');
$pagina     = $_POST['pagina'] ?? 'home.php';

if ($idAnotacao === false || $idAnotacao === null) {
    safe_redirect('home.php');
}

// Limite defensivo de tamanho (1 MB de HTML é mais que suficiente).
if (strlen($texto) > 1_000_000) {
    alert_and_redirect('Anotação excede o tamanho máximo permitido.', $pagina);
}

// Sanitiza o HTML vindo do TinyMCE — remove scripts, atributos perigosos,
// URLs com esquemas suspeitos (javascript:, data:), etc.
$texto = study_root_purify_html($texto);

$pdo = study_root_db();

// Confirma que a anotação pertence ao estudante.
$own = $pdo->prepare(
    'SELECT a.id_anotacao
       FROM anotacao a
       JOIN assunto s ON s.id_assunto = a.id_assunto_fk
      WHERE a.id_anotacao = :n AND s.id_estudante_fk = :e
      LIMIT 1'
);
$own->execute([':n' => $idAnotacao, ':e' => $estudante]);
if (!$own->fetch()) {
    safe_redirect('home.php');
}

$upd = $pdo->prepare('UPDATE anotacao SET conteudo = :c WHERE id_anotacao = :n');
$upd->execute([':c' => $texto, ':n' => $idAnotacao]);

safe_redirect($pagina);
