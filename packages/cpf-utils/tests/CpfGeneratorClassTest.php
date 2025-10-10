<?php

declare(strict_types=1);

namespace Lacus\CpfUtils\Tests;

use Lacus\CpfGen\Tests\CpfGeneratorTestCases;
use Lacus\CpfUtils\CpfGenerator;
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
}
