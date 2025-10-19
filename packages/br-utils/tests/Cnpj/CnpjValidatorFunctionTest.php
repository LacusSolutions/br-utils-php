<?php

declare(strict_types=1);

namespace Lacus\BrUtils\Tests\Cnpj;

use function Lacus\BrUtils\Cnpj\cnpj_val;

use Lacus\CnpjVal\Tests\CnpjValidatorTestCases;
use PHPUnit\Framework\TestCase;

class CnpjValidatorFunctionTest extends TestCase
{
    use CnpjValidatorTestCases;

    protected function isValid(string $cnpjString): bool
    {
        return cnpj_val($cnpjString);
    }
}
