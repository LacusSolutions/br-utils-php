<?php

declare(strict_types=1);

namespace Lacus\CpfUtils\Tests;

use Closure;
use Lacus\CpfFmt\Tests\CpfFormatterTestCases;
use Lacus\CpfGen\Tests\CpfGeneratorTestCases;
use Lacus\CpfUtils\CpfUtils;
use Lacus\CpfVal\Tests\CpfValidatorTestCases;
use PHPUnit\Framework\TestCase;

class CpfUtilsTest extends TestCase
{
    use CpfFormatterTestCases;
    use CpfGeneratorTestCases;
    use CpfValidatorTestCases;

    private CpfUtils $utils;

    protected function setUp(): void
    {
        $this->utils = new CpfUtils();
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
        return $this->utils->format(
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

    protected function generate(
        ?bool $format = null,
        ?string $prefix = null,
    ): string {
        return $this->utils->generate(
            $format,
            $prefix,
        );
    }

    protected function isValid(string $cpfString): bool
    {
        return $this->utils->isValid($cpfString);
    }
}
