<?php

declare(strict_types=1);

namespace Lacus\BrUtils\Tests\Cnpj;

use Lacus\BrUtils\Cnpj\CnpjGenerator;
use Lacus\CnpjGen\Tests\CnpjGeneratorTestCases;
use PHPUnit\Framework\TestCase;

class CnpjGeneratorClassTest extends TestCase
{
    use CnpjGeneratorTestCases;

    private CnpjGenerator $generator;

    protected function setUp(): void
    {
        $this->generator = new CnpjGenerator();
    }

    protected function generate(
        ?bool $format = null,
        ?string $prefix = null,
    ): string {
        return $this->generator->generate(
            $format,
            $prefix,
        );
    }
}
