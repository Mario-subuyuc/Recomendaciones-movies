<?php

namespace Tests\Unit;

use App\Services\ContadorPalabras;
use PHPUnit\Framework\TestCase;

class ContadorPalabrasTest extends TestCase
{
    public function test_unicode_whitespace_and_empty_elements(): void
    {
        $counter = new ContadorPalabras;
        $this->assertSame(0, $counter->contar(" \n\t"));
        $this->assertSame(4, $counter->contar("  Hola\tárbol\n你好\u{00A0}mundo!  "));
        $this->assertSame(1, $counter->contar('8.5'));
    }
}
