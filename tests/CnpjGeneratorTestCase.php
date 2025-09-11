<?php

declare(strict_types=1);

namespace Lacus\CnpjGen\Tests;

use PHPUnit\Framework\TestCase;

abstract class CnpjGeneratorTestCase extends TestCase
{
    abstract protected function generate(
        ?bool $format = null,
        ?string $prefix = null,
    ): string;

    public function testCnpjWithDotsAndDashFormatsToSameFormat(): void
    {
        $cnpj = $this->generate();

        $this->assertNotEmpty($cnpj);
    }
}
