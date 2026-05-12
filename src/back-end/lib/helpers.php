<?php
/**
 * Helpers gerais: escape, redirect com whitelist, validação.
 */

declare(strict_types=1);

if (!function_exists('h')) {

    /**
     * Escapa para HTML. Usar em TODA saída vinda do usuário ou do banco.
     */
    function h($value): string
    {
        return htmlspecialchars((string) ($value ?? ''), ENT_QUOTES, 'UTF-8');
    }

    /**
     * Aplica a whitelist de destinos e retorna a URL final.
     * Função pura — não chama header() nem exit. Útil para testes.
     */
    function resolve_safe_destination(?string $pagina, string $fallback = '/telas/home.php'): string
    {
        if (!is_string($pagina) || $pagina === '') {
            return $fallback;
        }

        $clean = ltrim($pagina, '/');
        if (preg_match('#^(home\.php|assunto\.php|anotacao\.php|cadastro\.php)(\?[A-Za-z0-9_=&\-]*)?$#', $clean)) {
            return '/telas/' . $clean;
        }

        return $fallback;
    }

    /**
     * Redireciona para uma das telas conhecidas. Ignora qualquer outro destino
     * para evitar open redirect via parâmetros controlados pelo usuário.
     */
    function safe_redirect(?string $pagina, string $fallback = '/telas/home.php'): void
    {
        header('Location: ' . resolve_safe_destination($pagina, $fallback));
        exit;
    }

    /**
     * Mostra um alert + redireciona. Mantém o padrão de UX do código antigo
     * mas sem deixar o PHP continuar executando depois.
     */
    function alert_and_redirect(string $message, ?string $pagina = null, string $fallback = '/telas/home.php'): void
    {
        $msg = htmlspecialchars($message, ENT_QUOTES, 'UTF-8');

        $destino = $fallback;
        if (is_string($pagina) && $pagina !== '') {
            $clean = ltrim($pagina, '/');
            if (preg_match('#^(home\.php|assunto\.php|anotacao\.php|cadastro\.php|../index\.php|index\.php)(\?[A-Za-z0-9_=&\-]*)?$#', $clean)) {
                $destino = (strpos($clean, 'index.php') !== false)
                    ? '/index.php'
                    : '/telas/' . preg_replace('#^\.\./#', '', $clean);
            }
        }

        echo '<!doctype html><meta charset="utf-8"><script>alert("' . $msg . '"); location.href=' . json_encode($destino) . ';</script>';
        exit;
    }

    /**
     * Validação simples: string não vazia depois de trim.
     */
    function is_non_empty_string($value): bool
    {
        return is_string($value) && trim($value) !== '';
    }

    /**
     * Normaliza espaços em branco para um único espaço.
     */
    function normalize_spaces(string $value): string
    {
        return trim(preg_replace('/\s+/', ' ', $value) ?? '');
    }
}
