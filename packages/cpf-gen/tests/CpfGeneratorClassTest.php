<?php

declare(strict_types=1);

namespace Lacus\CpfGen\Tests;

use Lacus\CpfGen\CpfGenerator;
use Lacus\CpfGen\CpfGeneratorOptions;
use PHPUnit\Framework\TestCase;

class CpfGeneratorClassTest extends TestCase
{
    use CpfGeneratorTestCases;

    private CpfGenerator $generator;

    protected function setUp(): void
    {
        $this->generator = new CpfGenerator();
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

        $this->assertInstanceOf(CpfGeneratorOptions::class, $options);
        $this->assertFalse($options->isFormatting());
        $this->assertEquals('', $options->getPrefix());
    }
}
