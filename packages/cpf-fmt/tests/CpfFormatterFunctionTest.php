<?php

declare(strict_types=1);

namespace Lacus\CpfFmt\Tests;

use PHPUnit\Framework\TestCase;

use function Lacus\CpfFmt\cpf_fmt;

class CpfFormatterFunctionTest extends TestCase
{
    use CpfFormatterTestCases;

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
        return cpf_fmt(
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
