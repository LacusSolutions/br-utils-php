<?php

declare(strict_types=1);

namespace Lacus\CpfFmt\Tests;

use Lacus\CpfFmt\CpfFormatter;
use PHPUnit\Framework\TestCase;

class CpfFormatterTest extends TestCase
{
    private CpfFormatter $formatter;

    protected function setUp(): void
    {
        $this->formatter = new CpfFormatter();
    }

    public function testFormat(): void
    {
        $this->assertEquals('123.456.789-09', $this->formatter->format('12345678909'));
    }

    public function testClean(): void
    {
        $this->assertEquals('12345678909', $this->formatter->clean('123.456.789-09'));
    }

    public function testIsFormatted(): void
    {
        $this->assertTrue($this->formatter->isFormatted('123.456.789-09'));
        $this->assertFalse($this->formatter->isFormatted('12345678909'));
    }

    public function testFormatWithInvalidLength(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->formatter->format('123456789');
    }
}
