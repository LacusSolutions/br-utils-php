<?php

declare(strict_types=1);

namespace Lacus\CpfGen\Tests;

use PHPUnit\Framework\TestCase;

abstract class CpfGeneratorTestCase extends TestCase
{
    abstract protected function generate(
        ?bool $format = null,
        ?string $prefix = null,
    ): string;

    public function testCpfWithDotsAndDashFormatsToSameFormat(): void
    {
        $cpf = $this->generate();

        $this->assertNotEmpty($cpf);
    }
}
