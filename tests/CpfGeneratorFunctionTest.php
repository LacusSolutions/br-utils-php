<?php

declare(strict_types=1);

namespace Lacus\CpfGen\Tests;

use PHPUnit\Framework\TestCase;

use function Lacus\CpfGen\cpf_gen;

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
