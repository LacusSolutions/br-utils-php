<?php

declare(strict_types=1);

namespace Lacus\BrUtils\Tests\Cpf;

use Closure;

use function Lacus\BrUtils\Cpf\cpf_fmt;

use Lacus\CpfFmt\Tests\CpfFormatterTestCases;
use PHPUnit\Framework\TestCase;

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
        ?Closure $onFail = null,
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
