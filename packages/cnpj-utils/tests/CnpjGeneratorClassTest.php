<?php

declare(strict_types=1);

namespace Lacus\CnpjUtils\Tests;

use Lacus\CnpjGen\Tests\CnpjGeneratorTestCases;
use Lacus\CnpjUtils\CnpjGenerator;
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
