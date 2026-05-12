<?php

declare(strict_types=1);

namespace StudyRoot\Tests;

use Bcrypt;
use PHPUnit\Framework\TestCase;

require_once __DIR__ . '/../back-end/bcrypt.php';

final class BcryptTest extends TestCase
{
    public function testHashProducesBcryptString(): void
    {
        $hash = Bcrypt::hash('senha-segura');
        // Bcrypt: 60 chars, prefixo $2y$ (PHP) ou $2a$ (legados aceitos)
        $this->assertSame(60, strlen($hash));
        $this->assertMatchesRegularExpression('/^\$2[ayb]\$/', $hash);
    }

    public function testCheckSucceedsForCorrectPassword(): void
    {
        $hash = Bcrypt::hash('hunter2');
        $this->assertTrue(Bcrypt::check('hunter2', $hash));
    }

    public function testCheckRejectsWrongPassword(): void
    {
        $hash = Bcrypt::hash('certo');
        $this->assertFalse(Bcrypt::check('errado', $hash));
    }

    public function testCheckRejectsTamperedHash(): void
    {
        $hash = Bcrypt::hash('senha');
        $tampered = substr($hash, 0, -1) . 'x';
        $this->assertFalse(Bcrypt::check('senha', $tampered));
    }

    public function testHashesAreDifferentForSameInput(): void
    {
        $h1 = Bcrypt::hash('senha');
        $h2 = Bcrypt::hash('senha');
        $this->assertNotSame($h1, $h2, 'Salts diferentes devem gerar hashes diferentes');
        $this->assertTrue(Bcrypt::check('senha', $h1));
        $this->assertTrue(Bcrypt::check('senha', $h2));
    }

    public function testNeedsRehashDetectsCostMismatch(): void
    {
        // Hash com custo baixo deve precisar de rehash quando exigimos custo alto.
        $hashBaixo = Bcrypt::hash('senha', 4);
        $this->assertTrue(Bcrypt::needsRehash($hashBaixo, 10));
        $this->assertFalse(Bcrypt::needsRehash($hashBaixo, 4));
    }

    public function testLegacyTwoAHashIsStillVerifiable(): void
    {
        // Garante compatibilidade com a versão antiga da classe, que usava $2a$.
        // Hash gerado offline: senha "legado" com custo 10, prefixo $2a$.
        // Geramos um hash com password_hash e trocamos $2y$ por $2a$ para simular.
        $modern = password_hash('legado', PASSWORD_BCRYPT, ['cost' => 10]);
        $legacy = '$2a$' . substr($modern, 4);
        $this->assertTrue(Bcrypt::check('legado', $legacy));
    }
}
