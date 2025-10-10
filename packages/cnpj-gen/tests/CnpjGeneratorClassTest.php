<?php

declare(strict_types=1);

namespace Lacus\CnpjGen\Tests;

use Lacus\CnpjGen\CnpjGenerator;
use Lacus\CnpjGen\CnpjGeneratorOptions;
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

    public function testObjectOrientedGetOptions(): void
    {
        $options = $this->generator->getOptions();

        $this->assertInstanceOf(CnpjGeneratorOptions::class, $options);
        $this->assertFalse($options->isFormatting());
        $this->assertEquals('', $options->getPrefix());
    }
}
