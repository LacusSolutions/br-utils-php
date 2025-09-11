<?php

declare(strict_types=1);

namespace Lacus\CpfVal\Tests;

use Lacus\CpfVal\CpfValidator;
use Lacus\CpfVal\CpfValidatorOptions;

class CpfValidatorClassTest extends CpfValidatorTestCase
{
    private CpfValidator $validator;

    protected function setUp(): void
    {
        $this->validator = new CpfValidator();
    }

    protected function isValid(string $cpfString): bool
    {
        return $this->validator->isValid($cpfString);
    }
}
