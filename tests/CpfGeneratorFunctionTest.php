<?php

declare(strict_types=1);

namespace Lacus\CpfUtils\Tests;

use Lacus\CpfGen\Tests\CpfGeneratorTestCases;
use PHPUnit\Framework\TestCase;

use function Lacus\CpfUtils\cpf_gen;

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
