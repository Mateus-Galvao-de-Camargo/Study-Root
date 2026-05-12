<?php

declare(strict_types=1);

namespace StudyRoot\Tests;

use PHPUnit\Framework\TestCase;

require_once __DIR__ . '/../back-end/lib/html.php';

final class HtmlPurifyTest extends TestCase
{
    public function testStripsScriptTags(): void
    {
        $dirty = '<p>oi</p><script>alert(1)</script>';
        $clean = study_root_purify_html($dirty);
        $this->assertStringNotContainsString('<script', $clean);
        $this->assertStringNotContainsString('alert(1)', $clean);
        $this->assertStringContainsString('<p>oi</p>', $clean);
    }

    public function testStripsOnErrorAttribute(): void
    {
        $dirty = '<img src="x" onerror="alert(1)">';
        $clean = study_root_purify_html($dirty);
        $this->assertStringNotContainsString('onerror', $clean);
        $this->assertStringNotContainsString('alert', $clean);
    }

    public function testStripsJavascriptUrl(): void
    {
        $dirty = '<a href="javascript:alert(1)">click</a>';
        $clean = study_root_purify_html($dirty);
        $this->assertStringNotContainsString('javascript:', $clean);
    }

    public function testStripsDataUrlInImage(): void
    {
        $dirty = '<img src="data:text/html,<script>alert(1)</script>">';
        $clean = study_root_purify_html($dirty);
        $this->assertStringNotContainsString('data:', $clean);
        $this->assertStringNotContainsString('<script', $clean);
    }

    public function testPreservesBasicFormatting(): void
    {
        $dirty = '<p><strong>negrito</strong> e <em>itálico</em></p>';
        $clean = study_root_purify_html($dirty);
        $this->assertStringContainsString('<strong>negrito</strong>', $clean);
        $this->assertStringContainsString('<em>itálico</em>', $clean);
    }

    public function testPreservesLinkWithHttp(): void
    {
        $dirty = '<a href="https://example.com">site</a>';
        $clean = study_root_purify_html($dirty);
        $this->assertStringContainsString('href="https://example.com"', $clean);
        $this->assertStringContainsString('>site</a>', $clean);
    }

    public function testAddsRelNoopenerToTargetBlank(): void
    {
        $dirty = '<a href="https://example.com">x</a>';
        $clean = study_root_purify_html($dirty);
        // HTML.TargetBlank deve forçar target=_blank com rel=noopener
        $this->assertStringContainsString('target="_blank"', $clean);
        $this->assertStringContainsString('noopener', $clean);
    }

    public function testStripsStyleTag(): void
    {
        $dirty = '<style>body{display:none}</style><p>oi</p>';
        $clean = study_root_purify_html($dirty);
        $this->assertStringNotContainsString('<style', $clean);
        $this->assertStringContainsString('<p>oi</p>', $clean);
    }

    public function testStripsIframe(): void
    {
        $dirty = '<iframe src="https://evil.com"></iframe><p>oi</p>';
        $clean = study_root_purify_html($dirty);
        $this->assertStringNotContainsString('<iframe', $clean);
        $this->assertStringContainsString('<p>oi</p>', $clean);
    }

    public function testEmptyInputReturnsEmptyString(): void
    {
        $this->assertSame('', study_root_purify_html(''));
    }

    public function testStripsEventHandlersOnAllowedElements(): void
    {
        $dirty = '<p onclick="alert(1)">click</p>';
        $clean = study_root_purify_html($dirty);
        $this->assertStringNotContainsString('onclick', $clean);
        $this->assertStringContainsString('click', $clean);
    }
}
