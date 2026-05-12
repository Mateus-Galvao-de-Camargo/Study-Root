<?php

declare(strict_types=1);

namespace StudyRoot\Tests;

use PHPUnit\Framework\TestCase;

require_once __DIR__ . '/../back-end/lib/auth.php';

final class AuthTest extends TestCase
{
    public function testCsrfCheckAcceptsMatchingTokens(): void
    {
        $token = bin2hex(random_bytes(16));
        $this->assertTrue(csrf_check($token, $token));
    }

    public function testCsrfCheckRejectsMismatch(): void
    {
        $this->assertFalse(csrf_check('aaaa', 'bbbb'));
    }

    public function testCsrfCheckRejectsEmptyOrNull(): void
    {
        $this->assertFalse(csrf_check('', 'something'));
        $this->assertFalse(csrf_check('something', ''));
        $this->assertFalse(csrf_check(null, 'something'));
        $this->assertFalse(csrf_check('something', null));
        $this->assertFalse(csrf_check(null, null));
        $this->assertFalse(csrf_check('', ''));
    }

    public function testCsrfCheckIsCaseSensitive(): void
    {
        $this->assertFalse(csrf_check('AbCdEf', 'abcdef'));
    }

    public function testCsrfCheckRejectsPrefixOnly(): void
    {
        // Garante que não está comparando por substring.
        $expected = 'abcdef123456';
        $this->assertFalse(csrf_check('abcdef', $expected));
        $this->assertFalse(csrf_check($expected . 'extra', $expected));
    }
}
