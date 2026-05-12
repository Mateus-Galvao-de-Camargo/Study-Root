<?php
/**
 * Sessão, autenticação e CSRF.
 */

declare(strict_types=1);

if (!function_exists('study_root_session_start')) {

    function study_root_session_start(): void
    {
        if (session_status() === PHP_SESSION_ACTIVE) {
            return;
        }

        // Detecta HTTPS atrás de proxy (Render usa X-Forwarded-Proto)
        $isHttps =
            (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ||
            (($_SERVER['HTTP_X_FORWARDED_PROTO'] ?? '') === 'https');

        session_set_cookie_params([
            'lifetime' => 0,
            'path'     => '/',
            'domain'   => '',
            'secure'   => $isHttps,
            'httponly' => true,
            'samesite' => 'Lax',
        ]);

        session_name('STUDYROOTSID');
        session_start();
    }

    /**
     * Garante que o usuário está autenticado. Redireciona para o login se não.
     * Importante: usa exit; — diferente do código antigo que continuava executando.
     */
    function require_auth(): int
    {
        study_root_session_start();
        if (empty($_SESSION['user_id'])) {
            header('Location: /index.php');
            exit;
        }
        return (int) $_SESSION['user_id'];
    }

    /**
     * Retorna o id do usuário logado ou null.
     */
    function current_user_id(): ?int
    {
        study_root_session_start();
        return isset($_SESSION['user_id']) ? (int) $_SESSION['user_id'] : null;
    }

    /**
     * Marca o usuário como autenticado. Regenera o id da sessão (anti-fixation).
     */
    function log_in_as(int $userId): void
    {
        study_root_session_start();
        session_regenerate_id(true);
        $_SESSION['user_id']   = $userId;
        $_SESSION['logged_at'] = time();
    }

    function log_out(): void
    {
        study_root_session_start();
        $_SESSION = [];
        if (ini_get('session.use_cookies')) {
            $params = session_get_cookie_params();
            setcookie(
                session_name(),
                '',
                time() - 42000,
                $params['path'],
                $params['domain'],
                $params['secure'],
                $params['httponly']
            );
        }
        session_destroy();
    }

    /**
     * Retorna o token CSRF da sessão atual, criando se ainda não existe.
     */
    function csrf_token(): string
    {
        study_root_session_start();
        if (empty($_SESSION['csrf'])) {
            $_SESSION['csrf'] = bin2hex(random_bytes(32));
        }
        return $_SESSION['csrf'];
    }

    /**
     * Imprime o hidden input para usar dentro de <form>.
     */
    function csrf_field(): string
    {
        $t = htmlspecialchars(csrf_token(), ENT_QUOTES, 'UTF-8');
        return '<input type="hidden" name="_csrf" value="' . $t . '">';
    }

    /**
     * Compara dois tokens CSRF em tempo constante. Função pura, sem efeitos
     * colaterais — usada por require_csrf() e testável diretamente.
     */
    function csrf_check(?string $sent, ?string $expected): bool
    {
        if (!is_string($sent) || $sent === '' || !is_string($expected) || $expected === '') {
            return false;
        }
        return hash_equals($expected, $sent);
    }

    /**
     * Verifica o token CSRF recebido. Aborta com 403 se inválido.
     */
    function require_csrf(): void
    {
        study_root_session_start();
        $sent     = $_POST['_csrf'] ?? $_GET['_csrf'] ?? '';
        $expected = $_SESSION['csrf'] ?? '';
        if (!csrf_check(is_string($sent) ? $sent : null, is_string($expected) ? $expected : null)) {
            http_response_code(403);
            exit('CSRF inválido. Recarregue a página e tente novamente.');
        }
    }
}
