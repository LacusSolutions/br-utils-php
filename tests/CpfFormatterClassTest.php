<?php

declare(strict_types=1);

namespace Lacus\CpfUtils\Tests;

use Closure;
use Lacus\CpfFmt\Tests\CpfFormatterTestCases;
use Lacus\CpfUtils\CpfFormatter;
use PHPUnit\Framework\TestCase;

class CpfFormatterClassTest extends TestCase
{
    use CpfFormatterTestCases;

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
        ?Closure $onFail = null,
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
}
