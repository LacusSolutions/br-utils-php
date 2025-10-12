<?php

declare(strict_types=1);

namespace Lacus\CnpjUtils\Tests;

use Closure;
use Lacus\CnpjUtils\CnpjUtils;
use Lacus\CnpjFmt\Tests\CnpjFormatterTestCases;
use Lacus\CnpjGen\Tests\CnpjGeneratorTestCases;
use Lacus\CnpjVal\Tests\CnpjValidatorTestCases;
use PHPUnit\Framework\TestCase;

class CnpjUtilsTest extends TestCase
{
    use CnpjFormatterTestCases;
    use CnpjGeneratorTestCases;
    use CnpjValidatorTestCases;

    private CnpjUtils $utils;

    protected function setUp(): void
    {
        $this->utils = new CnpjUtils();
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
        return $this->utils->format(
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

    protected function generate(
        ?bool $format = null,
        ?string $prefix = null,
    ): string {
        return $this->utils->generate(
            $format,
            $prefix,
        );
    }

    protected function isValid(string $cnpjString): bool {
        return $this->utils->isValid($cnpjString);
    }
}
