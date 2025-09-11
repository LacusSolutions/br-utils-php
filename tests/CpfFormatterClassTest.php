<?php

declare(strict_types=1);

namespace Lacus\CpfFmt\Tests;

use Lacus\CpfFmt\CpfFormatter;
use Lacus\CpfFmt\CpfFormatterOptions;

class CpfFormatterClassTest extends CpfFormatterTestCase
{
    private CpfFormatter $formatter;

    protected function setUp(): void
    {
        $this->formatter = new CpfFormatter();
    }

    protected function format(
        string $cpfString,
        ?bool $escape = null,
        ?bool $hidden = null,
        ?string $hiddenKey = null,
        ?int $hiddenStart = null,
        ?int $hiddenEnd = null,
        ?string $dotKey = null,
        ?string $dashKey = null,
        ?callable $onFail = null,
    ): string {
        return $this->formatter->format(
            $cpfString,
            $escape,
            $hidden,
            $hiddenKey,
            $hiddenStart,
            $hiddenEnd,
            $dotKey,
            $dashKey,
            $onFail,
        );
    }

    public function testObjectOrientedGetOptions(): void
    {
        $options = $this->formatter->getOptions();

        $this->assertInstanceOf(CpfFormatterOptions::class, $options);
        $this->assertFalse($options->isEscaped());
        $this->assertFalse($options->isHidden());
        $this->assertEquals('*', $options->getHiddenKey());
        $this->assertEquals(3, $options->getHiddenStart());
        $this->assertEquals(10, $options->getHiddenEnd());
        $this->assertEquals('.', $options->getDotKey());
        $this->assertEquals('-', $options->getDashKey());
    }
}
