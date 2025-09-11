<?php

declare(strict_types=1);

namespace Lacus\CpfGen\Tests;

use function Lacus\CpfGen\cpf_gen;

class CpfGeneratorFunctionTest extends CpfGeneratorTestCase
{
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
