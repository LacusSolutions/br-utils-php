<?php

declare(strict_types=1);

namespace Lacus\CnpjVal\Tests;

use Lacus\CnpjVal\CnpjValidator;
use Lacus\CnpjVal\CnpjValidatorOptions;

class CnpjValidatorClassTest extends CnpjValidatorTestCase
{
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
