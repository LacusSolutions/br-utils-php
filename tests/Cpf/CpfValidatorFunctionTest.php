<?php

declare(strict_types=1);

namespace Lacus\BrUtils\Tests\Cpf;

use function Lacus\BrUtils\Cpf\cpf_val;

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
