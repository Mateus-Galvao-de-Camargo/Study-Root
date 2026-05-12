<?php
/**
 * Endpoint JSON usado pelo autosave da tela de anotação.
 *
 * Diferente de update_texto.php (que faz redirect), este responde JSON
 * para ser consumido por fetch() do navegador sem recarregar a página.
 *
 * Respostas:
 *   200 {"ok": true,  "savedAt": <unix>}        sucesso
 *   400 {"ok": false, "error": "invalid_id"}    id inválido
 *   401 {"ok": false, "error": "unauthenticated"} sem sessão
 *   403 {"ok": false, "error": "csrf"}          token inválido
 *   404 {"ok": false, "error": "not_found"}     anotação não é do usuário
 *   413 {"ok": false, "error": "too_large"}    conteúdo > 1MB
 */

declare(strict_types=1);

require_once __DIR__ . '/lib/db.php';
require_once __DIR__ . '/lib/auth.php';
require_once __DIR__ . '/lib/helpers.php';
require_once __DIR__ . '/lib/html.php';

header('Content-Type: application/json; charset=utf-8');
header('Cache-Control: no-store');

function _respond(int $status, array $payload): void
{
    http_response_code($status);
    echo json_encode($payload);
    exit;
}

study_root_session_start();

$estudante = current_user_id();
if ($estudante === null) {
    _respond(401, ['ok' => false, 'error' => 'unauthenticated']);
}

// Aceita o token via POST ou via header (mais cômodo pro fetch).
$sentCsrf     = $_POST['_csrf'] ?? $_SERVER['HTTP_X_CSRF_TOKEN'] ?? '';
$expectedCsrf = $_SESSION['csrf'] ?? '';
if (!csrf_check(is_string($sentCsrf) ? $sentCsrf : null, is_string($expectedCsrf) ? $expectedCsrf : null)) {
    _respond(403, ['ok' => false, 'error' => 'csrf']);
}

$idAnotacao = filter_var($_POST['idAnotacao'] ?? '', FILTER_VALIDATE_INT);
$texto      = (string) ($_POST['editor'] ?? '');

if ($idAnotacao === false || $idAnotacao === null) {
    _respond(400, ['ok' => false, 'error' => 'invalid_id']);
}
if (strlen($texto) > 1_000_000) {
    _respond(413, ['ok' => false, 'error' => 'too_large']);
}

$texto = study_root_purify_html($texto);

$pdo = study_root_db();

$own = $pdo->prepare(
    'SELECT a.id_anotacao
       FROM anotacao a
       JOIN assunto s ON s.id_assunto = a.id_assunto_fk
      WHERE a.id_anotacao = :n AND s.id_estudante_fk = :e
      LIMIT 1'
);
$own->execute([':n' => $idAnotacao, ':e' => $estudante]);
if (!$own->fetch()) {
    _respond(404, ['ok' => false, 'error' => 'not_found']);
}

$upd = $pdo->prepare('UPDATE anotacao SET conteudo = :c WHERE id_anotacao = :n');
$upd->execute([':c' => $texto, ':n' => $idAnotacao]);

_respond(200, ['ok' => true, 'savedAt' => time()]);
