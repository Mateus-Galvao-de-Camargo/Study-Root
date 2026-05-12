<?php

declare(strict_types=1);

namespace StudyRoot\Tests;

use PHPUnit\Framework\TestCase;

require_once __DIR__ . '/../back-end/lib/helpers.php';

final class HelpersTest extends TestCase
{
    public function testHEscapesHtmlSpecialCharacters(): void
    {
        $this->assertSame('&lt;script&gt;', h('<script>'));
        $this->assertSame('&quot;teste&quot;', h('"teste"'));
        $this->assertSame('&#039;teste&#039;', h("'teste'"));
        $this->assertSame('a &amp; b', h('a & b'));
    }

    public function testHHandlesNullAndEmpty(): void
    {
        $this->assertSame('', h(null));
        $this->assertSame('', h(''));
    }

    public function testHCastsScalars(): void
    {
        $this->assertSame('42', h(42));
        $this->assertSame('1', h(true));
    }

    public function testIsNonEmptyStringRejectsWhitespaceOnly(): void
    {
        $this->assertTrue(is_non_empty_string('a'));
        $this->assertTrue(is_non_empty_string(' a '));
        $this->assertFalse(is_non_empty_string(''));
        $this->assertFalse(is_non_empty_string('   '));
        $this->assertFalse(is_non_empty_string("\t\n"));
        $this->assertFalse(is_non_empty_string(null));
        $this->assertFalse(is_non_empty_string(0));
    }

    public function testNormalizeSpacesCollapsesAndTrims(): void
    {
        $this->assertSame('hello world', normalize_spaces('hello   world'));
        $this->assertSame('hello world', normalize_spaces("  hello\tworld  "));
        $this->assertSame('hello world', normalize_spaces("hello\nworld"));
        $this->assertSame('', normalize_spaces('   '));
        $this->assertSame('hello', normalize_spaces('hello'));
    }
}
