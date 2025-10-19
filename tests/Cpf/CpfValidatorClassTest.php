<?php

declare(strict_types=1);

namespace Lacus\BrUtils\Tests\Cpf;

use Lacus\BrUtils\Cpf\CpfValidator;
use Lacus\CpfVal\Tests\CpfValidatorTestCases;
use PHPUnit\Framework\TestCase;

class CpfValidatorClassTest extends TestCase
{
    use CpfValidatorTestCases;

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
