<?php

declare(strict_types=1);

namespace Lacus\CpfVal\Tests;

use function Lacus\CpfVal\cpf_val;

class CpfValidatorFunctionTest extends CpfValidatorTestCase
{
    protected function isValid(string $cpfString): bool
    {
        return cpf_val($cpfString);
    }
}
