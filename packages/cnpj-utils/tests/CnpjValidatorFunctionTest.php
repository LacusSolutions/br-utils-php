<?php

declare(strict_types=1);

namespace Lacus\CnpjUtils\Tests;

use Lacus\CnpjVal\Tests\CnpjValidatorTestCases;
use PHPUnit\Framework\TestCase;

use function Lacus\CnpjUtils\cnpj_val;

class CnpjValidatorFunctionTest extends TestCase
{
    use CnpjValidatorTestCases;

    protected function isValid(string $cnpjString): bool
    {
        return cnpj_val($cnpjString);
    }
}
