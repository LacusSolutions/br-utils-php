<?php

declare(strict_types=1);

namespace Lacus\CpfVal\Tests;

use PHPUnit\Framework\TestCase;

abstract class CpfValidatorTestCase extends TestCase
{
    abstract protected function isValid(string $cpfString): bool;

    public function testCpfWithDotsAndDashFormatsToSameFormat(): void
    {
        $result = $this->isValid('foo');

        $this->assertTrue($result);
    }
}
