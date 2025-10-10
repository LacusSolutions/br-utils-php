<?php

declare(strict_types=1);

namespace Lacus\CnpjVal\Tests;

use PHPUnit\Framework\TestCase;

use function Lacus\CnpjVal\cnpj_val;

class CnpjValidatorFunctionTest extends TestCase
{
    use CnpjValidatorTestCases;

    protected function isValid(string $cnpjString): bool
    {
        return cnpj_val($cnpjString);
    }
}
