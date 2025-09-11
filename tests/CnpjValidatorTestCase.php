<?php

declare(strict_types=1);

namespace Lacus\CnpjVal\Tests;

use PHPUnit\Framework\TestCase;

abstract class CnpjValidatorTestCase extends TestCase
{
    abstract protected function isValid(string $cnpjString): bool;

    public function testCnpjWithDotsAndDashFormatsToSameFormat(): void
    {
        $result = $this->isValid('foo');

        $this->assertTrue($result);
    }
}
