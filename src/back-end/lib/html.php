<?php
/**
 * Sanitização de HTML para anotações (TinyMCE).
 *
 * O TinyMCE produz HTML que é armazenado bruto no banco e depois enviado
 * de volta para o textarea. Sem sanitização isso é XSS persistente caso
 * alguém cole tags <script> / atributos onerror / etc.
 *
 * Usamos HTMLPurifier com uma config conservadora que permite só os elementos
 * e atributos que um editor de anotações precisa.
 */

declare(strict_types=1);

if (!function_exists('study_root_purify_html')) {

    function study_root_purify_html(string $html): string
    {
        static $purifier = null;

        if ($purifier === null) {
            $autoload = __DIR__ . '/../../vendor/autoload.php';
            if (is_file($autoload)) {
                require_once $autoload;
            }
            if (!class_exists(\HTMLPurifier_Config::class)) {
                // Sem o purifier instalado, falha explícita em vez de XSS silenciosa.
                throw new RuntimeException(
                    'HTMLPurifier não está instalado. Rode `composer install` em src/.'
                );
            }

            $config = HTMLPurifier_Config::createDefault();
            $config->set('HTML.Doctype', 'HTML 4.01 Transitional');

            // Lista branca de tags e atributos comuns de editor rich-text.
            $config->set('HTML.AllowedElements', [
                'p', 'br', 'hr',
                'strong', 'em', 'u', 's', 'sub', 'sup', 'mark',
                'h1', 'h2', 'h3', 'h4', 'h5', 'h6',
                'ul', 'ol', 'li',
                'blockquote', 'pre', 'code',
                'a', 'span', 'div',
                'table', 'thead', 'tbody', 'tr', 'th', 'td',
                'img',
            ]);
            $config->set('HTML.AllowedAttributes', [
                'a.href', 'a.title', 'a.target',
                'img.src', 'img.alt', 'img.title', 'img.width', 'img.height',
                'span.style', 'div.style', 'p.style',
                'th.colspan', 'td.colspan', 'th.rowspan', 'td.rowspan',
            ]);

            // Links abrem em nova aba com noopener.
            $config->set('HTML.TargetBlank', true);
            $config->set('HTML.Nofollow', true);

            // URLs só http/https/mailto (impede javascript: e data:).
            $config->set('URI.AllowedSchemes', ['http' => true, 'https' => true, 'mailto' => true]);

            // CSS inline limitado a propriedades inofensivas.
            $config->set('CSS.AllowedProperties', [
                'color', 'background-color', 'font-weight', 'font-style',
                'text-align', 'text-decoration', 'font-size', 'padding-left',
            ]);

            // Cache em /tmp (não persistente, mas o purifier é rápido o suficiente).
            $cacheDir = sys_get_temp_dir() . '/htmlpurifier';
            if (!is_dir($cacheDir)) {
                @mkdir($cacheDir, 0o700, true);
            }
            $config->set('Cache.SerializerPath', $cacheDir);

            $purifier = new HTMLPurifier($config);
        }

        return $purifier->purify($html);
    }
}
