<?php

declare(strict_types=1);

namespace Lacus\BrUtils\Tests\Cnpj;

use function Lacus\BrUtils\Cnpj\cnpj_gen;

use Lacus\CnpjGen\Tests\CnpjGeneratorTestCases;
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
