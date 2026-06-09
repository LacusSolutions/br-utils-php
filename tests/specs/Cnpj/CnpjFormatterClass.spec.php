<?php

declare(strict_types=1);

namespace Lacus\BrUtils\Tests\Cnpj;

use Closure;
use Lacus\BrUtils\Cnpj\CnpjFormatter;
use Lacus\CnpjFmt\Tests\CnpjFormatterTestCases;
use PHPUnit\Framework\TestCase;

class CnpjFormatterClassTest extends TestCase
{
    use CnpjFormatterTestCases;

    private CnpjFormatter $formatter;

    protected function setUp(): void
    {
        $this->formatter = new CnpjFormatter();
    }

    protected function format(
        string $cnpjString,
        ?bool $escape = null,
        ?bool $hidden = null,
        ?string $hiddenKey = null,
        ?int $hiddenStart = null,
        ?int $hiddenEnd = null,
        ?string $dotKey = null,
        ?string $slashKey = null,
        ?string $dashKey = null,
        ?Closure $onFail = null,
    ): string {
        return $this->formatter->format(
            $cnpjString,
            $escape,
            $hidden,
            $hiddenKey,
            $hiddenStart,
            $hiddenEnd,
            $dotKey,
            $slashKey,
            $dashKey,
            $onFail,
        );
    }
}
