<?php

declare(strict_types=1);

namespace Lacus\CnpjVal\Tests;

use function Lacus\CnpjVal\cnpj_val;

class CnpjValidatorFunctionTest extends CnpjValidatorTestCase
{
    protected function isValid(string $cnpjString): bool
    {
        return cnpj_val($cnpjString);
    }
}
