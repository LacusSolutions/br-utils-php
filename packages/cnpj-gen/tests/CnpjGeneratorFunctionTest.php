<?php

declare(strict_types=1);

namespace Lacus\CnpjGen\Tests;

use function Lacus\CnpjGen\cnpj_gen;

use PHPUnit\Framework\TestCase;

class CnpjGeneratorFunctionTest extends TestCase
{
    use CnpjGeneratorTestCases;

    protected function generate(
        ?bool $format = null,
        ?string $prefix = null,
    ): string {
        return cnpj_gen(
            $format,
            $prefix,
        );
    }
}
