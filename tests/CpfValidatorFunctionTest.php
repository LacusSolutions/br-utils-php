<?php

declare(strict_types=1);

namespace Lacus\CpfUtils\Tests;

use Lacus\CpfVal\Tests\CpfValidatorTestCases;
use PHPUnit\Framework\TestCase;

use function Lacus\CpfUtils\cpf_val;

class CpfValidatorFunctionTest extends TestCase
{
    use CpfValidatorTestCases;

    protected function isValid(string $cpfString): bool
    {
        return cpf_val($cpfString);
    }
}
