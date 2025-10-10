<?php

declare(strict_types=1);

namespace Lacus\CnpjVal\Tests;

use Lacus\CnpjVal\CnpjValidator;
use PHPUnit\Framework\TestCase;

class CnpjValidatorClassTest extends TestCase
{
    use CnpjValidatorTestCases;

    private CnpjValidator $validator;

    protected function setUp(): void
    {
        $this->validator = new CnpjValidator();
    }

    protected function isValid(string $cnpjString): bool
    {
        return $this->validator->isValid($cnpjString);
    }
}
