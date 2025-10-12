<?php

declare(strict_types=1);

namespace Lacus\CpfGen\Tests;

use function Lacus\CpfGen\cpf_gen;

use PHPUnit\Framework\TestCase;

class CpfGeneratorFunctionTest extends TestCase
{
    use CpfGeneratorTestCases;

    protected function generate(
        ?bool $format = null,
        ?string $prefix = null,
    ): string {
        return cpf_gen(
            $format,
            $prefix,
        );
    }
}
