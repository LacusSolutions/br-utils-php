<?php

declare(strict_types=1);

namespace Lacus\CpfVal\Tests;

use PHPUnit\Framework\TestCase;

use function Lacus\CpfVal\cpf_val;

class CpfValidatorFunctionTest extends TestCase
{
    use CpfValidatorTestCases;

    protected function isValid(string $cpfString): bool
    {
        return cpf_val($cpfString);
    }
}
