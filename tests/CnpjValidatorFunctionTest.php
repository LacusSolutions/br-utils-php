<?php

declare(strict_types=1);

namespace Lacus\CnpjVal\Tests;

use function Lacus\CnpjVal\cnpj_val;

use PHPUnit\Framework\TestCase;

class CnpjValidatorFunctionTest extends TestCase
{
    use CnpjValidatorTestCases;

    protected function isValid(string $cnpjString): bool
    {
        return cnpj_val($cnpjString);
    }
}
