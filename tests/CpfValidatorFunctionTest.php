<?php

declare(strict_types=1);

namespace Lacus\CpfUtils\Tests;

use function Lacus\CpfUtils\cpf_val;

use Lacus\CpfVal\Tests\CpfValidatorTestCases;
use PHPUnit\Framework\TestCase;

class CpfValidatorFunctionTest extends TestCase
{
    use CpfValidatorTestCases;

    protected function isValid(string $cpfString): bool
    {
        return cpf_val($cpfString);
    }
}
