<?php

declare(strict_types=1);

namespace StudyRoot\Tests;

use PHPUnit\Framework\TestCase;

require_once __DIR__ . '/../back-end/lib/helpers.php';

final class SafeRedirectTest extends TestCase
{
    public function testNullFallsBackToHome(): void
    {
        $this->assertSame('/telas/home.php', resolve_safe_destination(null));
    }

    public function testEmptyFallsBackToHome(): void
    {
        $this->assertSame('/telas/home.php', resolve_safe_destination(''));
    }

    public function testAcceptsHome(): void
    {
        $this->assertSame('/telas/home.php', resolve_safe_destination('home.php'));
    }

    public function testAcceptsAssuntoWithQueryString(): void
    {
        $this->assertSame(
            '/telas/assunto.php?getIdAssunto=42',
            resolve_safe_destination('assunto.php?getIdAssunto=42')
        );
    }

    public function testAcceptsLeadingSlash(): void
    {
        $this->assertSame('/telas/home.php', resolve_safe_destination('/home.php'));
    }

    public function testRejectsExternalUrl(): void
    {
        $this->assertSame(
            '/telas/home.php',
            resolve_safe_destination('https://evil.example.com')
        );
        $this->assertSame(
            '/telas/home.php',
            resolve_safe_destination('//evil.example.com')
        );
    }

    public function testRejectsProtocolRelative(): void
    {
        $this->assertSame(
            '/telas/home.php',
            resolve_safe_destination('javascript:alert(1)')
        );
    }

    public function testRejectsParentDirectoryTraversal(): void
    {
        $this->assertSame(
            '/telas/home.php',
            resolve_safe_destination('../etc/passwd')
        );
    }

    public function testRejectsUnknownPage(): void
    {
        $this->assertSame(
            '/telas/home.php',
            resolve_safe_destination('algo_qualquer.php')
        );
    }

    public function testRejectsQueryStringWithBadCharacters(): void
    {
        // O regex só permite [A-Za-z0-9_=&-] depois do ?
        $this->assertSame(
            '/telas/home.php',
            resolve_safe_destination('home.php?foo=<script>')
        );
    }

    public function testCustomFallback(): void
    {
        $this->assertSame(
            '/index.php',
            resolve_safe_destination(null, '/index.php')
        );
    }
}
